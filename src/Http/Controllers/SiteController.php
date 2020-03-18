<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Tag;
use Flex360\Pilot\Pilot\Page;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\Publish\Article;
use Flex360\Pilot\Pilot\PageAuthenticator;

class SiteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    public function index($slug = null)
    {
        $page = Page::getRoot();

        if (!$page->exists) {
            $page->layout = 'layouts.home';
            $page->title = $page->title ?: 'Home';
        }

        if (is_object($page)) {
            $page = $page->populate();
        }

        return view('pilot::page', compact('page'));
    }

    public function view($slug = null)
    {
        $page = Page::getCurrent();

        // display 404
        if (empty($page)) {
            Page::mimic([
                'title' => 'Page Not Found',
                'body' => '<p>The page you are looking for has been moved or deleted.</p>',
                'layout' => 'layouts.internal'
            ]);

            return response()
                ->view('pilot::page', [], 404);
        }

        // populate the page with outside data
        $page = $page->populate();

        // handle page authentication
        $authenticator = new PageAuthenticator($page);

        if (!$authenticator->valid()) {
            $page = $authenticator->getPage();
        }

        // redirect if needed
        if (!empty($page->link)) {
            return redirect($page->link);
        }

        return view('pilot::page', compact('page'));
    }

    public function pageAuth()
    {
        $id = request()->get('page');

        $page = Page::find($id);

        $authenticator = new PageAuthenticator($page);

        return $authenticator->getResponse();
    }

    public function post($id, $slug)
    {
        $article = Article::find($id);
        $presenter = $article->present();

        return view('pilot::post', compact('article', 'presenter'));
    }

    public function mergeTags()
    {
        return view('pilot::admin.mergeTags');
    }

    public function mergeTagsExecute()
    {
        $badTag = Tag::find(request()->badTag);
        $goodTag = Tag::find(request()->goodTag);

        foreach ($badTag->posts()->get() as $post) {
            DB::table('post_tag')->insert(
                ['tag_id' => $goodTag->id, 'post_id' => $post->id, ]
            );
            // \DB::table('post_tag')->where('tag_id', $badTag->id)->where('post_id', $post->id)->delete();
        }

        $badTag->delete();

        return redirect('pilot/merge-tags')->with('status', 'Tag deleted!');
    }
}
