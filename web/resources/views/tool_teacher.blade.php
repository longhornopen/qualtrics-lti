@extends('layouts.app')

@section('content')

    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '#intro_text',
                height: 200,
                plugins: [
                    'advlist autolink lists link image charmap anchor',
                    'searchreplace visualblocks',
                    'media table paste'
                ],
                toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                branding: false
            });
            tinymce.init({
                selector: '#finish_text',
                height: 200,
                plugins: [
                    'advlist autolink lists link image charmap anchor',
                    'searchreplace visualblocks',
                    'media table paste'
                ],
                toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                branding: false
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
        <div class="col-md-12">
            If you haven't already, <a href="/" target="_blank">set up your survey according to the instructions.</a>
        </div>
        <div class="col-md-12">
            <form method="post" action="/app/config">
                {{@csrf_field()}}
                <div class="form-group">
                    <label for="qualtrics_url">Qualtrics Survey URL</label>
                    <small class="form-text text-muted">In Qualtrics, this is under the 'Distributions' menu item.  Click 'Distribute Survey', then 'Web', then 'Use Anonymous Link'.</small>
                    <input type="text" class="form-control" id="qualtrics_url" name="qualtrics_url" value="{{$assignment->qualtrics_url}}">
                </div>
                <div class="form-group">
                    <label for="intro_text">Intro text</label>
                    <small class="form-text text-muted">The text your students will see at the beginning of the survey.</small>
                    <textarea class="form-control" id="intro_text" name="intro_text">{{$assignment->intro_text}}</textarea>
                </div>
                <div class="form-group">
                    <label for="finish_text">Finished text</label>
                    <small class="form-text text-muted">The text your students will see at the end of the survey.</small>
                    <textarea class="form-control tinymce" id="finish_text" name="finish_text">{{$assignment->finish_text}}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <hr>

    <h3>Test Survey</h3>
    <p>
        After saving the configuration above, <a href="/app/test_begin">test your survey</a> and make sure it's set up correctly.
    </p>

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
                    @if (!$resp->date_outcome_reported && $resp->score!==null) <form method="post" action="/app/resend_grade">
                        {{ @csrf_field() }}
                        <input type="hidden" name="response_id" value="{{$resp->id}}">
                        <button type="submit" class="btn btn-secondary">Update grade</button>
                    </form>@endif
                </td>   
            </tr>
        @endforeach
    </table>

    <button id="CSV-btn" class="btn btn-primary">Download CSV</button>
    <script type= "text/javascript">
        document.getElementById("CSV-btn").onclick = function(){
            location.href = "/exportCSV";
        }
   </script>

    <br><br>
@endsection
