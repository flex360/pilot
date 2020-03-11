@extends('pilot::layouts.admin.master')

@section('content')

    <div class="module setting-module">

        <h1>Styles</h1>

        @include('pilot::admin.partials.alerts')

        {!! Form::model($site, $formOptions) !!}

        <div class="row">

            @foreach (['color', 'font', 'image'] as $type)

                <h3 class="col-lg-12">{{ ucwords(Str::plural($type)) }}</h3>

                @foreach ($site->getCssConfigByType($type) as $name => $options)
                    <div class="form-group col-sm-3">
                        {!! Form::label('css_' . $name, $options['label']) !!}

                        @if ($options['type'] == 'color')
                            <div class="input-group color-picker">
                                {!! Form::text('css[' . $name . ']', $site->getCssProperty($name), array('class' => 'form-control ' . @$options['class'])) !!}
                                <span class="input-group-addon"><i></i></span>
                            </div>
                        @elseif ($options['type'] == 'font')
                            {!! Form::select('css[' . $name . ']', $options['options'], $site->getCssProperty($name), array('class' => 'form-control ' . @$options['class'])) !!}
                        @elseif ($options['type'] == 'image')

                            {{ Uploader::input('css[' . $name . ']', null, $site->getCssProperty($name), null, []) }}
                        @else
                            {!! Form::$options['type']('css[' . $name . ']', $site->getCssProperty($name), array('class' => 'form-control ' . @$options['class'])) !!}
                        @endif

                        @if (isset($options['help']))
                            <span class="help-block">{{ $options['help'] }}</span>
                        @endif
                    </div>
                @endforeach

            @endforeach

        </div>

        <input type="hidden" name="redirect_to_route" value="{{ 'admin.style.index' }}">

        <button class="btn btn-primary btn-lg">Save</button>

        {!! Form::close() !!}

        {!! Uploader::helper() !!}

    </div>

@stop
