@extends('layouts.internal')

@section('content')

	<h1>Confirm Your Submission</h1>

    <p>Check the box below to complete your submission.</p>

    {!! Recaptcha::render('confirmComplete') !!}

    <form id="wufoo-confirm-form" action="{{ $interceptor->wufooUrl() }}" method="post">
        @foreach($interceptor->data() as $key => $value)
            <textarea name="{{ $key }}" style="display: none;">{{ $value }}</textarea>
        @endforeach
    </form>

@endsection

@section('scripts')
<script>
function confirmComplete(token) {
    $('#wufoo-confirm-form').submit();
}
</script>
@endsection
