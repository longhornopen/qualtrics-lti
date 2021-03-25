@extends('layouts.app')

@section('content')

    <h2>Testing your survey</h2>

    <p><a href="/app"><<< back to editing survey</a></p>

    <p>
        We're about to test your survey.  You'll be given a chance to take your survey as your students would see it.
        Afterwards, you'll be brought back here and be shown the score you got.  You can repeat this test as many times
        as you like.
    </p>

    <hr>
    <p>
        {!! $assignment->intro_text !!}
    </p>

    <p><a class="btn btn-primary" href="{{$full_qualtrics_url}}">Take your survey</a></p>

    <br><br>
@endsection
