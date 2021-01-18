@props(['href', 'src', 'loading' => 'eager', 'alt', 'type' => 'default', 'buttonText', 'target' => '_self'])
<div class="card flex flex-col w-full h-full transition-shadow duration-100" {{ $attributes }}>


    <div class="card-image">
        <a href="{{ $href }}" target="{{ $target }}" class="block w-full">
            <x-img
                :src="$src"
                :loading="$loading"
                :alt="$alt"
                style="width: 100%; max-width: auto;"
            ></x-img>
        </a>
    </div>

    @if(!empty((string) $slot))
<div class="flex flex-grow w-full bg-white text-shark border-b border-t border-solid border-madison">
    <a
        href="{{ $href }}"
        target="{{ $target }}"
        class="flex flex-col justify-center items-center w-full text-center p-2 {{ $type == 'default' ? 'border-crail border-solid border-b uppercase' : '' }} {{ $type == 'button' ? 'text-xl font-light py-3' : '' }}"
    >{{ $slot }}</a>
</div>
@endif

    @if ($type == 'button')
        <div class="flex flex-grow w-full">
            <a href="{{ $href }}" target="{{ $target }}" class="flex flex-col justify-center items-center w-full text-center text-white bg-crail uppercase p-3">Desktop</a>
            <a href="/live-demo?device=tablet&url={{ urlencode($href) }}" target="{{ $target }}" class="flex flex-col text-black justify-center items-center w-full text-center text-white bg-white uppercase p-3">Tablet</a>
            <a href="/live-demo?device=mobile&url={{ urlencode($href) }}" target="{{ $target }}" class="flex flex-col justify-center items-center w-full text-center text-white bg-denim uppercase p-3">Mobile</a>   
        </div>
    @endif
</div>