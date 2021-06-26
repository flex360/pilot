@extends('layouts.template')

@php
$page_featured_image = isset($page->featured_image_custom) ? $page->featured_image_custom : $page->featured_image;
$page_vertical_featured_image = isset($page->vertical_featured_image_custom) ? $page->vertical_featured_image_custom : $page->vertical_featured_image;

if ($page_featured_image == 'NONE') {
    $page_featured_image = null;
    $page_vertical_featured_image = null;
}
@endphp

@if ($page_featured_image != null || $page_vertical_featured_image != null)
    @section ('extra-top')
        <!-- header image and overlay text container -->
        <div class="relative">
            <!-- Featured Image Container --> 
            <div class="relative h-0 featured-image-ratio">
                <picture>
                    <source srcset="{{ $page_vertical_featured_image != null ? $page_vertical_featured_image : $page_featured_image }} 800w" media="(max-width: 768px)">
                    <source srcset="{{ $page_featured_image != null ? $page_featured_image : $page_vertical_featured_image }} 1000w">
                    <x-img
                        src="{{ $page_featured_image }}"
                        class="w-full absolute inset-0 h-full object-cover"
                        loading="lazy"
                        alt="{{ $page->title }}"
                    ></x-img>
                </picture>
            </div>

            <!-- Overlay info - some projects will overlay the h1 and meta description on top of the featured image -->
            {{-- <div class="relative md:absolute h-auto w-full md:w-3/4 lg:w-2/5 xl:w-1/2 position-overlay-content-page bg-primaryDark md:bg-transparent text-white">
                <div class="text-center text-white px-10 pb-10 lg:px-10 py-5">
                    <div class="font-display text-xl lg:text-2xl xl:text-3xl w-full mt-2"><span class="">{!! $page->title !!}</span></div>
                    <p class="font-body font-light text-lg pt-2">{{ $page->meta_description }}</p>
                </div>
            </div> --}}
        </div>
    @endsection
@endif

@section('template-content')
    <!--
        Note that some things are commented out that isn't used in all projects
        such as the breadcrumbs partial and banner zones. Standard mobile responsive
        container is wrapped around the content of this internal layout. Any file
        using this layout can push things above the content container in the 'extra-top'
        and 'extra-bottom' sections
    -->

    <div class="flex flex-col justify-center">
        {{-- @include('partials.breadcrumbs') --}}
        {{-- <x-banner zone="4"></x-banner> --}}

        @yield('extra-top')

        <div class="container px-6 md:px-10 lg:px-16">
            <div class="bg-white relative mb-10 {{ $page_featured_image != null ? 'pt-10' : '' }}">
                
                <!-- if project doesn't overlay h1 and metadescription on top of featured image,
                    it generally post it directly below the featured image -->
                @if ($page_featured_image != null || $page_vertical_featured_image != null)
                    <div class="pt-5 pb-0">
                        <div class="relative">
                            <h1 class="h1 w-full text-5xl md:text-5xl font-black tracking-normal border-b-2 border-primaryNormal pb-3 pr-40 flex-grow m-0">{{ $page->title }}</h1>
                            <p class="p w-full mt-2 font-normal text-base">{{ $page->meta_description }}</p>
                        </div>
                    </div>
                @else
                    <div class="pt-10 pb-8 flex flex-wrap xl:flex-nowrap">
                        <div class="w-full h-20 xl:h-auto lg:w-full xl:w-3/12 2xl:w-1/6"></div>
                        <div class="flex-grow">
                            <h1 class="h1 w-full text-5xl md:text-5xl font-black tracking-normal border-b-2 border-primaryNormal pb-3 px-4 m-0">{{ $page->title }}</h1>
                            <p class="p w-full mt-2 font-normal text-base">{{ $page->meta_description }}</p>
                        </div>
                    </div>
                @endif
                    
                <!-- main content start -->
                <div class="{{ ($page->featured_image != null || $page->vertical_featured_image != null) ? 'pt-8' : 'pt-8' }}">
                    @yield('content')
                </div>
            </div>
        </div>

        {{-- 
             <x-banner zone="5"></x-banner>
             <x-banner zone="6"></x-banner> 
        --}}

        @yield('extra-bottom')
    </div>
    
@endsection