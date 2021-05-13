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

        <link href="{{ pmix('css/app.css') }}" rel="stylesheet">

        {!! PilotAsset::link('css') !!}

        @stack('head')

        {!! PilotSetting::get('tracking.head_bottom') !!}
    </head>

    <body>
        <a href="#content" class="skip-to-content-link">Skip to content</a>
        {!! PilotSetting::get('tracking.body_top') !!}

        <div id="app" v-cloak>

            @include(PilotView::locate('partials.header'))
            <div id="content">
            @yield('template-content')
            </div>

            @include(PilotView::locate('partials.footer'))

        </div>

        <script src="{{ pmix('js/app.js') }}"></script>

        {!! PilotAsset::link('js') !!}

        {!! PilotSetting::get('tracking.body_bottom') !!}

        <!-- mobile responsive on iFrames throughout website -->
        <script src="/pilot-assets/legacy/js/iframeMobileResponsive.js"></script>

        @stack('scripts')
    </body>
</html>
