@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Menu Manager')

@section('buttons')
    <a href="{{ route('admin.menu.create') }}" class="btn btn-success btn-sm">Add Menu</a>
@endsection

@section('panel-body')

    <div class="module menu-module">

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->slug }}</td>
                    <td>{!! link_to_route('admin.menu.edit', 'Edit', $item->id) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection
