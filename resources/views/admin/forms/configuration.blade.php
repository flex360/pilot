@extends('pilot::layouts.admin.master')

@section('content')

    <div class="alert alert-info">
        <p><strong>Attention!</strong> To receive continuous updates, you must add a webhook notification to each Wufoo form. Use the form specific details below.</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="panel-title"><strong>{{ $form->name }}</strong> Integration Details</h3>
        </div>
        <div class="card-title">

            <table class="table">
                <tbody>
                    <tr>
                        <th>Webhook</th>
                        <td>
                            {{ route('form.webhook', $form->hash) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Handshake Key</th>
                        <td>{{ $form->getHandshakeKey() }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

@endsection
