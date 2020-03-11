<?php $detail = isset($detail) ? $detail : false; ?>

<h1>{!! $detail ? $post->title : $post->link() !!}</h1>

<div class="post-byline">
    {{ $post->published_on->format('l, F j, Y g:i a') }}
</div>

<div class="post-body">

    @if ($post->hasImage())

        <img src="{{ $post->image }}" class="post-image">

    @endif

    {!! $post->body !!}

</div>

@if ($post->hasGallery())

    <div class="blog-slider">

        <ul>

            @foreach ($post->gallery as $image)

                <li style="background-image: url('{{ $image['path'] }}');">

                    <h3>{{ $image['title'] or '' }}</h3>

                    <p>{{ $image['caption'] or '' }}</p>

                </li>

            @endforeach

        </ul>

    </div>

@endif

<div class="post-tags">
    Tags: {!! $post->present()->getTagLinks() !!}
</div>

@if ($detail)

    <div class="post-footer">

        <a href="{{ route('blog') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to News</a>

    </div>

@endif
