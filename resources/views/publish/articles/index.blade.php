@foreach ($articles as $article)
    {{ View::make('publish.articles.abstract')->withArticle($article) }}
@endforeach

<ul class="pager">
    
    @if ($page < 50 and count($articles) == $pageSize)
        <li class="previous"><a href="{{ (Request::path() !== '/' ? '/' : '') . Request::path() }}?page={{ $page + 1 }}">&larr; Older</a></li>
    @endif

    @if ($page > 1)
        <li class="next"><a href="{{ (Request::path() !== '/' ? '/' : '') . Request::path() }}?page={{ $page - 1 }}">Newer &rarr;</a></li>
    @endif

</ul>