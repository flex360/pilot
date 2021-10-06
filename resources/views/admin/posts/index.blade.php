@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'News Manager')

@section('buttons')
    <div id="action-btn-container">
        @if (PilotSetting::has('news') && isset(config('settings')['news']))
            <a href="{{ route('admin.setting.default', ['setting'=>'news']) }}" class="btn btn-warning btn-sm"><i class="fas fa-cogs"></i> Settings</a>
        @endif
        <a href="{{ route('blog') }}" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#tags-modal"><i class="fa fa-tags"></i> Manage Tags</button>
        <a href="{{ route('admin.post.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add News</a>
    </div>
@endsection

@section('panel-body')

    <form id="search-events-form" action="" method="get" class="form-inline">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search by Title" value="{{ request()->input('keyword') }}">
                    <span class="input-group-append">
                        @if (request()->has('keyword'))
                            <a href="/pilot/post" class="btn btn-default">Clear</a>
                        @endif
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                    </span>
                </div>
            </div>
    </form>

    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="index-nav-tabs">
            <li class="nav-item">
              <a class="nav-link {{ $view == 'all' ? 'active' : '' }}" href="{{ route('admin.post.all') }}">All</a>
            </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'published' ? 'active' : '' }}" href="/pilot/post">Published</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'scheduled' ? 'active' : '' }}" href="{{ route('admin.post.scheduled') }}">Scheduled</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'drafts' ? 'active' : '' }}" href="{{ route('admin.post.draft') }}">Drafts <span class="badge badge-pill badge-danger">{{ $draftsCount }}</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $view == 'sticky' ? 'active' : '' }}" href="{{ route('admin.post.sticky') }}">Sticky</a>
          </li>
       </ul>
   </div>

        @if ($posts->isEmpty())

            @if ($view == 'all')
            <p style="margin-top: 0px; padding: 15px">You have no posts right now! <a href="{{ route('admin.post.create') }}">Add a post here.</a></p>
            @endif

            @if ($view == 'published')
            <p style="margin-top: 0px; padding: 15px">You have no published posts right now! <a href="{{ route('admin.post.create') }}">Add a post here.</a></p>
            @endif

            @if ($view == 'scheduled')
                <p style="margin-top: 0px; padding: 15px">A scheduled post is one that is set to published, but the "Publish Date" field is set some time in the future.
                                     You have no scheduled posts right now! <a href="{{ route('admin.post.create') }}">Add a post here.</a></p>
            @endif

            @if ($view == 'drafts')
            <p style="margin-top: 0px; padding: 15px">You have no drafted posts right now! <a href="{{ route('admin.post.create') }}">Add a post here.</a></p>
            @endif

            @if ($view == 'sticky')
            <p style="margin-top: 0px; padding: 15px">You have no sticky posts right now! You can mark a post sticky by checking the Sticky Post box while editing a Post</p>
            @endif

        @else

            <div id="event-manager-container" class="table-responsive">

                <table class="module-index table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Summary</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody class="dynamo-index-body">
                        @foreach ($posts as $post)
                        <tr class="dynamo-index-row">
                            <td>{{ $post->published_on->format('n/j/Y g:i a') }}</td>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->getBackendSummary() }}</td>
                            <td>{{ $post->getStatus()->name }}</td>
                            <td class="dynamo-width-of-action-row">
                                <div style="display: flex; gap: 5px;">
                                    <a href="{{ route('admin.post.edit', $post->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="{{ route('admin.post.copy', $post->id) }}" class="btn btn-secondary btn-sm">Copy</a>
                                    <a href="/pilot/post/{{ $post->id }}/delete" onclick="return confirm('Are you sure you want to delete this? This action cannot be undone and will be deleted forever. ( FLEX360 can bring it back for you )')" class="btn btn-secondary btn-sm">Delete</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div> <!--event-manager-container -->
        @endif

    {!! $posts->appends(request()->all())->links() !!}

@include('pilot::admin.posts.tags', compact('tags'))

@endsection
