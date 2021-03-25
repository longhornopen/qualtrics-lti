@extends('layouts.app')

@section('content')

    <h2>Testing your survey</h2>

    <p><a href="/app"><<< back to editing survey</a></p>

    <p>
        You've successfully completed your test survey.  Here's the scoring information that I'd be recording
        if this were a real student:
    </p>

    <table class="table table-bordered">
        <tr>
            <th>Score</th>
            <th>Max Score</th>
        </tr>
        <tr>
            <td>{{$request->filled('Score') ? $request->get('Score') : 'MISSING'}}</td>
            <td>{{$request->filled('MaxScore') ? $request->get('MaxScore') : 'MISSING'}}</td>
        </tr>
    </table>

    <p>
        There should be numbers for both 'Score' and 'MaxScore' above.  If not, check that you've followed
        the <a href="/" target="_blank">'How do I grade a Qualtrics survey?' instructions</a>.
    </p>

    <hr>
    <p>
        {!! $assignment->finish_text !!}
    </p>

    <br><br>
@endsection
