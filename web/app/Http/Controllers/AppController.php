<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentResponse;
use ceLTIc\LTI;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LonghornOpen\LaravelCelticLTI\LtiTool;

class AppController extends Controller
{
    public function getTool(Request $request) {
        $assignment = Assignment::firstOrCreate([
            'resource_link_dbid' => $request->session()->get('lti_resource_link_dbid'),
        ], [
            'qualtrics_url' => "",
            'intro_text' => "Welcome!  Please click below to begin your survey.",
            'finish_text' => "Thank you for taking this survey!  Your response has been recorded.",
        ]);

        if ($request->session()->get('lti_is_teacher')) {
            return view('tool_teacher', [
                'assignment' => $assignment,
                'assignment_responses' => AssignmentResponse::where('assignment_id',$assignment->id)->get()
            ]);
        }

        $assignment_response = AssignmentResponse::firstOrCreate([
            'assignment_id' => $assignment->id,
            'user_result_id' => $request->session()->get('lti_user_result_dbid'),
            'user_name' => $request->session()->get('lti_user_name'),
            'user_email' => $request->session()->get('lti_user_email'),
        ], [

        ]);

        $sep = "&";
        if (strpos($assignment->qualtrics_url, "?")===FALSE) {
            $sep = "?";
        }
        $full_qualtrics_url = $assignment->qualtrics_url . $sep . "return_url=" . urlencode($request->url() . "/response");
        $share_user = $request->session()->has('lti_person_sourcedid');
        if ($share_user) {
            $full_qualtrics_url .= "&sis_user_id=" . $request->session()->get('lti_person_sourcedid');
        }

        return view('tool_student', [
            'assignment' => $assignment,
            'assignment_response' => $assignment_response,
            'full_qualtrics_url' => $full_qualtrics_url
        ]);
    }

    public function getToolResponse(Request $request) {
        if (!$request->has('Score')) {
            return "ERROR: 'Score' parameter is missing on return URL.";
        }
        if (!$request->has('MaxScore')) {
            return "ERROR: 'MaxScore' parameter is missing on return URL.";
        }

        $grade = (float)$request->get('Score') / (float)$request->get('MaxScore');
        $assignment = Assignment::where('resource_link_dbid', $request->session()->get('lti_resource_link_dbid'))
            ->firstOrFail();
        $assignment_response = AssignmentResponse::where('assignment_id',$assignment->id)
            ->where('user_result_id', $request->session()->get('lti_user_result_dbid'))
            ->firstOrFail();

        $assignment_response->score = $grade;
        $assignment_response->save();

        $lti_tool = LtiTool::getLtiTool();
        $resourceLink = $lti_tool->getResourceLinkById($request->session()->get('lti_resource_link_dbid'));
        if ($resourceLink->hasOutcomesService()) {
            $user_result = $lti_tool->getUserResultById($request->session()->get('lti_user_result_dbid'));
            $outcome = new LTI\Outcome($grade);
            $ok = $resourceLink->doOutcomesService(LTI\ResourceLink::EXT_WRITE, $outcome, $user_result);
            if (!$ok) {
                return "<html><body><h3>ERROR: Unable to save grade.  Please notify your instructor.</h3></body></html>";
            }
            $assignment_response->date_outcome_reported = new Carbon();
            $assignment_response->save();
        }

        return redirect('/app');
    }

    public function postToolConfig(Request $request) {
        if (!$request->session()->get('lti_is_teacher')) {
            abort(403);
        }

        $request->validate([
            'qualtrics_url' => 'required|url'
        ]);

        $assignment = Assignment::where('resource_link_dbid', $request->session()->get('lti_resource_link_dbid'))
            ->firstOrFail();
        $assignment->qualtrics_url = $request->get('qualtrics_url');
        $assignment->intro_text = $request->get('intro_text');
        $assignment->finish_text = $request->get('finish_text');
        $assignment->save();

        return redirect('/app')->with('success_msg', "Successfully updated.");
    }

    public function postResendGrade(Request $request)
    {
        if (!$request->session()->get('lti_is_teacher')) {
            abort(403);
        }

        $assignment_response = AssignmentResponse::findOrFail($request->get('response_id'));

        $grade = $assignment_response->score;
        $lti_tool = LtiTool::getLtiTool();
        $resourceLink = $lti_tool->getResourceLinkById($request->session()->get('lti_resource_link_dbid'));
        if ($resourceLink->hasOutcomesService()) {
            $user_result = $lti_tool->getUserResultById($assignment_response->user_result_id);
            $outcome = new LTI\Outcome($grade);
            $ok = $resourceLink->doOutcomesService(LTI\ResourceLink::EXT_WRITE, $outcome, $user_result);
            if (!$ok) {
                return "<html><body><h3>ERROR: Unable to save grade.  Please try again later, or notify your administrator.</h3></body></html>";
            }
        }

        $assignment_response->date_outcome_reported = new Carbon();
        $assignment_response->save();

        return redirect('/app')->with('success_msg', "Grade successfully updated.");
    }

    public function getTestBegin(Request $request) {
        $assignment = Assignment::where('resource_link_dbid', $request->session()->get('lti_resource_link_dbid'))
            ->firstOrFail();
        $sep = "&";
        if (strpos($assignment->qualtrics_url, "?")===FALSE) {
            $sep = "?";
        }
        $response_url = str_replace('/test_begin', '/test_end', $request->url());
        $full_qualtrics_url = $assignment->qualtrics_url . $sep . "return_url=" . urlencode($response_url);

        return view('tool_test_begin', [
            'assignment' => $assignment,
            'full_qualtrics_url' => $full_qualtrics_url
        ]);
    }

    public function getTestEnd(Request $request) {
        $assignment = Assignment::where('resource_link_dbid', $request->session()->get('lti_resource_link_dbid'))
            ->firstOrFail();

        return view('tool_test_end', [
            'assignment' => $assignment,
            'request' => $request,
            'score' => $request->get('Score'),
            'max_score' => $request->get('MaxScore')
        ]);
    }
}
