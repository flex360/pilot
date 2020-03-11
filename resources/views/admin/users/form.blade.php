@extends('pilot::layouts.admin.panel')

@section('panel-heading', ($item->exists ? 'Edit' : 'Add') . ' User')

@section('buttons')

@endsection

@section('panel-body')

    <div class="module">

        {!! Form::model($item, $formOptions) !!}

            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', null, array('class' => 'form-control', 'autofocus' => true)) !!}
            </div>

            <div class="form-group">
                {!! Form::label('username', 'Username') !!}
                {!! Form::text('username', null, array('class' => 'form-control', 'autocomplete' => 'new-username')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Password') !!}
                {!! Form::password('password', array('class' => 'form-control', 'placeholder' => 'Optional', 'autocomplete' => 'new-password')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('role_id', 'Role') !!}
                {!! Form::select('role_id', \Flex360\Pilot\Pilot\Role::pluck('name', 'id'), null, array('class' => 'form-control')) !!}
            </div>

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.user.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->hasRole('admin') && $item->exists)
            {!! Form::model($item, array('route' => array('admin.user.destroy', $item->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>

@endsection
