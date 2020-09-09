@extends('pilot::layouts.admin.panel')

@section('panel-heading', ($item->exists ? 'Edit' : 'Add') . ' Menu')

@section('buttons')

    @if ($item->exists)    
        <menu-edit-modal></menu-edit-modal>
    @endif

@endsection

@section('panel-body')

    <div class="module">

        {!! Form::model($item, $formOptions) !!}

            <div class="form-group">

                {!! Form::label('name', 'Name') !!}

                {!! Form::text('name', null, array('class' => 'form-control')) !!}

            </div>

            <div class="form-group">

                {!! Form::label('slug', 'Slug') !!}

                {!! Form::text('slug', null, array('class' => 'form-control', 'disabled' => $item->exists)) !!}

            </div>

            @if ($item->exists)

                <div class="form-group">

                    {!! Form::label('menu-items', 'Menu Items') !!}

                    <menu-item-list :id="{{ $item->id }}"></menu-item-list>

                </div>

            @endif

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.menu.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (1==0 && Auth::user()->isAdmin())
            {!! Form::model($item, array('route' => ['admin.menu.destroy', $item->id], 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>

@endsection
