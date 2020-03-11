<ul class="nav {{ $menu->class }}">
    @foreach ($menu->tree() as $item)
        @if ($item->hasChildren())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown"
                   href="{{ $item->url }}" role="button" aria-haspopup="true" aria-expanded="false">
                    {{ $item->title }}
                </a>
                <div class="dropdown-menu">
                    @foreach ($item->children as $child)
                        <a class="dropdown-item" href="{{ $child->url }}" target="{{ $child->target }}">
                            {{ $child->title }}
                        </a>
                    @endforeach
                </div>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ UrlHelper::is($item->url) ? 'active' : '' }}" href="{{ $item->url }}" target="{{ $item->target }}">
                    {{ $item->title }}
                </a>
            </li>
        @endif
    @endforeach
</ul>