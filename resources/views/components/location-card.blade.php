@props(['href', 'src', 'loading' => 'eager', 'alt', 'type' => 'default', 'buttonText', 'target' => '_blank'])
<div class="card flex flex-col w-full h-full transition-shadow duration-100" {{ $attributes }}>
    <div class="card-image relative" style="padding-top: 75%;">
        <div class="flex w-full absolute inset-0">
            <x-img
                :src="$src"
                :loading="$loading"
                :alt="$alt"
                style="width: 100%; max-width: auto;"
            ></x-img>
        </div>
    </div>
    @if ($type == 'button')
        <div class="flex flex-grow w-full" style="max-height: 50px;">
            {{-- <a href="{{ $href }}" target="{{ $target }}" class="flex flex-col justify-center items-center w-full text-center text-white bg-denim uppercase p-3">{{ $buttonText }}</a> --}}
            <div class="flex flex-col justify-center items-center w-full text-center text-white bg-denim uppercase p-3">{{ $buttonText }}</div>
        </div>
    @endif
    @if(!empty((string) $slot))
        {{ $slot }}
    @endif
</div>