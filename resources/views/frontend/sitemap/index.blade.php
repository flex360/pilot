@extends('layouts.internal')

@section('content')

	<ul class="sitemap-ul ul">
		<li class="sitemap-li "><a class="sitemap-a a" href="{{ $root->url() }}">{{ $root->title }}</a></li>
		@foreach ($root->getChildren() as $child)
            @if($child->belongsOnSiteMap())
			<li><a class="sitemap-a a" href="{{ $child->url() }}">{{ $child->title }}</a></li>
            @endif
		@endforeach
	</ul>

@endsection
