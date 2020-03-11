@extends('pilot::layouts.admin.master')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10">

                @include('pilot::admin.partials.alerts')

                <div class="card mb-4">

                    <div class="card-header">
                        <div class="float-left">
                            @yield('panel-heading')
                        </div>
                        <div class="float-right">
                            @yield('buttons')
                        </div>
                    </div>

                    <div class="card-body @yield('panel-body-class')">
                        @yield('panel-body')
                    </div>

                    @yield('table')

                </div>

                @yield('more')
            </div> <!-- col-12 col-lg-11 col-xl-10-->
        </div> <!-- row justify-content-center-->
  </div> <!-- end container-fluid-pt4-->
@endsection
