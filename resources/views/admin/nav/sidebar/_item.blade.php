<li class="{{ $navItem->getCssClasses() }}">
    <a href="{{ $navItem->url }}">{{ $navItem->name }}</a>
    @if ($navItem->children->isNotEmpty())
	    <ul>
	        @foreach($navItem->children as $child)
                @include('pilot::admin.nav.sidebar._item', ['navItem' => $child])
	        @endforeach
	    </ul>
	@endif
</li>