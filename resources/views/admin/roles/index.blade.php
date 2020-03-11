@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'User Roles')

@section('buttons')
    <a href="{{ route('admin.role.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Role</a>
@endsection

@section('panel-body')

    <div class="module role-module">

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{!! link_to_route('admin.role.edit', 'Edit', $role->id) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection
