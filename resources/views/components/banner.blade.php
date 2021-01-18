@if (!empty($banner))
<div  {{ $attributes->merge(['class' => 'flex justify-center']) }} style="background-color: {{ $banner->bg_color }};">
    <a href="{{ $banner->link }}" target="{{ $banner->link_target }}" class="flex justify-center {{ $banner->link == null ? 'cursor-default pointer-events-none' : '' }}">
        <div class="w-full">
            <picture>
                <source srcset="{{ $banner->image_mobile }} 400w" media="(max-width: 767px)">
                <source srcset="{{ $banner->image_desktop }} 1920w">
                <x-img
                    src="{{ $banner->image_mobile }}"
                    loading="lazy"
                    alt="{{ $banner->name }}"
                ></x-img>
            </picture>
        </div>
    </a>
</div>
@endif

{{-- <div id="home-promo-1" class="flex justify-center" style="background-color: #9a3324;">
    <a href="">
        <x-img
            src="https://s3.amazonaws.com/hanks-fine-furniture/img/home/29b172c79e30324364e604c977d5bf11.jpg"
            loading="eager"
            alt="President's Day - Deals & Steals"
        ></x-img>
    </a>
</div> --}}
