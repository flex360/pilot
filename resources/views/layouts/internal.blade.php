@extends(PilotView::locate('layouts.template'))

@section('template-content')

    <!--
        Note that some things are commented out that isn't used in all projects
        such as the breadcrumbs partial and banner zones. Standard mobile responsive
        container is wrapped around the content of this internal layout. Any file
        using this layout can push things above the content container in the 'extra-top'
        and 'extra-bottom' sections
    -->

    <div class="flex flex-col justify-center">
        {{-- <x-banner zone="4"></x-banner> --}}


        {{-- @include('partials.breadcrumbs') --}}

        @yield('extra-top')

        <div class="container px-6 md:px-10 lg:px-16 {{ UrlHelper::getPart(1) == 'collections' ? 'pb-5' : 'py-5' }}">
            @yield('content')
        </div>

        @yield('extra-bottom')

        {{-- <x-banner zone="5"></x-banner>
        <x-banner zone="6"></x-banner> --}}
    </div>

@endsection
