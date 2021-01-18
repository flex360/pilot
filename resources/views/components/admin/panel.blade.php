<div {{ $attributes->merge(['class' => 'card']) }}>
    @isset($header)
        <div class="card-header">
            @isset($headerRight)
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        {!! $header !!}
                    </div>
                    <div>
                        {!! $headerRight !!}
                    </div>
                </div>
            @else
                {!! $header !!}
            @endisset
        </div>
    @endisset
    <div class="card-body">{!! $slot !!}</div>
    @isset($footer)
        <div class="card-footer">{!! $footer !!}</div>
    @endisset
</div>