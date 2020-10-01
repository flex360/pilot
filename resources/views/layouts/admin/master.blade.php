<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->getMetaTitle() }} - Admin</title>

    {!! PilotAsset::link('css') !!}

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <!-- DualListBox CSS -->
    <link rel="stylesheet" type="text/css" href="/pilot-assets/components/bootstrap-duallistbox/src/bootstrap-duallistbox.css">

    <!-- main admin css -->
    <link href="{{ pmix('/pilot-assets/admin/css/app.css') }}" rel="stylesheet">
    <link src="/dist/components/Trumbowyg-master/dist/ui/trumbowyg.min.css"></link>

    @yield('head')
    @stack('head')
    @include('pilot::admin.nav.sidebar._styles')

    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

    @section('header')

        @if (!config('pilot.backend_side_bar_layout'))
        <body>
            <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <button class="navbar-toggler" data-toggle="collapse" data-target="#collapse_target">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!--Left side of cms nav bar -->
                @if (! empty($currentSite))
                <a class="navbar-brand" href="{{ route('admin.page.index') }}">{{ $currentSite->name }}</a>
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
                                @if (Auth::user()->isAdmin())
                                    @if (isset(config('pilot.plugins')['styles']) && config('pilot.plugins')['styles']['enabled'])
                                        {{-- <a class="dropdown-item" href="{{ route('admin.style.index') }}"><i class="fa fa-paint-brush"></i> Styles</a> --}}
                                    @endif
                                    <a class="dropdown-item" href="{{ route('admin.user.index') }}"><i class="fa fa-users"></i> Users</a>
                                    @if(Auth::user()->hasRole('super'))
                                        <a class="dropdown-item" href="{{ route('admin.role.index') }}"><i class="fa fa-lock"></i> Roles</a>
                                        <a class="dropdown-item" href="{{ route('admin.site.index') }}"><i class="fa fa-globe"></i> Websites</a>
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

            <body class="body-sidebar">
            <!-- First render the top navbar like normal, but don't include module links -->
            <header>
                <nav class="navbar navbar-expand-lg navbar-dark navbar-bg-sidebar">
                    <button class="navbar-toggler" data-toggle="collapse" data-target="#collapse_target">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <!--Left side of cms nav bar -->
                    @if (! empty($currentSite))
                    <a class="navbar-brand" href="{{ route('admin.page.index') }}">{{ $currentSite->name }}</a>
                    @endif
    
                    <div class="collapse navbar-collapse" id="collapse_target">
                        <!-- this menu is tablet and mobile only -->
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

                            <!-- this menu is tablet and mobile only -->
                            @include('pilot::admin.partials.modules')

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

                        <hr class="d-none d-lg-block"> <!-- This line forces the right side to float right -->
    
                        <!--Right side of cms nav bar -->
                        <div class="align-items-center d-none d-lg-flex">
                            <ul class="navbar-nav navbar-right" style="margin-right: 1rem;">
                                @if (! empty($currentSite))
                                <li class="nav-item"><a class="nav-link pilot-sidebar-view-site" href="{{ $currentSite->getDefaultProtocol() }}://{{ $currentSite->getDefaultDomain() }}" target="_blank"> View Site</a></li>
                                @endif
                            </ul>

                            @php
                                $user = auth()->user();
                            @endphp
                            <!-- Sidebar profile icon -->
                            {{-- <a class="sidebar-profile-icon" href="/">
                                <div class="sidebar-profile-icon-container">
                                    {{ $user->name != null ? substr($user->name, 0, 1) : 'U' }}
                                </div>
                            </a> --}}



    <!-- Froala plugin for downloading html textarea as a pdf -->
    <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
                            <div id="profile-menu" class="order-1 order-lg-12 ml-auto">
                                <div class="dropdown-toggle account" role="button" id="dropdownMenuLink" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <a class="sidebar-profile-icon" href="/">
                                        <div class="sidebar-profile-icon-container">
                                            {{ $user->name != null ? substr($user->name, 0, 1) : 'U' }}
                                        </div>
                                    </a>
                                </div>
                    
                                <div class="dropdown-menu dropdown-menu-center dropdown-menu-lg-right px-3 py-3" aria-labelledby="dropdownMenuLink" style="background-color: #222529;">
                                    <a class="d-block" href="/pilot/logout" style="color: white;">Sign Out</a>
                                </div>
                              </div>








                        </div>
    
                    </div> <!--collapse navbar-collapse -->
    
                </nav>
            </header>

            <!-- Bootstrap row -->
            <div id="app">
                <div class="row" id="body-row">
                    <!-- Sidebar -->
                    <!-- this menu is destop view -->
                    <div id="pilot-sidebar-container" class="sidebar-expanded d-none d-lg-block col-2 sidebar">
                        <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->

                        <!-- Website name -->
                        <div class="pilot-sidebar-name">{{ $currentSite->name }}</div>
                        <!-- Sidebar -->
                        <div class="pilot-sidebar">
                        @php
                            $navItems = [];
                            foreach (config('pilot.plugins') as $key => $settings) {
                                if (isset($settings['enabled']) && $settings['enabled']) {
                                    if (isset($settings['url']) && is_array($settings['url'])) {
                                        $url = route(...$settings['url']);
                                    } else {
                                        $url = $settings['url'] ?? '';
                                    }
                                    $navItems[] = PilotNavItem::make($settings['name'], $url);
                                }
                            }

                        @endphp

                        <!-- this wrapper makes the base plugin modules Nav have no margin on bottom -->
                        <div class="plugins-nav-items-wrapper">{!! PilotNav::create(...$navItems) !!}</div>
                    
                        <!-- this menu is destop view right under the default plugin modules -->
                        @include('pilot::admin.partials.modulesSidebar')


                        <!-- List Group END-->
                        {{-- <hr style="
                        margin: 20px;
                        border-top-color: rgba(0, 0, 0, 0.1);
                        border-top-style: solid;
                        border-top-width: 1px;
                        border-top: 1px solid rgba(0, 0, 0, 0.1);
                        border-style: inset;
                        border-width: 1px;"> --}}


                        <!-- Second half of the sidebar modules -->
                        {{-- <ul class=""> --}}
                            {{-- <div class="second-half-sidebar"> --}}
                            <div class="pilot-sidebar-lower">
                            @php
                                $learnNav = PilotNavItem::make('<i class="fa fa-graduation-cap" style="margin-right: 7px;"></i> Learn', '', 'admin.learn.*', null, '_blank');
                                foreach($learnPages as $learnPage) {
                                    $learnNav->addChildren(
                                        PilotNavItem::make($learnPage->title, $learnPage->url(), null, null, '_blank')
                                    );
                                }
                            @endphp
    
                            {!! PilotNav::create(
                                PilotNavItem::make('<i class="fa fa-cogs" style="margin-right: 7px;"></i> Settings', route('admin.setting.index'), 'admin.setting.*'),
                                $learnNav,
                                PilotNavItem::make('<i class="fas fa-tools" style="margin-right: 7px;"></i> Admin', '')
                                    ->addChildren(
                                        PilotNavItem::make('Websites', route('admin.site.index')),
                                        PilotNavItem::make('Users', route('admin.user.index')),
                                        PilotNavItem::make('Roles', route('admin.role.index')),
                                        PilotNavItem::make('Logout', '/pilot/logout')
                                    )
                            ) !!}
                            </div>
                        </div>

                                <!-- settings -->
                                {{-- <a href="{{ route('admin.setting.index') }}" class="{{ Request::is('pilot/setting*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-start align-items-center">
                                        <i class="fa fa-cogs" style="margin-right: 7px;"></i><span class="menu-collapsed" style="font-weight: 200;">Settings</span>
                                    </div>
                                </a> --}}

                                <!-- Learn more pages -->
                                {{-- <a href="#submenu3" data-toggle="collapse" aria-expanded="false" onclick="rotateIcon(this.lastElementChild.lastElementChild)" class="submenu-toggler secondhalf sidebar list-group-item list-group-item-action flex-column align-items-start" style="background-color: #474b54 !important;">
                                    <div class="d-flex w-100 justify-content-start align-items-center">
                                        <i class="fa fa-graduation-cap" style="margin-right: 7px;"></i><span class="menu-collapsed">Learn</span>
                                        <i class="fas fa-chevron-right ml-auto" style="font-size: 16px;"></i>
                                    </div>
                                </a>

                                <div id="submenu3" class="collapse sidebar-submenu">
                                    <ul class="secondhalf" style="background: #474b54 !important; padding: 0px 5px;">
                                        @foreach ($learnPages as $learnPage)
                                        <li>
                                            {!! $learnPage->getLink(['class' => 'active secondhalf sidebar list-group-item list-group-item-action text-secondary', 'target' => '_blank']) !!}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div> --}}

                                {{-- <collapsable 
                                    label="Admin"
                                    :expanded="true"
                                >

                                    <template v-slot:label="$props">
                                        <a href="#submenu4" data-toggle="collapse" aria-expanded="true" onclick="rotateIcon(this.lastElementChild.lastElementChild)" class="submenu-toggler secondhalf sidebar list-group-item list-group-item-action flex-column align-items-start" style="background-color: #474b54 !important;">
                                            <div class="d-flex w-100 justify-content-start align-items-center">
                                                <span class="menu-collapsed" style="font-weight: 200;">Admin</span>
                                                <i class="fas fa-chevron-right ml-auto" style="font-size: 13px;"></i>
                                            </div>
                                        </a>
                                        <div class="flex items-center border-iron border-b p-2 text-2xl cursor-pointer" style="display: flex; justify-content: space-between; color: #fff;">
                                            <div class="text-left">Admin</div>
                                            <div class="">
                                                <svg width="17" height="11" xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.2s linear 0s;" :style="{ transform: $props.collapsed ? '' : 'rotate(180deg)' }">
                                                    <path stroke="currentColor" stroke-width="3" d="M1 2l7.547 6.534L16.017 2" fill="none" fill-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div id="submenu4" class="sidebar-submenu collapse text-left">
                                            <ul class="secondhalf" style="background: #474b54 !important; padding: 0px 5px;">
                                                <li>
                                                    <a href="{{ route('admin.site.index') }}" class="{{ Request::is('pilot/site*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action text-secondary">
                                                        <span class="menu-collapsed">Websites</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.user.index') }}" class="{{ Request::is('pilot/user*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action text-secondary">
                                                        <span class="menu-collapsed">Users</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.role.index') }}" class="{{ Request::is('pilot/role*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action text-secondary">
                                                        <span class="menu-collapsed">Roles</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/pilot/clear" class="{{ Request::is('pilot/clear*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action text-secondary">
                                                        <span class="menu-collapsed">Clear Application Cache</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/pilot/logout" class="{{ Request::is('pilot/logout*')  ? 'active' : null }} secondhalf sidebar list-group-item list-group-item-action text-secondary">
                                                        <span class="menu-collapsed">Logout</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </template>

                                    
                                </collapsable> --}}

                            {{-- </div> --}}
                        {{-- </ul> --}}
                    </div>

                    <!-- sidebar-container END -->
                    <div class="pilot-content col py-3">

                        @yield('content')

                    </div>

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

    <!-- DirtyFormsJS - Leave Site? code -->
    <script src="/pilot-assets/components/jquery.dirtyforms/jquery.dirtyforms.min.js"></script>

    @yield('scripts')
    @stack('scripts')

</body>
</html>
