@extends('layouts.internal')

@section('content')

    <div class="module">

        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="{{ route('calendar') }}">Upcoming</a></li>
            <li><a href="{{ route('calendar.month') }}">Calendar</a></li>
        </ul>

        @foreach ($events as $event)

            @include('pilot::frontend.calendar.partials.event', compact('event'))

        @endforeach

    </div>

@stop
