@foreach ($posts as $post)

    <div class="col-12 col-lg-4 mb-4 px-3 news-post">
		<div class="news-post-container">
    		@include('frontend.blog.partials.post', compact('post'))
    	</div>
	</div>

@endforeach
