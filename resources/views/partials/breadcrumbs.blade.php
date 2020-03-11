<ul class="breadcrumbs">
    @foreach ($breadcrumbs as $crumb)
        <li class="{{ $crumb->isRoot() ? 'breadcrumb-home' : null }}">{{ $crumb->getLink() }}</li>
    @endforeach
    <li class="breadcrumb-current">{{ $page->getLink() }}</li>
</ul>
