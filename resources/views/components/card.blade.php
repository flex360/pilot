@props(['href', 'src', 'loading' => 'eager', 'alt', 'type' => 'default', 'buttonText', 'target' => '_self', 'active' => 'true', 'objectFit'])
<div
    class="card flex flex-col w-full h-full transition-shadow duration-100 {{ $active == 'false' ? 'card-inactive' : '' }}" {{ $attributes }}>
    <div class="card-image relative" style="padding-top: 75%;">
        <a href="{{ $active == 'true' ? $href : '#' }}"
            target="{{ $target }}"
            class="flex w-full absolute inset-0 bg-white {{ $active == 'true' ? '' : 'cursor-default' }}"
            @if ($active == 'false')
                onclick="return false;"
            @endif
        >
            <x-img
                :src="$src"
                :loading="$loading"
                :alt="$alt"
                style="width: 100%; max-width: auto;"
                :class="$objectFit ?? 'object-cover'"
            ></x-img>
        </a>
    </div>
    @if(!empty((string) $slot))
    <div class="flex flex-grow w-full bg-white text-shark">
        <a
            href="{{ $active == 'true' ? $href : '#' }}"
            target="{{ $target }}"
            class="flex flex-col justify-center items-center w-full text-center p-2 {{ $type == 'default' ? 'border-crail border-solid border-b uppercase' : '' }} {{ $type == 'button' ? 'text-xl font-light py-6' : '' }} {{ $active == 'true' ? '' : 'cursor-default' }}"
            @if ($active == 'false')
                onclick="return false;"
            @endif
        >{{ $slot }}</a>
    </div>
    @endif
    @if ($type == 'button')
        <div class="flex flex-grow w-full" style="{{ isset($buttonHeight) ? $buttonHeight : '' }}">
            <a href="{{ $href }}" target="{{ $target }}" class="flex flex-col justify-center items-center w-full text-center text-white bg-denim uppercase p-3">{{ $buttonText }}</a>
        </div>
    @endif
</div>