<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentResponse;
use ceLTIc\LTI;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use LonghornOpen\LaravelCelticLTI\LtiTool;
use League\Csv\Writer;
use Ramsey\Uuid\Uuid;
use SplTempFileObject;


class AppController extends Controller
{
    public function getTool($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        $assignment = Assignment::firstOrCreate([
            'resource_link_dbid' => $session_data['lti_resource_link_dbid'],
        ], [
            'qualtrics_url' => "",
            'intro_text' => "Welcome!  Please click below to begin your survey.",
            'finish_text' => "Thank you for taking this survey!  Your response has been recorded.",
        ]);

        if ($session_data['lti_is_teacher']) {
            return view('tool_teacher', [
                'assignment' => $assignment,
                'assignment_responses' => AssignmentResponse::where('assignment_id', $assignment->id)->get(),
                'uuid' => $uuid,
            ]);
        }

        $assignment_response = AssignmentResponse::firstOrCreate([
            'assignment_id' => $assignment->id,
            'user_result_id' => $session_data['lti_user_result_dbid'],
            'user_name' => $session_data['lti_user_name'],
            'user_email' => $session_data['lti_user_email'],
        ], [

        ]);

        $sep = "&";
        if (!str_contains($assignment->qualtrics_url, "?")) {
            $sep = "?";
        }
        $full_qualtrics_url = $assignment->qualtrics_url . $sep . "return_url=" . urlencode(request()->url() . "/response");
        if (array_key_exists('lti_person_sourcedid', $session_data)) {
            $full_qualtrics_url .= "&sis_user_id=" . $session_data['lti_person_sourcedid'];
        }

        # Add user email to URL
        if (array_key_exists('lti_user_email', $session_data)) {
            $full_qualtrics_url .= "&user_email=" . urlencode($session_data['lti_user_email']);
        }

        return view('tool_student', [
            'assignment' => $assignment,
            'assignment_response' => $assignment_response,
            'full_qualtrics_url' => $full_qualtrics_url,
        ]);
    }

    public function getToolResponse($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        if (!request()->has('Score')) {
            return view('error', ['message'=>"ERROR: 'Score' parameter is missing on return URL."]);
        }
        if (!request()->has('MaxScore')) {
            return view('error', ['message'=>"ERROR: 'MaxScore' parameter is missing on return URL."]);
        }

        $grade = (float)request()->get('Score') / (float)request()->get('MaxScore');
        $grade = min(1, max(0, $grade)); // constrain grades to range [0,1], as LTI requires
        $assignment = Assignment::where('resource_link_dbid', $session_data['lti_resource_link_dbid'])
            ->firstOrFail();
        $assignment_response = AssignmentResponse::where('assignment_id', $assignment->id)
            ->where('user_result_id', $session_data['lti_user_result_dbid'])
            ->firstOrFail();

        $assignment_response->score = $grade;
        $assignment_response->save();

        $lti_tool = LtiTool::getLtiTool();
        $resourceLink = $lti_tool->getResourceLinkById($session_data['lti_resource_link_dbid']);
        if ($resourceLink->hasOutcomesService()) {
            $user_result = $lti_tool->getUserResultById($session_data['lti_user_result_dbid']);
            $outcome = new LTI\Outcome($grade);
            $ok = $resourceLink->doOutcomesService(LTI\Enum\ServiceAction::Write, $outcome, $user_result);
            if (!$ok) {
                return view('error', ['message'=>"ERROR: Unable to save grade.  Please try again later, or notify your instructor."]);
            }
            $assignment_response->date_outcome_reported = new Carbon();
            $assignment_response->save();
        }

        return redirect('/app/'.$uuid);
    }

    public function postToolConfig($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        if (!$session_data['lti_is_teacher']) {
            abort(403);
        }

        request()->validate([
            'qualtrics_url' => 'required|url'
        ]);

        $assignment = Assignment::where('resource_link_dbid', $session_data['lti_resource_link_dbid'])
            ->firstOrFail();
        $assignment->qualtrics_url = request()->get('qualtrics_url');
        $assignment->intro_text = request()->get('intro_text');
        $assignment->finish_text = request()->get('finish_text');
        $assignment->save();

        return redirect('/app/'.$uuid)->with('success_msg', "Successfully updated.");
    }

