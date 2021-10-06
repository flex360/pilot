@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Event Manager')

@section('buttons')
    <div id="action-btn-container">
        @if (\PilotSetting::has('events') && isset(config('settings')['events']))
            <a href="{{ route('admin.setting.default', ['setting'=>'events']) }}" class="btn btn-warning btn-sm"><i class="fas fa-cogs"></i> Settings</a>
        @endif
        <a href="{{ route('calendar') }}" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#tags-modal"><i class="fa fa-tags"></i> Manage Tags</button>
        <a href="{{ route('admin.event.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Events</a>
    </div>
@endsection

@section('panel-body-class')

@section('panel-body')

    <form id="search-events-form" action="" method="get" class="form-inline">
        <div class="form-group">
            <label for="" class="search-label">Search</label>
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search by Title" value="{{ request()->input('keyword') }}">
                <span class="input-group-append">
                    @if (request()->has('keyword'))
                        <a href="/pilot/event" class="btn btn-default">Clear</a>
                    @endif
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                </span>
            </div>
        </div>
    </form>

    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="index-nav-tabs">
          <li class="nav-item">
            <a class="nav-link {{ $view == 'all' ? 'active' : '' }}" href="{{ route('admin.event.all') }}">All</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'published' ? 'active' : '' }}" href="/pilot/event">Published</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'scheduled' ? 'active' : '' }}" href="{{ route('admin.event.scheduled') }}">Scheduled</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'drafts' ? 'active' : '' }}" href="{{ route('admin.event.draft') }}">Drafts <span class="badge badge-pill badge-danger">{{ $draftsCount }}</span></a>
          </li>
          <li class="nav-item">
              <a class="nav-link {{ $view == 'past' ? 'active' : '' }}" href="{{ route('admin.event.past') }}" role="tab">Past Events  <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
              title="Includes all post so long as their 'End Date/Time' is in the past (drafts or published)."></i></a>
          </li>
       </ul>
   </div>

    @if ($events->isEmpty())

        @if ($view == 'all')
        <p style="margin-top: 0px; padding: 15px">You have no events right now! <a href="{{ route('admin.event.create') }}">Add a event here.</a></p>
        @endif

        @if ($view == 'published')
        <p style="margin-top: 0px; padding: 15px">You have no published events right now! <a href="{{ route('admin.event.create') }}">Add a event here.</a></p>
        @endif

        @if ($view == 'scheduled')
            <p style="margin-top: 0px; padding: 15px">A scheduled event is one that is set to published, but the "Publish Date" field is set some time in the future.
                                 You have no scheduled events right now! <a href="{{ route('admin.event.create') }}">Add a event here.</a></p>
        @endif

        @if ($view == 'drafts')
        <p style="margin-top: 0px; padding: 15px">You have no drafted events right now! <a href="{{ route('admin.event.create') }}">Add a event here.</a></p>
        @endif

        @if ($view == 'past')
        <p style="margin-top: 0px; padding: 15px">You have no past events right now! <a href="{{ route('admin.event.create') }}">Add a event here.</a></p>
        @endif

    @else

        <div id="event-manager-container" class="table-responsive">

                <table class="module-index table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Tags</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->tags->implode('name', ', ') }}</td>
                            <td>{{ $event->start->format('n/j/Y g:i a') }}</td>
                            <td>{{ $event->end->format('n/j/Y g:i a') }}</td>
                            <td>{{ $event->getStatus()->name }}</td>
                            <td>
                                {!! link_to_route('admin.event.edit', 'Edit', $event->id) !!}
                                 |
                                <a href="{{ route('admin.event.copy', $event->id) }}">Copy</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

        </div> <!--event-manager-container -->

    @endif

        {!! $events->appends(request()->all())->links() !!}

@endsection

@section('table')

@include('pilot::admin.posts.tags', compact('tags'))

@endsection

@section('more')

@endsection
