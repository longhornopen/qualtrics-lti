@extends('layouts.app')

@section('content')

    <br><br>

    @if ($assignment_response->score==null)

        {!! $assignment->intro_text !!}

        <p><a class="btn btn-primary" href="{{$full_qualtrics_url}}">Take your survey</a></p>

    @else

        {!! $assignment->finish_text !!}

    @endif

    <br><br>
@endsection
