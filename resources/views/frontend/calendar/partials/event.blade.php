<h3>{{ $event->link() }}</h3>

<p>{{ $event->present()->getDateString() }}</p>

<p>
    <a href="{{ $event->url() }}" class="btn btn-default btn-xs">Event Details <i class="fa fa-arrow-right"></i></a>
</p>