@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section('content')
<div class="container-fluid pt-4 {{ config('pilot.backend_side_bar_layout') ? 'pl-lg-0 pr-lg-0' : ''}}">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-11 col-xl-10 {{ config('pilot.backend_side_bar_layout') ? 'pl-lg-0 pr-lg-0' : ''}}">
            <div class="card {{ config('pilot.backend_side_bar_layout') ? 'sidebar-card' : ''}} mb-4">
                <div class="card-header">
                        @if ($dynamo->addVisible())
                            <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-xs float-right">Add {{ $dynamo->getName() }}</a>
                        @endif
                        Employee Manager
                    </div>

                    <div class="card-body">

                        <input type="hidden" id="departmentID" name="departmentID" value="{{ $department->id }}">

                        @include('dynamo::partials.alerts')

                        @if ($items->isEmpty())

                            <div>No Employees found in this department. Please <a href="{{ route('admin.department.edit', array($department->id)) }}">edit this Department</a> to add Employees to it, or <a href="{{ route($dynamo->getRoute('create')) }}">create Employeees.</a></div>

                        @else

                            <table class="table" id="dynamo-index">
                                <thead>
                                    <tr>
                                        <th>Sort</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Departments</th>
                                        <th style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dynamo-index-body">
                                    @foreach ($items as $item)
                                        <tr class="dynamo-index-row" data-id="{{ $item->id }}">
                                          <td><i class="fas fa-bars fa-2x"></i></td>
                                            <td>{!! $dynamo->getIndexValue('photo', $item) !!}</td>
                                            <td>{!! $dynamo->getIndexValue('first_name', $item) !!} {!! $dynamo->getIndexValue('last_name', $item) !!}</td>
                                            <td>{!! $dynamo->getIndexValue('departments', $item) !!}</td>
                                            <td>
                                                <a href="{{ route($dynamo->getRoute('edit'), $item->id) }}" class="btn btn-secondary btn-sm">Edit</a>

                                                @if ($dynamo->deleteVisible())
                                                    {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                                                        <button class="btn btn-secondary btn-sm btn-delete">Delete</button>
                                                    {!! Form::close() !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {!! method_exists($items, 'render') ? $items->appends(request()->only(['q']))->render() : null !!}

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .panel-body .table { margin-bottom: 0; }
    </style>
@endsection

@section('scripts')
    <script>
    $(document).ready(function(){
        $('.btn-delete').click(function(){
            return confirm('Are you sure?');
        });
    });
    </script>
@endsection
