<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    
    public function index()
    {
        $posts = Post::published()
                ->orderBy('published_on', 'desc')
                ->simplePaginate(10);

        Page::mimic([
            'title' => 'News'
        ]);

        return view('pilot::frontend.blog.index', compact('posts'));
    }

    public function loadMorePostIntoIndex()
    {
        $posts = Post::published()
                ->orderBy('published_on', 'desc')
                ->simplePaginate(10);

        Page::mimic([
            'title' => 'News'
        ]);

        return view('pilot::frontend.blog.loadMorePost', compact('posts'));
    }

    public function post($id, $slug)
    {
        $query = Post::where('id', '=', $id);

        if (Auth::guest()) {
            $query = $query->where('status', '=', 30);
        }

        $post = $query->firstOrFail();

        $post->mimicPage();

        $detail = true;

        return view('pilot::frontend.blog.post', compact('post', 'detail'));
    }

    public function tagged($id, $slug)
    {
        $posts = Post::join(config('pilot.table_prefix', '') . 'post_tag', config('pilot.table_prefix', '') . 'posts.id', '=', config('pilot.table_prefix', '') . 'post_tag.post_id')
                ->where(config('pilot.table_prefix', '') . 'post_tag.tag_id', '=', $id)
                ->published()
                ->where(config('pilot.table_prefix', '') . 'posts.status', '=', 30)
                ->orderBy(config('pilot.table_prefix', '') . 'posts.published_on', 'desc')
                ->simplePaginate(9);

        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);

        Page::mimic([
            'title' => 'Blog Posts: ' . $slug,
        ]);

        return view('pilot::frontend.blog.index', compact('posts'));
    }

    public function loadMorePostIntoTagged($id, $slug)
    {
        $posts = Post::join(config('pilot.table_prefix', '') . 'post_tag', config('pilot.table_prefix', '') . 'posts.id', '=', config('pilot.table_prefix', '') . 'post_tag.post_id')
                ->where(config('pilot.table_prefix', '') . 'post_tag.tag_id', '=', $id)
                ->published()
                ->where(config('pilot.table_prefix', '') . 'posts.status', '=', 30)
                ->orderBy(config('pilot.table_prefix', '') . 'posts.published_on', 'desc')
                ->simplePaginate(6);

        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);

        Page::mimic([
            'title' => 'Blog Posts: ' . $slug,
        ]);

        return view('pilot::frontend.blog.loadMorePost', compact('posts'));
    }

    public function rss()
    {
        $posts = Post::latest(20);

        return response()
            ->view('pilot::frontend.blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
