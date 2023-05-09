@extends('layouts.app')

@section('content')

    <h2>Testing your survey</h2>

    <p><a href="/app" class="body_link"><<< back to editing survey</a></p>

    <p>
        We're about to test your survey.  You'll be given a chance to take your survey as your students would see it.
    </p>

    <p>
        You'll know everything is set up correctly when your survey sends you back to this page and you see the
        <span style="background-color: lightgreen;border:solid 1px green;border-radius:5px;padding:5px;">✔️OK!</span> mark.
    </p>

    <p>
        You can repeat this test as many times as you like.
    </p>

    <hr>
    <p>
        {!! $assignment->intro_text !!}
    </p>

    <p><a class="btn btn-primary" href="{{$full_qualtrics_url}}">Take your survey</a></p>

    <br><br>
@endsection
