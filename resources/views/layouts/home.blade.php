@extends(PilotView::locate('layouts.template'))

@section('template-content')

    <!-- 
        it's recommended to just delete the welcome partial file and reference here.
        Start creating the homepage of your website in this "home" layout file.
    -->
    @include('pilot::partials.welcome')

@endsection
