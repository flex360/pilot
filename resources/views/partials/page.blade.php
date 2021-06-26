<!-- main content start -->
<div class="{{ ($page->featured_image != null || $page->vertical_featured_image != null) ? 'pt-0' : 'pt-44' }}">
    {!! $page->block(1) !!}

    {!! $page->body !!}

    {!! $page->block(2) !!}
</div>