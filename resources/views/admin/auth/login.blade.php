@extends('pilot::layouts.admin.login')
@section('content')
    @include('pilot::admin.partials.alerts')
    {!! Form::open(array('route' => 'admin.login', 'style' => 'margin: 0;')) !!}
        <div class="form-group">
            {!! Form::label('username', 'Username', array('class' => 'sr-only')) !!}
            {!! Form::text('username', null, array('class' => 'form-control input', 'placeholder' => 'Username', 'autofocus' => true)) !!}
        </div>
        <div class="form-group">
            {!! Form::label('password', 'Password', array('class' => 'sr-only')) !!}
            {!! Form::password('password', array('class' => 'form-control input', 'placeholder' => 'Password')) !!}
        </div>
        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
    {!! Form::close() !!}
@endsection
