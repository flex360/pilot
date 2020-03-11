@extends('pilot::layouts.admin.panel')

@section('panel-heading', 'Form Manager')

@section('buttons')

@endsection

@section('panel-body')

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Records</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($forms as $form)
                <tr>
                    <td>{{ $form->name }}</td>
                    <td>{{ $form->getRecordCount() }}</td>
                    <td>
                        <a href="{{ route('admin.form.entries', $form->hash) }}" class="btn btn-default btn-xs"><i class="fa fa-list"></i> View Entries</a>
                        <button data-hash="{!! $form->hash !!}" data-token="{!! csrf_token() !!}" class="sync-button btn btn-default btn-xs"><i class="fa fa-cloud-download"></i> Sync Entries <i class="fa fa-spinner fa-spin" aria-hidden="true" style="display: none;"></i></button>
                        <a href="{{ $form->link }}" class="btn btn-default btn-xs" target="_blank"><i class="fa fa-eye"></i> View Form</a>
                        <a href="{{ route('admin.form.configuration', $form->hash) }}" class="btn btn-default btn-xs"><i class="fa fa-cogs"></i> Setup</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
