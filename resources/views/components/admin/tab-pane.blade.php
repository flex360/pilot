<div
    {{ $attributes->merge(['class' => 'tab-pane fade show' . ($active ? ' active' : '')]) }}
    id="{{ $tab }}"
    role="tabpanel"
    aria-labelledby="{{ $tab }}-tab"
>
    {!! $slot !!}
</div>