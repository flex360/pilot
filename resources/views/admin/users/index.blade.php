    @extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Users')

@section('buttons')
    @if (Auth::user()->hasRole('super', 'admin'))
        <a href="{{ route('admin.user.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add User</a>
    @endif
@endsection

@section('panel-body')

    <div class="module user-module table-responsive">

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->getName() }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name }}</td>
                    <td>
                        @if (Auth::user()->canEditUser($user))
                            {!! link_to_route('admin.user.edit', 'Edit', $user->id) !!}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection
