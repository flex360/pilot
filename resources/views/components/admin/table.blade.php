<table {{ $attributes->merge(['class' => 'table']) }}>
    <thead>
        <tr>
            {!! $head !!}
        </tr>
    </thead>
    <tbody>
        {!! $slot !!}
    </tbody>
</table>