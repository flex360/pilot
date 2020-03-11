@extends('layouts.master')

@section('content')
    <div class="post">

        <?php $post = $article; ?>

        <h1 class="post-title">{{ $post->title }}</h1>

        <div class="date-badge">
            by {{ $post->author_link() }} on <strong>{{ $post->published_on() }}</strong>
        </div>

        <?php $tag_links = $post->tag_links(); ?>
        <?php if (!empty($tag_links)): ?>
        <div class="post-tags">
            <strong>Categories:</strong> <?php echo implode(", ", $post->tag_links()); ?>
        </div>
        <?php endif; ?>

        <div class="post-summary" style="display: none;"><?php echo $post->summary; ?></div>

        <?php $content = $post->content(); ?>

        <?php if ($post->asset_type() == 'gallery'): ?>
        <div class="post-content"><?php echo $content; ?></div>
        <?php endif; ?>

        <div class="post-gallery">
            <?php foreach ($post->images() as $image): ?>
                <?php
                $style = '';
                if ($image->width <= 500)
                    $style = 'width: ' . $image->width . 'px; float: right; margin: 0 0 20px 20px;';
                ?>
                <div class="post-image-wrap">
                    <div class="post-image" style="<?php echo $style; ?>">
                        <img src="<?php echo $image->path; ?>" alt="<?php echo $image->title; ?>" data-credit="<?php echo $image->credit; ?>">
                        <div class="post-image-credit">{{ $image->credit }}</div>

                        <?php /*
                        <div class="share-overlay" style="display: none;">
                            <a href="<?php echo Social::facebook_auto($post, $image->path); ?>" class="popup" data-width="500" data-height="500"><i class="fa fa-facebook-square fa-2x" title="Share on Facebook"></i> Share It</a>
                            <a href="<?php echo Social::twitter($post->url(), $post->title); ?>" class="popup" data-width="500" data-height="400"><i class="fa fa-twitter-square fa-2x" title="Share on Twitter"></i> Tweet It</a>
                            <a href="<?php echo Social::pinterest($post->url(), $image->path, $post->summary); ?>" class="popup" data-width="500" data-height="400"><i class="fa fa-pinterest-square fa-2x" title="Pin on Pinterest"></i> Pin It</a>
                        </div>
                        */ ?>
                    </div>

                </div>
            <?php endforeach ?>
        </div>

        <?php if ($post->asset_type() != 'gallery'): ?>
        <div class="post-content"><?php echo $content; ?></div>
        <?php endif; ?>

        <div class="post-footer">
            <?php /*
            <div class="share">
                <h2>Share</h2>
                <ul>
                    <li><a href="<?php echo Social::email($post->url(), $post->title); ?>" class="circle-link circle-link-white external"><i class="fa fa-envelope fa-2x" title="Share via Email"></i></a></li>
                    <li><a href="<?php echo Social::facebook($post->url()); ?>" class="circle-link circle-link-white popup" data-width="400" data-height="500"><i class="fa fa-facebook fa-2x" title="Share on Facebook"></i></a></li>
                    <li><a href="<?php echo Social::twitter($post->url(), $post->title); ?>" class="circle-link circle-link-white popup" data-width="500" data-height="400"><i class="fa fa-twitter fa-2x" title="Share on Twitter"></i></a></li>
                    <li><a href="<?php echo Social::pinterest($post->url(), $post->image(), $post->summary); ?>" class="circle-link circle-link-white popup" data-width="700" data-height="400"><i class="fa fa-pinterest fa-2x" title="Share on Pinterest"></i></a></li>
                    <li><a href="<?php echo Social::google_plus($post->url()); ?>" class="circle-link circle-link-white popup" data-width="500" data-height="400"><i class="fa fa-google-plus fa-2x"></i></a></li>
                </ul>
            </div>
            */ ?>
        </div>

        <?php /*
        <!-- Author information -->
        <?php $author = R::findOne('staff', 'publish_id=?', array($post->author->id)); ?>
        <?php if (!empty($author) && $post->author->id != 0): ?>
            <div class="post-author well">
                <img src="<?php echo $author->photo[0]; ?>">
                <p>
                    <strong><?php echo $author->name; ?></strong>
                    <?php echo (!empty($author->title) ? '<br>' . $author->title : ''); ?><br>
                    <a href="mailto:' . $author->email . '"><?php echo $author->email; ?></a>
                </p>
            </div>
        <?php endif; ?>
        */ ?>

        <?php $related = $post->related(6); ?>
        <?php if (!empty($related)): ?>
        <div class="related-posts hidden-xs">

            <h2>You Might Also Like These</h2>

            <div class="row">
                <?php foreach ($related as $r): ?>
                <div class="related-post col-lg-2 col-md-3 col-sm-3">
                    <a href="<?php echo $r->url(); ?>" title="<?php echo $r->title; ?>"><img src="<?php echo $r->image(300); ?>" class="img-responsive img-thumbnail" alt=""></a>
                    <a href="<?php echo $r->url(); ?>" title="<?php echo $r->title; ?>"><?php echo substr($r->title, 0, 40) . '...'; ?></a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        @if (!empty(Setting::get('disqus-shortname')))
            <div id="comments" class="post-comments">
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                    var disqus_shortname = '{{ Setting::get('disqus-shortname') }}'; // required: replace example with your forum shortname

                    /* * * DON'T EDIT BELOW THIS LINE * * */
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            </div>
        @endif

        <a href="<?php echo $post->url(); ?>" class="post-permalink" style="display: none;">Permalink</a>

    </div>
@stop
