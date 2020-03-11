@extends('layouts.internal')

@section('metadata')
    <link rel="canonical" href="<?php echo $post->url(); ?>" />

    <!-- <meta property="fb:app_id" content="1506914376207450" /> -->
    <meta property="og:title" content="{{ htmlspecialchars($post->title) }}" />
    @if ($post->hasImage())
        <meta property="og:image" content="{{ $post->getSocialImage() }}" />
        <meta property="og:image:width" content="{{ $post->getSocialImageWidth() }}" />
        <meta property="og:image:height" content="{{ $post->getSocialImageHeight() }}" />
    @else
        <meta property="og:image" content="{{ config('app.social_image') }}" />
        <meta property="og:image:width" content="{{ config('app.social_image_width') }}" />
        <meta property="og:image:height" content="{{ config('app.social_image_height') }}" />
    @endif
    <meta property="og:url" content="{{ $post->url() }}" />
    <meta property="og:site_name" content="{{ Site::getCurrent()->name }}" />
    <meta property="og:description" content="{{ htmlspecialchars($post->getOpenGraphDescription()) }}" />
    <meta property="og:type" content="article" />

    <!-- Twitter card -->
    <meta name="twitter:card" content="{{ $post->getTwitterCardType() }}">
    <!-- <meta name="twitter:site" content="@lrsoiree"> -->
    <meta name="twitter:title" content="{{ htmlspecialchars($post->title) }}">
    <meta name="twitter:description" content="{{ htmlspecialchars($post->getTwitterDescription()) }}">
    @if ($post->hasImage())
        <meta name="twitter:image" content="{{ $post->getSocialImage() }}">
    @else
        <meta name="twitter:image" content="{{ config('app.social_image') }}">
    @endif
@endsection

@section('content')

    @include('frontend.blog.partials.post', compact('post'))

@stop
