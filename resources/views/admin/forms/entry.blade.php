@extends('pilot::layouts.admin.panel')

@section('panel-heading', $wufoo->name . ' Entry #' . $entry->EntryId)

@section('buttons')

@endsection

@section('panel-body')

    <table class="table">
        <tbody>
            @foreach ($wufoo->getColumns() as $column)
                <tr>
                    <th style="width: 200px;">{{ $wufoo->getColumnComment($column) }}</th>
                    <td>{!! $entry->getValue($column) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
