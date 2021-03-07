<div class="help-block">
    Select a background color if you don't have an image, and your logo will 
    automatically display in front of this background color.<br>
</div>

<div class="color-swatch-picker">
    <ul>
    @foreach (config('pilot.plugins.projects.fields.background-color-options') as $color)
   
        <li>
            <label>
                {!! Form::radio('fi_background_color', $color, $item->fi_background_color == $color || (UrlHelper::getPart(3) == 'create' && $loop->first) ? true : false) !!}
                {{-- <input type="radio" name="fi_background_color" value="black" {{ UrlHelper::getPart(3) == 'create' && $loop->first ? 'checked' : '' }}> --}}
                <span class="swatch" style="background-color: {{ $color }}"></span> 
            </label>
        </li>
    @endforeach
    </ul> 
</div> 