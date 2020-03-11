<?php echo '<?xml version="1.0"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

  <channel>
    <title>{{ Site::getCurrent()->name }}</title>
    <link>http://{{ Site::getCurrent()->getDefaultDomain() }}</link>
    <description>Latest news from {{ Site::getCurrent()->name }}</description>
    <atom:link href="http://{{ Site::getCurrent()->getDefaultDomain() }}/rss.xml" rel="self" type="application/rss+xml" />
    <language>en-us</language>
    <pubDate>{{ date('r') }}</pubDate>
    <lastBuildDate>{{ isset($posts[0]) ? $posts[0]->present()->published_on('r') : date('r') }}</lastBuildDate>

    @foreach ($posts as $post)
    <item>
      <title><![CDATA[{{ $post->title }}]]></title>
      <link>{{ $post->url() }}</link>
      <pubDate>{{ $post->present()->published_on('r') }}</pubDate>
      <guid>{{ $post->id }}</guid>
      <description><![CDATA[{{ Str::limit(strip_tags($post->body), 300) }}]]></description>
    </item>
    @endforeach
  </channel>
</rss>
