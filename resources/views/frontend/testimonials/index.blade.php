@extends('layouts.internal')

@section('content')

    <h1 class="page-title">{{ $page->title }}</h1>
    
    <!-- loop thru the Employees and their departments -->
    @foreach ($testimonials as $peep)
        <h3>{{ $peep->name }}</h3>
    @endforeach

@endsection