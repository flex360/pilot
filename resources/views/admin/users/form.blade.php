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
                {!! Form::label('email', 'Email') !!}
                {!! Form::text('email', null, array('class' => 'form-control', 'autocomplete' => 'new-email')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Password') !!}
                {!! Form::password('password', array('class' => 'form-control', 'placeholder' => 'Optional', 'autocomplete' => 'new-password')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('role_id', 'Role') !!}
                {!! Form::select('role_id', $roles, null, array('class' => 'form-control')) !!}
            </div>

            @if (Auth::user()->isSuperAdmin())
                <div class="form-group">
                    {!! Form::label('site_id', 'Website') !!}
                    {!! Form::select('site_id', $sites, null, array('class' => 'form-control')) !!}
                </div>
            @else
                <input type="hidden" name="site_id" value="{{ $currentSite->id }}">
            @endif

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.user.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->isAdmin() && $item->exists)
            {!! Form::model($item, array('route' => array('admin.user.destroy', $item->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>

@endsection
