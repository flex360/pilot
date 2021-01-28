@extends('layouts.internal')

@section('content')

	<h1 class="page-title">{{ $page->title }}</h1>
    
    <div id="posts" class="row">
        
        @foreach ($posts as $post)
	    	<div class="col-12 col-lg-4 mb-4 px-3 news-post">
	    		<div class="news-post-container">
	        		@include('pilot::frontend.blog.partials.post', compact('post'))
	        	</div>
	    	</div>
	    @endforeach

	</div>

	{{-- <div class="row">
            <span class="mr-auto ml-auto" id="loadNewsSpan"><button class="btn btn-primary" style="display: flex;" id="loadMoreNewsBtn" data-page="1" onclick='LoadNextPageOfPost()'>Load More Posts</button>
            <i style="display:none;" class="mr-auto ml-auto fa-2x fas fa-spinner fa-pulse"></i></span>
    </div> --}}

    {!! $posts->render() !!}

@endsection