<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LonghornOpen\LaravelCelticLTI\LtiTool;
use Ramsey\Uuid\Uuid;

class LtiController extends Controller
{
    public function ltiMessage(Request $request)
    {
        $tool = LtiTool::getLtiTool();
        $tool->handleRequest();

        $session_data = [];

        // $tool contains information about the launch - which LMS, course, placement, and user this corresponds to.
        // Store these in your database or session, as appropriate for your app.
        if ($tool->getLaunchType() === $tool::LAUNCH_TYPE_LAUNCH) {
            $isTeacher = $tool->userResult->isAdmin() || $tool->userResult->isStaff();

            $session_data['lti_user_result_dbid'] = $tool->userResult->getRecordId();
            $session_data['lti_user_name'] = $tool->userResult->fullname;
            $session_data['lti_user_email'] = $tool->userResult->email;
            $session_data['lti_resource_link_dbid'] = $tool->resourceLink->getRecordId();
            $session_data['lti_context_dbid'] = $tool->context->getRecordId();
            $session_data['lti_person_sourcedid'] = $tool->userResult->sourcedId;
            $session_data['lti_is_teacher'] = $isTeacher;
        }

        $uuid = Uuid::uuid4();
        $request->session()->put('uuid-'.$uuid, $session_data);
        session(['lti_session_exists' => true]);

        return redirect('/app/'.$uuid->toString());
    }

    public function getJWKS()
    {
        $tool = LtiTool::getLtiTool();
        return $tool->getJWKS();
    }

    public function ltiHelp()
    {
        return view('lti_help');
    }
}
