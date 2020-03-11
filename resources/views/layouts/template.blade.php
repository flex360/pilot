<!DOCTYPE html>
<html lang="en">
    <head>
        {!! PilotSetting::get('tracking.head_top') !!}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $page->getMetaDesc() }}">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="/favicon.ico">

        <title>{{ $page->getMetaTitle() . ' - ' . PilotSite::getCurrent()->name }}</title>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        {!! PilotAsset::link('css') !!}

        @stack('head')

        {!! PilotSetting::get('tracking.head_bottom') !!}
    </head>

    <body>
        {!! PilotSetting::get('tracking.body_top') !!}

        @include(PilotView::locate('partials.header'))

        @yield('template-content')

        @include(PilotView::locate('partials.footer'))

        <script src="{{ mix('js/app.js') }}"></script>

        {!! PilotAsset::link('js') !!}

        {!! PilotSetting::get('tracking.body_bottom') !!}

        @stack('scripts')
    </body>
</html>
