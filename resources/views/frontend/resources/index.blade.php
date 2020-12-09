@extends('layouts.internal')

@section('content')

	<h1 class="page-title">{{ $page->title }}</h1>
    
    <!-- loop thru the Resource Categories and create accordian with it's resources inside of it -->
    @foreach ($categories as $cat)
        <h2>{{ $cat->name }}</h2>
        @foreach ($cat->resources as $resource)
            <h3>{{ $resouce->title }}</h3>
        @endforeach
    @endforeach

@endsection