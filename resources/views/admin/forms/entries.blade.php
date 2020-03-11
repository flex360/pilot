@extends('pilot::layouts.admin.panel')

@section('panel-heading', $wufoo->name . ' Entries')

@section('buttons')

@endsection

@section('panel-body')

    <table class="table">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $wufoo->getColumnComment($column) }}</th>
                @endforeach
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    @foreach ($columns as $column)
                        <td>{{ $entry->getValue($column) }}</td>
                    @endforeach
                    <td>
                        <a href="{{ route('admin.form.entry', ['hash' => $wufoo->hash, 'id' => $entry->EntryId]) }}" class="btn btn-default btn-xs">View Entry</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {!! $entries->render() !!}

@endsection
