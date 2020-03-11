<!-- <li class="home"><a href="/"><i class="fa fa-home"></i> Home</a></li> -->
@foreach ($pages->children as $page)
    <li class="nav-item {{ Request::is(trim($page->path, '/')) ? 'active' : '' }}"><a href="{{ $page->url() }}" class="nav-link" target={{ $page->open_in_new_tab ? "_blank" : "_self"}}>{{ $page->getNavTitle() }}</a>
        @if ($page->hasChildren('publish'))
            <ul class="dropdown-nav">
                @foreach ($page->getChildren('publish') as $child)
                    <li class="nav-item"><a href="{{ $child->url() }}" class="nav-link {{ Request::is(trim($child->path, '/')) ? 'active' : '' }}" target={{ $child->open_in_new_tab ? "_blank" : "_self"}}>{{ $child->getNavTitle() }}</a></li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
