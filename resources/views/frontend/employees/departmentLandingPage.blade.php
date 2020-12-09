@extends('layouts.internal')

@section('content')

	<h1 class="page-title">{{ $page->title }}</h1>
    
    <!-- loop thru the Departments and it's employees, tags, resources -->
    {{-- @foreach ($departments as $department)
        <h2>{{ $department->name }}</h2>

        
        @foreach ($department->employees as $peep)
            <h3>{{ $peep->name }}</h3>
        @endforeach

        @foreach ($department->tags as $tag)
            <h3>{{ $tag->name }}</h3>
        @endforeach

        @foreach ($department->resources as $resource)
            <h3>{{ $resource->title }}</h3>
        @endforeach
    @endforeach --}}

@endsection