@extends('layouts.app')

@section('head_extras')
<script src="/build/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('content')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.baseURL = "/build/tinymce";
            tinymce.init({
                selector: '#intro_text',
                height: 200,
                plugins: [
                    'advlist','autolink','lists','link','image','charmap','anchor',
                    'searchreplace','visualblocks',
                    'media','table'
                ],
                toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                branding: false,
                promotion: false
            });
            tinymce.init({
                selector: '#finish_text',
                height: 200,
                plugins: [
                    'advlist','autolink','lists','link','image','charmap','anchor',
                    'searchreplace','visualblocks',
                    'media','table'
                ],
                toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                branding: false,
                promotion: false
            });
        });
    </script>

    <br><br>

    <h2>Configuration</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success_msg'))
        <div class="alert alert-success">{{session('success_msg')}}</div>
    @endif

    <div class="row">
        <div class="col-12" style="margin-bottom: 1rem;">
            <ul>
                <li>If you haven't already, <a href="/" target="_blank" class="body_link">set up your survey according to the instructions.</a></li>
                <li>{!! __('messages.additional_instructions', ['url' => e('https://www.qualtrics.com/support/survey-platform/survey-module/survey-tools/check-survey-accessibility/#QuestionTypeAccessibility')]) !!}</li>
                <li>Once you've set up your survey, test it using the 'Test Survey' link below.</li>
            </ul>
        </div>
        <div class="col-12" style="margin-bottom: 1rem;">
            <hr>
            <form method="post" action="/app/{{$uuid}}/config">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label for="qualtrics_url" style="font-size:1.35rem;">Qualtrics Survey URL</label>
                    <div class="form-text">In Qualtrics, this is under the 'Distributions' menu item.  Click 'Distribute Survey', then 'Web', then 'Use Anonymous Link'.</div>
                    <input type="text" class="form-control" id="qualtrics_url" name="qualtrics_url" value="{{ old('qualtrics_url', $assignment->qualtrics_url) }}">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="intro_text" style="font-size:1.35rem;">Intro text</label>
                    <div class="form-text">The text your students will see at the beginning of the survey.</div>
                    <textarea class="form-control" id="intro_text" name="intro_text">{{ old('intro_text', $assignment->intro_text) }}</textarea>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="finish_text" style="font-size:1.35rem;">Finished text</label>
                    <div class="form-text">The text your students will see at the end of the survey.</div>
                    <textarea class="form-control tinymce" id="finish_text" name="finish_text">{{ old('finish_text', $assignment->finish_text) }}</textarea>
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="font-size:1.35rem;">Options</div>
                    <div style="margin-left:1em;">
                        <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            > share student data with Qualtrics
                        </a>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body">
                                <p>You can choose to share the following student data with Qualtrics so that you can capture it as part of your survey.  See <a href="/docs/student_data_capture" style="text-decoration:underline" target="_blank">the documentation</a> to learn how to set up the survey to do that.</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="share_data[]" value="user_email"
                                           @if ($assignment->shouldSendPersonalData('user_email'))
                                               checked
                                           @endif
                                           id="share_data_user_email">
                                    <label class="form-check-label" for="share_data_user_email">
                                        user_email (example: {{$session_data['lti_user_email']??'unknown'}})
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="share_data[]" value="sis_user_id"
                                           @if ($assignment->shouldSendPersonalData('sis_user_id'))
                                               checked
                                           @endif
                                           id="share_data_sis_user_id">
                                    <label class="form-check-label" for="share_data_sis_user_id">
                                        sis_user_id (example: {{$session_data['lti_person_sourcedid']??'unknown'}})
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="share_data[]" value="user_id"
                                           @if ($assignment->shouldSendPersonalData('user_id'))
                                               checked
                                           @endif
                                           id="share_data_user_id">
                                    <label class="form-check-label" for="share_data_user_id">
                                        user_id (example: {{$session_data['lti_user_id']??'unknown'}})
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Configuration</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12" style="margin-top:2em;">
            <hr>
            <h3>Test Survey</h3>
            <p>
                After saving the configuration above, <a href="/app/{{$uuid}}/test_begin" class="body_link">test your survey</a> and make sure it's set up correctly.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-12" style="margin-top:2em;">
            <hr>

            <h3>Responses</h3>

            <table class="table table-striped">
                <tr>
                    <th>Student</th>
                    <th>Score</th>
                    <th>Grade reported</th>
                    <th></th>
                </tr>
                @foreach ($assignment_responses as $resp)
                    <tr>
                        <td>{{$resp->getPersonIdentity()}}</td>
                        <td>{{$resp->score}}</td>
                        <td>@if ($resp->date_outcome_reported)
                                <span title="{{$resp->date_outcome_reported->toRfc7231String()}}">
                                    {{$resp->date_outcome_reported->diffForHumans()}}
                                </span>
                            @endif</td>
                        <td>
                            @if (!$resp->date_outcome_reported && $resp->score!==null) <form method="post" action="/app/{{$uuid}}/resend_grade">
                                {{ @csrf_field() }}
                                <input type="hidden" name="response_id" value="{{$resp->id}}">
                                <button type="submit" class="btn btn-secondary">Update grade</button>
                            </form>@endif
                        </td>
                    </tr>
                @endforeach
            </table>

            <a class="btn btn-primary" role="button" href="/app/{{$uuid}}/exportCSV">Download CSV</a>

        </div>
    </div>

    <br><br>
@endsection
