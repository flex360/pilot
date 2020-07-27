<ul class="pilot-nav">
    @foreach ($nav->items() as $navItem)
        @include('pilot::admin.nav.sidebar._item', compact('navItem'))
    @endforeach
</ul>