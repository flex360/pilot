@extends('layouts.internal')

@section('content')

    @foreach ($events as $event)

        @include('frontend.calendar.partials.event', compact('event'))

    @endforeach

    {{ $events->render() }}

@stop
