<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Post;
use Flex360\Pilot\Pilot\Page;

class BlogController extends Controller
{
    public function index()
    {
        $posts = \Post::published()
                ->orderBy('published_on', 'desc')
                ->simplePaginate(10);

        \Page::mimic([
            'title' => 'News'
        ]);

        return \View::make('frontend.blog.index', compact('posts'));
    }

    public function loadMorePostIntoIndex()
    {
        $posts = \Post::published()
                ->orderBy('published_on', 'desc')
                ->simplePaginate(10);

        \Page::mimic([
            'title' => 'News'
        ]);

        return \View::make('frontend.blog.loadMorePost', compact('posts'));
    }

    public function post($id, $slug)
    {
        $query = \Post::where('id', '=', $id);

        if (\Auth::guest()) {
            $query = $query->where('status', '=', 30);
        }

        $post = $query->firstOrFail();

        $post->mimicPage();

        $detail = true;

        return \View::make('frontend.blog.post', compact('post', 'detail'));
    }

    public function tagged($id, $slug)
    {
        $posts = Post::join('post_tag', 'posts.id', '=', 'post_tag.post_id')
                ->where('post_tag.tag_id', '=', $id)
                ->published()
                ->where('posts.status', '=', 30)
                ->orderBy('posts.published_on', 'desc')
                ->simplePaginate(9);

        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);
        //dd($slug);

        Page::mimic([
            'title' => 'Blog Posts: ' . $slug,
        ]);

        return view('frontend.blog.index', compact('posts'));
    }

    public function loadMorePostIntoTagged($id, $slug)
    {
        $posts = Post::join('post_tag', 'posts.id', '=', 'post_tag.post_id')
                ->where('post_tag.tag_id', '=', $id)
                ->published()
                ->where('posts.status', '=', 30)
                ->orderBy('posts.published_on', 'desc')
                ->simplePaginate(6);

        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);
        //dd($slug);

        Page::mimic([
            'title' => 'Blog Posts: ' . $slug,
        ]);

        return view('frontend.blog.loadMorePost', compact('posts'));
    }

    public function rss()
    {
        $posts = \Post::latest(20);

        return response()
            ->view('frontend.blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
