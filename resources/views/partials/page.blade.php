@if ($page->featured_image != null)
    @section ('extra-top')
        <!-- Featured Image Container --> 
        <div class="relative">
            <picture>
                <source srcset="{{ $page->featured_image }} 800w" media="(max-width: 768px)">
                <source srcset="{{ $page->featured_image }} 1000w">
                <x-img
                    src="{{ $page->featured_image }}"
                    class="w-full"
                    loading="lazy"
                    alt="Collection Name"
                ></x-img>
            </picture>

            <!-- Overlay info -->
            <div class="absolute h-auto w-2/3 md:w-1/2 lg:w-2/5 xl:w-1/3 hidden md:inline" style="top: 35px; background: rgba(255, 255, 255, 0.75);">
                <div class="px-10 py-5">
                    {{-- <div class="w-full uppercase font-light"><span class="text-crail">Collection</span></div> --}}
                    <div class="w-full text-3xl font-display mt-2 border-b border-solid border-crail uppercase"><span class="">{!! $page->title !!}</span></div>
                    <p class="font-body pt-5">{{ $page->meta_description }}</p>
                </div>
            </div>
            <!-- Red line at the bottom of the Featured Image container -->
            {{-- <div class="relative border-b border-solid border-crail mx-10" style="bottom: 30px"></div> --}}
        </div>

        <!-- Overlay info -->
        <div class="w-full inline md:hidden" style="top: 35px; background: rgba(255, 255, 255, 0.75);">
            <div class="px-10 py-5">
                {{-- <div class="w-full uppercase font-light"><span class="text-crail">Collection</span></div> --}}
                <div class="w-full text-3xl font-display mt-2 border-b border-solid border-crail uppercase"><span class="">{!! $page->title !!}</span></div>
                <p class="font-body pt-5">{{ $page->meta_description }}</p>
            </div>
        </div>

    @endsection

@else
<div class="py-5">
    <div class="w-full uppercase text-lg font-light mt-12"><span class="text-crail"></span></div>
    <div class="w-full text-3xl font-display font-bold mt-2 border-b border-solid border-crail"><span class="">{!! $page->title !!}</span></div>
@endif

{!! $page->block(1) !!}

{!! $page->body !!}

{!! $page->block(2) !!}

@if ($page->featured_image == null)
</div>
@endif
