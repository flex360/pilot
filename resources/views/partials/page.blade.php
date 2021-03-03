
<div id="page-container" class="container bg-white p-10 md:p-20 rounded-none md:rounded-b-3xl mb-10 {{ $page->featured_image != null ? 'pt-10' : '' }}">

    @if ($page->featured_image != null || $page->vertical_featured_image != null)
    @section ('extra-top')
    <!-- header image and overlay text container -->
    <div class="relative">

        <!-- Featured Image Container --> 
        <div id="page-featured-image" class="relative container">
            <div class="absolute inset-0 rounded-none md:rounded-b-3xl rounded-t-none featured-image-linear-gradient" style="z-index: 10;"></div>

            <div class="aspect-ratio-box relative rounded-none md:rounded-b-3xl rounded-t-none">
                <picture>
                    <source srcset="{{ $page->vertical_featured_image != null ? $page->vertical_featured_image : $page->featured_image }} 800w" media="(max-width: 768px)">
                    <source srcset="{{ $page->featured_image != null ? $page->featured_image : $page->vertical_featured_image }} 1000w">
                    <x-img
                        src="{{ $page->featured_image }}"
                        class="w-full rounded-3xl rounded-t-none"
                        loading="lazy"
                        alt="Collection Name"
                    ></x-img>
                </picture>
            </div>
        </div>

        <!-- Overlay info -->
        <div class="relative md:absolute h-auto w-full md:w-3/4 lg:w-2/5 xl:w-1/2 position-overlay-content-page bg-primaryDark md:bg-transparent text-white">
            <div class="text-center text-white px-10 pb-10 lg:px-10 py-5">
                {{-- <div class="w-full uppercase font-light"><span class="text-crail">Collection</span></div> --}}
                <div class="font-display text-xl lg:text-2xl xl:text-3xl w-full mt-2"><span class="">{!! $page->title !!}</span></div>
                <p class="font-body font-light text-lg pt-2">{{ $page->meta_description }}</p>
            </div>
        </div>
    </div>
        
    @endsection

    @else
    <div class="pt-32 pb-8">
        <h1 class="h1 text-3xl md:text-6xl">{{ $page->title }}</h1>
        <p class="p">{{ $page->meta_description }}</p>
    @endif

    {!! $page->block(1) !!}

    {!! $page->body !!}

    {!! $page->block(2) !!}

    @if ($page->featured_image == null)
    </div>
    @endif

</div>

</div>




