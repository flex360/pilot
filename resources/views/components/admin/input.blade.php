@php
$attributes = $attributes->merge([
    'class' => 'form-control'.($multiple ? ' chosen-select' : ''),
    'id' => $type . '-' . rand(100000000, 999999999),
    'data-placeholder' => $attributes->get('placeholder'),
]);
@endphp
<div class="form-group {{ $wrapperClass }}">
    <label for="{{ $attributes->get('id') }}">{{ $label }}</label>
    @if ($type == 'select')
        <select
            {{ $attributes }}
            {{ $multiple ? 'multiple' : '' }}
            {{ $disabled ? 'disabled' : '' }}
        >
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ $isSelected($optionValue) ? 'selected="selected"' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($type == 'textarea')
        <textarea
            {{ $attributes }}
            {{ $disabled ? 'disabled' : '' }}
        >{{ $value }}</textarea>
    @else
        <input
            type="{{ $type }}"
            {{ $attributes }}
            value="{{ $value }}"
            {{ $disabled ? 'disabled' : '' }}
        >
    @endif
</div>