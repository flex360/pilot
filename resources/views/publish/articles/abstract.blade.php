<div class="article-abstract">
    <div class="row">
        <div class="media col-sm-7">
            <img src="{{ $article->present()->image() }}">
        </div>
        
        <div class="copy col-sm-5">
            <h2>{{ $article->present()->link() }}</h2>

            <p>{{ $article->summary }}</p>

            <p><a href="{{ $article->present()->url() }}">Read More <i class="fa fa-chevron-right"></i></a></p>
        </div>
    </div>
</div>