@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Websites')

@section('buttons')
    <a href="{{ route('admin.site.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Website</a>
@endsection

@section('panel-body')

    <div class="module site-module">

        <p>These are the websites controlled from this admin.</p>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Domain</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $site)
                <tr>
                    <td>{{ $site->id }}</td>
                    <td>{{ $site->name }}</td>
                    <td><a href="http://{{ $site->domain }}" target="_blank">{{ $site->domain }}</a></td>
                    <td>{!! link_to_route('admin.site.edit', 'Edit', $site->id) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection
