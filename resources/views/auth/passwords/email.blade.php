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

    @if (session()->has('status'))
        <div class="alert alert-success">{{ session()->get('status') }}</div>
    @endif

    <form method="POST" action="{{ url('/password/email') }}">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                Send Password Reset Link
            </button>
        </div>
    </form>

@endsection
