@extends('layouts.internal')

@section('content')

    <div class="module">

        <ul class="nav nav-tabs" role="tablist">
            <li><a href="{{ route('calendar') }}">Upcoming</a></li>
            <li class="active"><a href="{{ route('calendar.month') }}">Calendar</a></li>
        </ul>

        <div id="fullCalendar" style="margin-top: 20px;"></div>

    </div>

@stop
