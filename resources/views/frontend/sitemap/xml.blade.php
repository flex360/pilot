<?php header('Content-type: text/xml'); ?>
<?php echo '<?'; ?>xml version="1.0" encoding="UTF-8" <?php echo '?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $root->url() }}</loc>
        <lastmod>{{ $root->updated_at->format('c') }}</lastmod>
        <changefreq>weekly</changefreq>
    </url>
    @foreach($root->getChildren() as $child)
        @if($child->belongsOnSiteMap())
            <url>
                <loc>{{ $child->url() }}</loc>
                <lastmod>{{ $child->updated_at->format('c') }}</lastmod>
                <changefreq>weekly</changefreq>
            </url>
        @endif
    @endforeach
</urlset>
