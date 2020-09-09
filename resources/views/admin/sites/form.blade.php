@extends('pilot::layouts.admin.panel')

@section('panel-heading', ($item->exists ? 'Edit' : 'Add') . ' Website')

@section('buttons')

@endsection

@section('panel-body')

    <div class="module">

        {!! Form::model($item, $formOptions) !!}

            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', null, array('class' => 'form-control', 'autofocus' => true)) !!}
            </div>

            <div class="form-group">
                {!! Form::label('domain', 'Domain(s)') !!}
                {!! Form::text('domain', $item->domain, array('class' => 'form-control')) !!}
                <div class="help-block">Separate multiple domains with a comma. Prepend domain with ! to denote default domain.</div>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="force_www" value="0" checked style="display: none;">
                    <input type="checkbox" name="force_www" value="1" {{ $item->force_www ? 'checked' : null }}> Redirect to www
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="hidden" name="force_https" value="0">
                    <input type="checkbox" name="force_https" value="1" {{ $item->force_https ? 'checked' : null }}> Redirect to https
                </label>
            </div>

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.site.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->isAdmin() && $item->exists)
            {!! Form::model($item, array('route' => array('admin.site.destroy', $item->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>

@endsection
