@extends('layouts.internal')

@section('content')


<div id="page-container" class="container bg-white p-10 md:p-20 rounded-none md:rounded-b-3xl mb-10 {{ $post->horizontal_featured_image != null ? 'pt-10' : '' }}">

    @if ($post->horizontal_featured_image != null || $post->vertical_featured_image != null)
    @section ('extra-top')
    <!-- header image and overlay text container -->
    <div class="relative">

        <!-- Featured Image Container --> 
        <div id="page-featured-image" class="relative container">
            <div class="absolute inset-0 rounded-none md:rounded-b-3xl rounded-t-none featured-image-linear-gradient" style="z-index: 10;"></div>
            <div class="aspect-ratio-box relative rounded-none md:rounded-b-3xl rounded-t-none">
                <picture>
                    <source srcset="{{ $post->vertical_featured_image != null ? $post->vertical_featured_image : $post->horizontal_featured_image }} 800w" media="(max-width: 768px)">
                    <source srcset="{{ $post->horizontal_featured_image != null ? $post->horizontal_featured_image : $post->vertical_featured_image }} 1000w">
                    <x-img
                        src="{{ $post->horizontal_featured_image }}"
                        class="w-full rounded-none md:rounded-b-3xl rounded-t-none"
                        loading="lazy"
                        alt="Collection Name"
                    ></x-img>
                </picture>
            </div>
        </div>

        <!-- Overlay info -->
        <div class="relative md:absolute h-auto w-full md:w-3/4 lg:w-2/5 xl:w-1/2 position-overlay bg-primaryDark md:bg-transparent text-white">
            <div class="text-center text-white px-1 lg:px-10 py-5 pt-0 md:pt-5">

                <!-- tags & date -->
                <div class="font-body text-sm text-white pt-2 tags" style="font-size: 14px;">
                @if ($post->tags->isNotEmpty())
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('blog.tagged', ['id' => $tag->id, 'slug' => Str::slug($tag->name)]) }}"
                            class=" font-light hover:underline">{{ $tag->name }}</a>
                        @if (!$loop->last)
                        <span> | </span>
                        @endif
                    @endforeach
                @endif
                    <span class="ml-5">
                        <time class="font-body text-white font-light text-sm mt-4 hidden md:inline">
                            {{ $post->published_on->format('l, M j, Y') }}
                        </time>
                    </span>
                </div>
                
                <div class="font-display text-xl lg:text-2xl xl:text-3xl w-full pt-2"><span class="">{{ ucwords($post->title) }}</span></div>
                <p class="font-body font-light text-lg pt-2 px-5 md:px-0">{!! $post->summary !!}</p>
                <span class="ml-5">
                    <time class="font-body text-white font-light text-sm mt-0 md:mt-4 block md:hidden">
                        {{ $post->published_on->format('l, M j, Y') }}
                    </time>
                </span>
            </div>
        </div>
    </div>

        @endsection

    

    @else
    <div class="py-32">
        <h1 class="h1 mb-0">
            {!! $post->title !!}
        </h1>
        <div class="font-body text-primaryNormal">
            <span class="mr-3">{{ $post->published_on->format('l, M j, Y') }}</span>
            <span>
                <i class="fal fa-tag" style="transform: scaleX(-1);"></i>
                @if ($post->tags->isNotEmpty())
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('blog.tagged', ['id' => $tag->id, 'slug' => Str::slug($tag->name)]) }}"
                            class="font-light hover:underline">{{ $tag->name }}</a>
                        @if (!$loop->last)
                        <span> | </span>
                        @endif
                    @endforeach
                @endif
            </span>

            <div class="">{!! $post->getSummary() !!}</div>
            
        </div>
    
    @endif

   <!-- body content -->
   <div class="user-html mt-10">
        {!! $post->body !!}
    </div>

</div>

    @section('extra-bottom')
    <!-- gallery -->
    @if ($post->hasGallery())
    <div class="gallery-container container px-5">
        <div class="flex justify-between items-center w-full">
            <h3 class="h3 text-complementaryDarkBrown">Image Gallery</h3>
            <div onclick="toggleModal(0)" class="hidden md:block font-body text-primaryNormal hover:text-primaryLight transition-all ease-in-out duration-300 no-underline hover:no-underline text-lg mb-3 cursor-pointer">
                <span><i class="far fa-chevron-right text-sm mr-2"></i> View all</span>
            </div>
        </div>

        <!-- grid of gallery images -->
        <div class="flex flex-wrap -mx-3 mt-4">
        @foreach($post->gallery as $index => $img)
            <div onclick="toggleModal({{ $index }})" class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 px-3 rounded-xl cursor-pointer">
                <div class="aspect-ratio-box relative rounded-3xl mb-8">
                    <img src="{{ $img['path'] }}" alt="{{ $img['title'] }}" class="w-full rounded-3xl">
                </div>
            </div>
            
            <!-- galleryImage Modal -->
            @include('partials.galleryImageModal', compact('index', 'img'))
        @endforeach
        </div>
        

        <!-- view all link mobile -->
        <div onclick="toggleModal(0)" class="flex justify-center block md:hidden font-body text-primaryNormal hover:text-primaryLight transition-all ease-in-out duration-300 no-underline hover:no-underline text-lg mb-3 cursor-pointer">
            <span><i class="far fa-chevron-right text-sm mr-2"></i> View all</span>
        </div>
    </div>
    @endif

    <div class="{{ $post->hasGallery() ? 'pt-12' : 'pt-16' }}">
        <hr class="border-primaryLight w-4/5 m-auto">
    </div>

    <div class="w-full flex justify-center py-20">
        <button onclick="window.history.go(-1)" class="btn-danger text-lg px-10 py-5">
            Return to News
        </button>
    </div>
    @endsection

    @if ($post->horizontal_featured_image == null)
    </div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="/pilot-assets/legacy/js/modal.js?v=3"></script>
    <script src="/pilot-assets/legacy/js/modalArrowKey.js"></script>
    
@endpush