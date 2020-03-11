@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Merge Tags')

@section('buttons')

@endsection

@section('panel-body')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {!! Form::open(['route' => 'admin.merge.tags',
                    'id' => 'mergeTagsForm']) !!}

    <div class="form-group">
        {!! Form::label('badTag', 'Tag that you want to merge & delete ( Bad Tag )') !!}
        {!! Form::select('badTag', PilotTag::getSelectList(), null, array('class' => 'form-control',
        'autofocus' => true,
        'placeholder' => 'Pick a tag...',
        'required' => true)) !!}
    </div>

    <div class="form-group">
        {!! Form::label('goodTag', 'Tag that you want to merge into ( Good Tag )') !!}
        {!! Form::select('goodTag', PilotTag::getSelectList(), null,
        array('class' => 'form-control',
        'autofocus' => true,
        'placeholder' => 'Pick a tag...',
        'required' => true)) !!}
    </div>

    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
    
@endsection

@section('scripts')

<script>

$('#mergeTagsForm').submit(function(e) {
    // e.preventDefault()
    return confirm('Are you sure? This action will take the Bad Tag and merge all its relationships into the Good Tag, and then delete the Bad Tag.');
})


</script>

@endsection
