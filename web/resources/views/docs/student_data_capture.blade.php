@extends('layouts.app')

@section('content')

    <div class="row" style="margin-bottom:1em;margin-top:1em;">
    <h2>Capturing student data in a Qualtrics survey</h2>
    </div>

    <p>If you wish, you can configure this tool to pass several pieces of a student's personal information to Qualtrics,
    where they can be captured.  You can choose any of the following:</p>
    <table class="table table-bordered">
        <tr><th>Type</th><th>Field Name</th><th>What is it?</th></tr>
        <tr><td>Email</td><td>user_email</td><td>The student's email address.</td></tr>
        <tr><td>SIS ID</td><td>sis_user_id</td><td>The student ID in your Student Information System.  (This will vary by institution.)</td></tr>
        <tr><td>LTI ID</td><td>user_id</td><td>An internal tracking number assigned by this tool.  (You can download a list of these from the 'Download CSV' button in the tool.)</td></tr>
    </table>

    <p>Full documentation including screenshots showing how to set this up is at Part 2 ("Capturing Information from a URL as Embedded Data") of
    <a style="text-decoration:underline;" href="https://www.qualtrics.com/support/survey-platform/survey-module/survey-flow/standard-elements/passing-information-through-query-strings/#PassingInformationIntoASurvey" target="_blank">this page of the Qualtrics documentation</a>.
    The short version of that documentation is:
    </p>
    <ol>
        <li>Navigate to your survey's Survey Flow.</li>
        <li>Create a new Embedded Data at the top of the Survey Flow.</li>
        <li>Give it the 'Field Name' (above) of the field you want to capture.  Capitalization and spelling must exactly match the Field Names above.</li>
    </ol>

    <p>Once you've set up this tool to send the field, and set up Qualtrics to receive it, it should get stored as part of the Qualtrics survey automatically.</p>

@endsection
