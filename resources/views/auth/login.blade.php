@extends('layouts.internal')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/login">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="">Username</label>
            <input type="username" name="username" class="form-control" value="{{ old('username') }}">
        </div>

        <div class="form-group">
            <label for="">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control">
                <span class="input-group-btn">
                    <a href="{{ url('/password/reset') }}"class="btn btn-default" type="button">Forgot?</a>
                </span>
            </div>
        </div>

        <div class="form-group">
            <input type="checkbox" name="remember"> Remember Me
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>

@endsection
