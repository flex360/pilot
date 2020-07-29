<li class="{{ $navItem->getCssClasses() }} {{ $navItem->hasActiveChild() ? 'nav__item--expanded' : '' }}">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ $navItem->url }}">{{ $navItem->name }}</a>
        @if ($navItem->hasChildren())
            <div data-toggle-menu="#{{ $navItem->id() }}" style="cursor: pointer;">
                <i class="fa fa-plus-circle" data-action="show"></i>
                <i class="fa fa-minus-circle" data-action="hide" style="display: none;"></i>
            </div>
        @endif
    </div>
    @if ($navItem->hasChildren())
        <ul id="{{ $navItem->id() }}" class="{{ $navItem->hasActiveChild() ? 'expanded' : '' }}" style="height: 0px; overflow: hidden;">
	        @foreach($navItem->children as $child)
                @include('pilot::admin.nav.sidebar._item', ['navItem' => $child])
	        @endforeach
	    </ul>
	@endif
</li>