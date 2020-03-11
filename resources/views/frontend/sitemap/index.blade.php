@extends('layouts.internal')

@section('content')

	<ul>
		<li><a href="{{ $root->url() }}">{{ $root->title }}</a></li>
		@foreach ($root->getChildren() as $child)
			<li><a href="{{ $child->url() }}">{{ $child->title }}</a></li>
		@endforeach
	</ul>

@endsection