    public function postResendGrade($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        if (!$session_data['lti_is_teacher']) {
            abort(403);
        }

        $assignment_response = AssignmentResponse::findOrFail(request()->get('response_id'));

        $grade = $assignment_response->score;
        $grade = min(1, max(0, $grade)); // constrain grades to range [0,1], as LTI requires
        $lti_tool = LtiTool::getLtiTool();
        $resourceLink = $lti_tool->getResourceLinkById($session_data['lti_resource_link_dbid']);
        if ($resourceLink->hasOutcomesService()) {
            $user_result = $lti_tool->getUserResultById($assignment_response->user_result_id);
            $outcome = new LTI\Outcome($grade);
            $ok = $resourceLink->doOutcomesService(LTI\Enum\ServiceAction::Write, $outcome, $user_result);
            if (!$ok) {
                return view('error', ['message'=>"ERROR: Unable to save grade.  Please try again later, or notify your administrator."]);
            }
        }

        $assignment_response->date_outcome_reported = new Carbon();
        $assignment_response->save();

        return redirect('/app/'.$uuid)->with('success_msg', "Grade successfully updated.");
    }

    public function getCsvExport($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        $assignment = Assignment::where('resource_link_dbid', $session_data['lti_resource_link_dbid'])
            ->firstOrFail();

        set_time_limit(60);
        ini_set('memory_limit', '2048M');
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $assignment_responses = AssignmentResponse::where('assignment_id', $assignment->id)
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['gradeReported', 'score', 'userName', 'userEmail']);
        foreach ($assignment_responses as $assignment_response) {
            $csv->insertOne([$assignment_response->date_outcome_reported, $assignment_response->score, $assignment_response->user_name, $assignment_response->user_email]);
        }

        $file_name1 = $assignment->id;
        $csv->output('assignment_' . $file_name1 . '_grades.csv');
        die;
    }

    public function getTestBegin($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        $assignment = Assignment::where('resource_link_dbid', $session_data['lti_resource_link_dbid'])
            ->firstOrFail();
        $sep = "&";
        if (!str_contains($assignment->qualtrics_url, "?")) {
            $sep = "?";
        }
        $response_url = str_replace('/test_begin', '/test_end', request()->url());
        $full_qualtrics_url = $assignment->qualtrics_url . $sep . "return_url=" . urlencode($response_url);

        return view('tool_test_begin', [
            'assignment' => $assignment,
            'full_qualtrics_url' => $full_qualtrics_url,
            'uuid' => $uuid,
        ]);
    }

    public function getTestEnd($uuid)
    {
        $session_data = session('uuid-' . $uuid);
        if (!$session_data) {
            return view('error', ['message'=>"Sorry, your session has expired.  Please relaunch this tool through your LMS."]);
        }

        $assignment = Assignment::where('resource_link_dbid', $session_data['lti_resource_link_dbid'])
            ->firstOrFail();

        return view('tool_test_end', [
            'assignment' => $assignment,
            'request' => request(),
            'score' => request()->get('Score'),
            'max_score' => request()->get('MaxScore'),
            'uuid' => $uuid,
        ]);
    }

    // Requires DEV_MODE_ENABLE env flag, per routes/web.php
    public function getDevModeLaunch(Request $request)
    {
        return view('dev/launch');
    }

    // Requires DEV_MODE_ENABLE env flag, per routes/web.php
    public function postDevModeLaunch(Request $request)
    {
        $session_data = [];
        $session_data['lti_is_teacher'] = $request->get('is_teacher');
        $session_data['lti_resource_link_dbid'] = $request->get('resource_link_dbid');

        // FIXME: support student test launches later
        //        $session_data['lti_user_result_dbid');
        //        $session_data['lti_user_name');
        //        $session_data['lti_user_email');

        $uuid = Uuid::uuid4();
        $request->session()->put('uuid-'.$uuid, $session_data);
        session(['lti_session_exists' => true]);

        return redirect('/app/'.$uuid->toString());
    }

}
