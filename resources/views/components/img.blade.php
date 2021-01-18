@php
    $attributes = $attributes->except(['loading'])->merge(['class' => $loading == 'lazy' ? 'lazy' : '']);
    if ($loading === 'lazy') {
        $attributes = $attributes->merge(['data-src' => $attributes->get('src')]);
        $attributes->offsetUnset('src');
    }
@endphp
<img {{ $attributes }}>