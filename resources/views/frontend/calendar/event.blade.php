@extends('layouts.internal')

@section('content')

    <div class="event-detail">

        <h1>{{ $event->title }}</h1>

        <p>{{ $event->present()->getDateString() }}</p>

        <div class="event-body">

            @if ($event->hasImage())

                <img src="{{ $event->image }}" class="event-image">

            @endif

        </div>

        {{ $event->body }}

        @if ($event->hasGallery())

            <div class="event-slider">

                <ul>

                    @foreach ($event->gallery as $image)

                        <li style="background-image: url('{{ $image['path'] }}');">

                            <h3>{{ $image['title'] or '' }}</h3>

                            <p>{{ $image['caption'] or '' }}</p>

                        </li>

                    @endforeach

                </ul>

            </div>

        @endif

    </div>

    <div class="event-footer">
        <a href="javascript:history.go(-1);" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
    </div>

@stop
