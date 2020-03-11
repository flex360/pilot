@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Settings')

@section('buttons')
    {{-- <a href="{{ route('admin.setting.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Setting</a> --}}
@endsection

@section('panel-body')

    {{-- NOTE: DO NOT DELETE, USE THIS CODE TO SWITCH TO BOOTSTRAP TABS INSTEAD OF LIST GROUP --}}
    {{-- <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="index-nav-tabs">
        @foreach ($allSettings as $set)
            <li class="nav-item">
              <a class="nav-link {{ $view == $set['label'] ? 'active' : '' }}" href="{{ route('admin.setting.default', ['setting'=>strtolower($set['label'])]) }}">{{ $set['label'] }}</a>
            </li>
        @endforeach
        </ul>
    </div> --}}


    <div class="row">
        <div class="col-3">
            <div class="list-group" id="list-tab" role="tablist">
                @foreach (config('settings') as $key => $tempSetting)
                    <a class="list-group-item list-group-item-action {{ $configSetting['label'] == $tempSetting['label'] ? 'active' : '' }}"
                    href="{{ route('admin.setting.default', ['setting' => $key]) }}"
                    role="tab"
                    aria-controls="home"
                    title="{{ $key }}">
                    {{ $tempSetting['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="col-9">

            <div style="margin-top: 10px;" class="module">

                {!! Form::model($configSetting, $formOptions) !!}

                @foreach ($configSetting['fields'] as $index => $field)

                    @if ($field['type'] == 'header')
                        <h4 style="margin-bottom: 20px;">{{ $field['label'] }}</h4>
                    @elseif ($field['type'] == 'text' || $field['type'] == 'textarea')
                    <div class="form-group">
                        <label for="{{ $field['id'] }}" title=""{{ $configSetting['key'] . '.' . $field['id'] }}>
                            {!! $field['label'] !!}
                        </label>
                        @if ($field['type'] == 'text')
                            {!! Form::text($field['id'], $setting->getFieldValueById($field['id']), array('class' => 'form-control')) !!}
                        @elseif ($field['type'] == 'textarea')
                            {!! Form::textarea($field['id'], $setting->getFieldValueById($field['id']) , array('class' => 'form-control ' . $field['id'])) !!}
                        @endif
                    </div>
                    @endif
                @endforeach

                @if($configSetting['fields'] != null)
                    <button class="btn btn-primary">Save</button>
                    {{-- <a class="form-cancel" href="{{ route('admin.setting.default', ['setting'=> strtolower($configSetting['label'])]) }}">Cancel</a> --}}
                @endif

                {!! Form::close() !!}
            </div>
        </div>

    </div>

@endsection
