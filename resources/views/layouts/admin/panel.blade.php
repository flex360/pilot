@extends('pilot::layouts.admin.master')

@section('content')
    <div class="container-fluid pt-4 {{ config('pilot.backend_side_bar_layout', false) ? 'pl-lg-0 pr-lg-0' : ''}}">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 {{ config('pilot.backend_side_bar_layout', false) ? 'pl-lg-0 pr-lg-0' : ''}}">

                @include('pilot::admin.partials.alerts')

                <div class="card {{ config('pilot.backend_side_bar_layout', false) ? 'sidebar-card' : ''}} mb-4">

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
