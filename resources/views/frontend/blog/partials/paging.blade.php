<nav>
    <ul class="pager">
        @if ($paginator->currentPage() > 1)
        <li class="previous"><a href="{{ $paginator->getUrl($paginator->currentPage()-1) }}"><span aria-hidden="true">&larr;</span> Newer</a></li>
        @endif
        
        @if ($paginator->currentPage() != $paginator->lastPage())
        <li class="next"><a href="{{ $paginator->getUrl($paginator->currentPage()+1) }}">Older <span aria-hidden="true">&rarr;</span></a></li>
        @endif
    </ul>
</nav>