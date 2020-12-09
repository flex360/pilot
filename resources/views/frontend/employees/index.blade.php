@extends('layouts.internal')

@section('content')

	<h1 class="page-title">{{ $page->title }}</h1>
    
    <!-- loop thru the Employees and their departments -->
    @foreach ($employees as $peep)
        <h2>{{ $employees->name }}</h2>
        @foreach ($peep->departments as $department)
            <h3>{{ $department->title }}</h3>
        @endforeach
    @endforeach

@endsection