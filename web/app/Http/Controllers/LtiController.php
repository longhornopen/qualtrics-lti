<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            $roles = implode(',', $tool->userResult->roles);
            // LTI 1.3 roles...
            $isTeacher = str_contains($roles, 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor')
               || str_contains($roles, 'http://purl.imsglobal.org/vocab/lis/v2/membership#Administrator')
               || str_contains($roles, 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator')
               || str_contains($roles, 'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper')
            // or LTI 1.2 roles...
               || str_contains($roles, 'urn:lti:role:ims/lis/Instructor')
               || str_contains($roles, 'urn:lti:role:ims/lis/Administrator')
               || str_contains($roles, 'urn:lti:instrole:ims/lis/Administrator')
               || str_contains($roles, 'urn:lti:role:ims/lis/ContentDeveloper');

            $session_data['lti_session_exists'] = true;
            $session_data['lti_user_result_dbid'] = $tool->userResult->getRecordId();
            $session_data['lti_user_name'] = $tool->userResult->fullname;
            $session_data['lti_user_email'] = $tool->userResult->email;
            $session_data['lti_resource_link_dbid'] = $tool->resourceLink->getRecordId();
            $session_data['lti_context_dbid'] = $tool->context->getRecordId();
            $session_data['lti_person_sourcedid'] = $tool->userResult->sourcedId;
            $session_data['lti_is_teacher'] = $isTeacher;
        }

        $request->session()->put($session_data);
        $uuid = Uuid::uuid4();
        Cache::put('sess'.$uuid, $request->session()->getId(), 300);

        return redirect('/lti_check?id='.$uuid);
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

    /**
     * Handle being loaded in an iframe, which some browsers won't store a cookie for
     * by opening a new window outside of the iframe where the session actually works.
     */
    // FIXME: Are launchCheck() and launchRedirect() still needed with newer versions of Celtic-LTI?
    //        Seems to be being handled there now.
    public function launchCheck(Request $request)
    {
        if ($request->session()->get('lti_session_exists')) {
            return redirect('/app');
        }

        $id = $request->get('id');
        return <<<TAG
<html><head>
<script>
function deactivate() {
   document.getElementById('link_div').style.display = 'none';
   document.getElementById('message_div').style.display = 'block';
}
setTimeout(deactivate, 4 * 60 * 1000);
</script>
</head><body>
<div id="link_div" style='text-align:center;font-family:sans-serif;font-size:200%;'>
<a href="/lti_redirect?id=$id" target="_blank">Click here</a> to load this tool.
</div>
<div id='message_div' style='text-align:center;font-family:sans-serif;font-size:200%;display:none;'>
Please reload this page in your LMS to launch this tool.
</div>
</body></html>
TAG;
    }

    public function launchRedirect(Request $request)
    {
        $uuid = $request->get('id');
        $session_id = Cache::get('sess'.$uuid);
        $request->session()->setId($session_id);
        $request->session()->start();
        return redirect('/app');
    }
}
