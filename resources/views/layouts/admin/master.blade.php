<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->getMetaTitle() }} - Admin</title>

    {!! PilotAsset::link('css') !!}

    <!-- Font awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    {{-- <script src="https://kit.fontawesome.com/abeb59e04a.js" crossorigin="anonymous"></script> --}}

    <!-- DualListBox CSS -->
    <link rel="stylesheet" type="text/css" href="/pilot-assets/components/bootstrap-duallistbox/src/bootstrap-duallistbox.css">

    <!-- DateTimePicker CSS -->
    <link rel="stylesheet" type="text/css" href="/pilot-assets/components/datetimepicker-2.5.20/build/jquery.datetimepicker.min.css">

    <!-- main admin css -->
    <link href="{{ '/pilot-assets/legacy/css/main.css' }}" rel="stylesheet">
    <link href="{{ pmix('/pilot-assets/admin/css/app.css') }}" rel="stylesheet">
    <link src="/dist/components/Trumbowyg-master/dist/ui/trumbowyg.min.css"></link>

    @yield('head')

    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

    @section('header')

            @if (!config('pilot.backend_side_bar_layout'))
            <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <button class="navbar-toggler" data-toggle="collapse" data-target="#collapse_target">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!--Left side of cms nav bar -->
                @if (! empty($currentSite))
                <a class="navbar-brand" href="{{ route('admin.pages.index') }}">{{ $currentSite->name }}</a>
                @endif

                <div class="collapse navbar-collapse" id="collapse_target">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        @if (isset(config('pilot.plugins')['pages']) && config('pilot.plugins')['pages']['enabled'])
                            <li class="nav-item {{ Request::is('pilot') || Request::is('pilot/page*') ? 'active' : null }}"><a class="nav-link" href="/pilot">{{ config('pilot.plugins')['pages']['name'] }}</a></li>
                        @endif

                        @if (isset(config('pilot.plugins')['events']) && config('pilot.plugins')['events']['enabled'])
                            <li class="nav-item {{ Request::is('pilot/event*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.event.index') }}">{{ config('pilot.plugins')['events']['name'] }}</a></li>
                        @endif

                        @if (isset(config('pilot.plugins')['news']) && config('pilot.plugins')['news']['enabled'])
                            <li class="nav-item {{ Request::is('pilot/post*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.post.index') }}">{{ config('pilot.plugins')['news']['name'] }}</a></li>
                        @endif

                        @if (isset(config('pilot.plugins')['annoucements']) && config('pilot.plugins')['annoucements']['enabled'])
                            <li class="nav-item {{ Request::is('pilot/annoucement*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.annoucement.index') }}">{{ config('pilot.plugins')['annoucements']['name'] }}</a></li>
                        @endif

                        @if (isset(config('pilot.plugins')['forms']) && config('pilot.plugins')['forms']['enabled'] && WufooForm::hasForms())
                            <li class="nav-item {{ Request::is('pilot/form*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.form.index') }}">{{ config('pilot.plugins')['forms']['name'] }}</a></li>
                        @endif

                        @include('pilot::admin.partials.modules')

                    </ul>

                    <hr> <!-- This line forces the right side to float right -->

                    <!--Right side of cms nav bar -->
                    <ul class="navbar-nav navbar-right">
                        @if (! empty($currentSite))
                          <li class="nav-item"><a class="nav-link" href="{{ $currentSite->getDefaultProtocol() }}://{{ $currentSite->getDefaultDomain() }}" target="_blank"><i class="fa fa-eye"></i> View Site</a></li>
                        @endif
                        <li class="nav-item {{ Request::is('pilot/setting*') ? 'active' : null }}"><a class="nav-link" href="/pilot/setting"><i class="fa fa-cogs"></i> Settings</a></li>

                        @if (isset($learnPages))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#" data-toggle="dropdown" data-target="#dropdown_learn">
                                    <i class="fa fa-graduation-cap"></i> Learn <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_learn">
                                        @foreach ($learnPages as $learnPage)
                                            {!! $learnPage->getLink(['class' => 'dropdown-item', 'target' => '_blank']) !!}
                                        @endforeach
                                </div>
                            </li>
                        @endif

                        <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#" data-toggle="dropdown" data-target="#dropdown_admin">{{ optional(Auth::user())->getName() }} <span class="caret"></span></a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_admin">
                                @if (Auth::user()->hasRole('admin'))
                                    @if (isset(config('pilot.plugins')['styles']) && config('pilot.plugins')['styles']['enabled'])
                                        {{-- <a class="dropdown-item" href="{{ route('admin.style.index') }}"><i class="fa fa-paint-brush"></i> Styles</a> --}}
                                    @endif
                                    <a class="dropdown-item" href="{{ route('admin.site.index') }}"><i class="fa fa-globe"></i> Websites</a>
                                    <a class="dropdown-item" href="{{ route('admin.user.index') }}"><i class="fa fa-users"></i> Users</a>
                                    <a class="dropdown-item" href="{{ route('admin.role.index') }}"><i class="fa fa-lock"></i> Roles</a>
                                    @if(Auth::user()->username == 'admin')
                                        <a class="dropdown-item" href="/pilot/clear"><i class="fas fa-trash-alt"></i> Clear Application Cache</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                @endif
                                <a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
                            </div>
                        </li>
                    </ul>

                </div> <!--collapse navbar-collapse -->

            </nav>
            </header>

            <div id="app">

                @yield('content')
        
            </div>

            @else <!-- if backend_side_bar_layout is enabled, do this instead -->

            <!-- First render the top navbar like normal, but don't include module links -->
            <header>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
                    <button class="navbar-toggler" data-toggle="collapse" data-target="#collapse_target">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <!--Left side of cms nav bar -->
                    @if (! empty($currentSite))
                    <a class="navbar-brand" href="{{ route('admin.pages.index') }}">{{ $currentSite->name }}</a>
                    @endif
    
                    <div class="collapse navbar-collapse" id="collapse_target">
                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0 d-lg-none d-block">
                            @if (isset(config('pilot.plugins')['pages']) && config('pilot.plugins')['pages']['enabled'])
                                <li class="nav-item {{ Request::is('pilot') || Request::is('pilot/page*') ? 'active' : null }}"><a class="nav-link" href="/pilot">{{ config('pilot.plugins')['pages']['name'] }}</a></li>
                            @endif
    
                            @if (isset(config('pilot.plugins')['events']) && config('pilot.plugins')['events']['enabled'])
                                <li class="nav-item {{ Request::is('pilot/event*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.event.index') }}">{{ config('pilot.plugins')['events']['name'] }}</a></li>
                            @endif
    
                            @if (isset(config('pilot.plugins')['news']) && config('pilot.plugins')['news']['enabled'])
                                <li class="nav-item {{ Request::is('pilot/post*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.post.index') }}">{{ config('pilot.plugins')['news']['name'] }}</a></li>
                            @endif
    
                            @if (isset(config('pilot.plugins')['annoucements']) && config('pilot.plugins')['annoucements']['enabled'])
                                <li class="nav-item {{ Request::is('pilot/annoucement*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.annoucement.index') }}">{{ config('pilot.plugins')['annoucements']['name'] }}</a></li>
                            @endif
    
                            @if (isset(config('pilot.plugins')['forms']) && config('pilot.plugins')['forms']['enabled'] && WufooForm::hasForms())
                                <li class="nav-item {{ Request::is('pilot/form*')  ? 'active' : null }}"><a class="nav-link" href="{{ route('admin.form.index') }}">{{ config('pilot.plugins')['forms']['name'] }}</a></li>
                            @endif
    
                            @include('pilot::admin.partials.modules')
    
                        </ul>

                        <hr> <!-- This line forces the right side to float right -->
    
                        <!--Right side of cms nav bar -->
                        <ul class="navbar-nav navbar-right">
                            @if (! empty($currentSite))
                              <li class="nav-item"><a class="nav-link" href="{{ $currentSite->getDefaultProtocol() }}://{{ $currentSite->getDefaultDomain() }}" target="_blank"><i class="fa fa-eye"></i> View Site</a></li>
                            @endif
                            <li class="nav-item {{ Request::is('pilot/setting*') ? 'active' : null }}"><a class="nav-link" href="/pilot/setting"><i class="fa fa-cogs"></i> Settings</a></li>
    
                            @if (isset($learnPages))
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#" data-toggle="dropdown" data-target="#dropdown_learn">
                                        <i class="fa fa-graduation-cap"></i> Learn <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_learn">
                                            @foreach ($learnPages as $learnPage)
                                                {!! $learnPage->getLink(['class' => 'dropdown-item', 'target' => '_blank']) !!}
                                            @endforeach
                                    </div>
                                </li>
                            @endif
    
                            <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#" data-toggle="dropdown" data-target="#dropdown_admin">{{ optional(Auth::user())->getName() }} <span class="caret"></span></a>
                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_admin">
                                    @if (Auth::user()->hasRole('admin'))
                                        @if (isset(config('pilot.plugins')['styles']) && config('pilot.plugins')['styles']['enabled'])
                                            {{-- <a class="dropdown-item" href="{{ route('admin.style.index') }}"><i class="fa fa-paint-brush"></i> Styles</a> --}}
                                        @endif
                                        <a class="dropdown-item" href="{{ route('admin.site.index') }}"><i class="fa fa-globe"></i> Websites</a>
                                        <a class="dropdown-item" href="{{ route('admin.user.index') }}"><i class="fa fa-users"></i> Users</a>
                                        <a class="dropdown-item" href="{{ route('admin.role.index') }}"><i class="fa fa-lock"></i> Roles</a>
                                        @if(Auth::user()->username == 'admin')
                                            <a class="dropdown-item" href="/pilot/clear"><i class="fas fa-trash-alt"></i> Clear Application Cache</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
                                </div>
                            </li>
                        </ul>
    
                    </div> <!--collapse navbar-collapse -->
    
                </nav>
            </header>

            <!-- Bootstrap row -->
            <div class="row" id="body-row">
                <!-- Sidebar -->
                <div id="sidebar-container" class="sidebar-expanded d-none d-lg-block col-2 bg-light sidebar">
                    <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->
                    <!-- Bootstrap List Group -->
                    <ul class="list-group sticky-top">
                        <!-- Separator with title -->
                        <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                            <small>MODULES</small>
                        </li>
                        <!-- /END Separator -->
                        <!-- Menu with submenu -->
                        @if (isset(config('pilot.plugins')['pages']) && config('pilot.plugins')['pages']['enabled'])
                            <a href="/pilot" class="bg-light list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-start align-items-center">
                                    <span class="menu-collapsed">{{ config('pilot.plugins')['pages']['name'] }}</span>
                                </div>
                            </a>
                        @endif
                        @if (isset(config('pilot.plugins')['events']) && config('pilot.plugins')['events']['enabled'])
                            <a href="{{ route('admin.event.index') }}" class="bg-light list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-start align-items-center">
                                    <span class="menu-collapsed">{{ config('pilot.plugins')['events']['name'] }}</span>
                                </div>
                            </a>
                        @endif
                        @if (isset(config('pilot.plugins')['news']) && config('pilot.plugins')['news']['enabled'])
                            <a href="{{ route('admin.post.index') }}" class="bg-light list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-start align-items-center">
                                    <span class="menu-collapsed">{{ config('pilot.plugins')['news']['name'] }}</span>
                                </div>
                            </a>
                        @endif
                        @if (isset(config('pilot.plugins')['annoucements']) && config('pilot.plugins')['annoucements']['enabled'])
                            <a href="{{ route('admin.annoucement.index') }}" class="bg-light list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-start align-items-center">
                                    <span class="menu-collapsed">{{ config('pilot.plugins')['annoucements']['name'] }}</span>
                                </div>
                            </a>
                        @endif
                        

                        @include('pilot::admin.partials.modulesSidebar')

                        {{-- EXAMPLE OF SUBMENUS --}}
                        {{-- <a href="#submenu1" data-toggle="collapse" aria-expanded="false" onclick="rotateIcon(this.lastElementChild.lastElementChild)"class="submenu-toggler bg-light list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="menu-collapsed">Profile</span>
                                <i class="fas fa-chevron-right ml-auto"></i>
                            </div>
                        </a>
                        <!-- Submenu content -->
                        <div id="submenu1" class="collapse sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="#" class="list-group-item list-group-item-action bg-light text-secondary">
                                        <span class="menu-collapsed">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="list-group-item list-group-item-action bg-light text-secondary">
                                        <span class="menu-collapsed">Password</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="#submenu2" data-toggle="collapse" aria-expanded="false" onclick="rotateIcon(this.lastElementChild.lastElementChild)"class="submenu-toggler bg-light list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="menu-collapsed">Profile</span>
                                <i class="fas fa-chevron-right ml-auto"></i>
                            </div>
                        </a>
                        <!-- Submenu content -->
                        <div id="submenu2" class="collapse sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="#" class="list-group-item list-group-item-action bg-light text-secondary">
                                        <span class="menu-collapsed">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="list-group-item list-group-item-action bg-light text-secondary">
                                        <span class="menu-collapsed">Password</span>
                                    </a>
                                </li>
                            </ul>
                        </div> --}}
                    </ul>
                    <!-- List Group END-->
                </div>
                <!-- sidebar-container END -->
                <div id="app" class="col py-3">

                    @yield('content')

                </div>

            </div>
            @endif

       
    @show

    <script type="text/javascript" src="{{ pmix('/pilot-assets/admin/js/app.js') }}"></script>

    {!! PilotAsset::link('js') !!}

    <!-- Admin javascript -->
    <script type="text/javascript" src="/pilot-assets/legacy/js/main.js"></script>

    <!-- DualListBox javascript -->
    <script src="/pilot-assets/components/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js"></script>
    @if (request()->is('pilot/*'))
    <!-- Module specific javascript -->
    <script type="text/javascript" src="/pilot-assets/legacy/js/{{ strtolower(UrlHelper::getPart(2)) }}.js"></script>
    @endif

    @if (request()->is('pilot'))
    <!-- Module specific javascript -->
    <script type="text/javascript" src="/pilot-assets/legacy/js/page.js"></script>
    @endif
    
    <!-- SortableJS -->
    <script src="/pilot-assets/components/sortablejs/Sortable.min.js"></script>
    <script src="/pilot-assets/components/jquery-sortablejs/jquery-sortable.js"></script>

    <!-- DateTimePicker -->
    <script src="/pilot-assets/components/datetimepicker-2.5.20/build/jquery.datetimepicker.full.min.js"></script>

    <!-- DirtyFormsJS - Leave Site? code -->
    <script src="/pilot-assets/components/jquery.dirtyforms/jquery.dirtyforms.min.js"></script>

    <script>

        function rotateIcon(icon) {   
            icon.classList.toggle('fa-rotate-90');
        }

    </script>

    @yield('scripts')

    <!-- Import Trumbowyg plugins... -->
    <p id="wysiwygSetting" style="display: none;" value="{!! config('wysiwyg.type') !!}"></p>

</body>
</html>
