<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Facades\Post as PostFacade;
use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Tag;
use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Auth;
use Flex360\Pilot\Pilot\MediaHandler;

class PostController extends AdminController
{
    public static $model = 'Post';
    public static $viewFolder = 'posts';

    public function __construct(MediaHandler $mediaHandler)
    {
        $this->fileHandler = $mediaHandler->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $draftedPost = PostFacade::withoutGlobalScopes()
            ->draft()
            ->where('deleted_at', null)
            ->orderBy('published_on', 'desc')
            ->get();

        //To dynamically set notification of how many drafts there are.
        $draftsCount = $draftedPost->count();

        $scheduled = PostFacade::withoutGlobalScopes()->Scheduled()->get();

        $query = PostFacade::withoutGlobalScopes()
                      //->whereRaw('end >= NOW()')
                      ->whereNotIn('id', $draftedPost->pluck('id'))
                      ->whereNotIn('id', $scheduled->pluck('id'))
                      ->where('deleted_at', null)
                      ->orderBy('published_on', 'desc');

        $query = PostFacade::filter($query);

        $posts = $query->paginate(20);

        $tags = Tag::orderBy('name', 'asc')->get();

        $view = 'published';

        return view('pilot::admin.posts.index', compact('posts', 'tags', 'draftsCount', 'view'));
    }

    public function indexOfScheduled()
    {
        $draftedPost = PostFacade::withoutGlobalScopes()
                              ->draft()
                              ->where('deleted_at', null)
                              ->orderBy('published_on', 'desc')
                              ->get();
        //To dynamically set notification of how many drafts there are.
        $draftsCount = $draftedPost->count();

        $query = PostFacade::withoutGlobalScopes()
                                  ->scheduled()
                                  ->where('deleted_at', null)
                                  ->orderBy('published_on', 'desc');

        $query = PostFacade::filter($query);

        $posts = $query->paginate(20);

        $tags = Tag::orderBy('name', 'asc')->get();

        $view = 'scheduled';

        return view('pilot::admin.posts.index', compact('posts', 'draftsCount', 'tags', 'view'));
    }

    public function indexOfDrafts()
    {
        $draftedPost = PostFacade::withoutGlobalScopes()
                              ->draft()
                              ->where('deleted_at', null)
                              ->orderBy('published_on', 'desc');

        //To dynamically set notification of how many drafts there are.
        $draftsCount = $draftedPost->count();

        $draftedPost = $draftedPost->paginate(20);

        $query = PostFacade::withoutGlobalScopes()
                              ->draft()
                              ->where('deleted_at', null)
                              ->orderBy('published_on', 'desc');

        $query = PostFacade::filter($query);

        $posts = $query->paginate(20);

        $tags = Tag::orderBy('name', 'asc')->get();

        $view = 'drafts';

        return view('pilot::admin.posts.index', compact('posts', 'draftsCount', 'tags', 'view'));
    }

    public function indexOfSticky()
    {
        $draftedPost = PostFacade::withoutGlobalScopes()
                              ->draft()
                              ->where('deleted_at', null)
                              ->orderBy('published_on', 'desc');

        //To dynamically set notification of how many drafts there are.
        $draftsCount = $draftedPost->count();

        $draftedPost = $draftedPost->paginate(20);

        $query = PostFacade::withoutGlobalScopes()
                              ->where('sticky', 1)
                              ->where('deleted_at', null)
                              ->orderBy('published_on', 'desc');

        $query = PostFacade::filter($query);

        $posts = $query->paginate(20);

        $tags = Tag::orderBy('name', 'asc')->get();

        $view = 'sticky';

        return view('pilot::admin.posts.index', compact('posts', 'draftsCount', 'tags', 'view'));
    }

    public function indexOfAll()
    {
        $draftedPost = PostFacade::withoutGlobalScopes()
                             ->draft()
                             ->where('deleted_at', null)
                             ->orderBy('published_on', 'desc');

        //To dynamically set notification of how many drafts there are.
        $draftsCount = $draftedPost->count();

        $draftedPost = $draftedPost->paginate(20);

        $query = PostFacade::withoutGlobalScopes()
                             ->where('deleted_at', null)
                             ->orderBy('published_on', 'desc');

        $query = PostFacade::filter($query);

        $posts = $query->paginate(20);

        $view = 'all';

        $tags = Tag::orderBy('name', 'asc')->get();

        return view('pilot::admin.posts.index', compact('posts', 'draftsCount', 'tags', 'view'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $item = PostFacade::getFacadeRoot();

        // set default publish on date
        $item->published_on = date('n/j/Y g:i a');

        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');

        $formOptions = ['route' => 'admin.post.store'];

        return view('pilot::admin.posts.form', compact('item', 'tags', 'formOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //create new post and attach input
        $newPost = PostFacade::getFacadeRoot();
        $input = request()->except('tags', 'horizontal_featured_image', 'vertical_featured_image', 'gallery');

        $newPost->title = $input['title'];
        $newPost->body = $input['body'];
        $newPost->published_on = $input['published_on'];
        $newPost->status = $input['status'];
        $newPost->slug = $input['slug'];
        $newPost->summary = $input['summary'];

        //Change input of slug to make sure its URL friendly
        if (empty($newPost->slug)) {
            $newPost->slug = Str::slug($newPost->title);
        } else {
            $newPost->slug = Str::slug($newPost->slug);
        }

        $item = PostFacade::create($newPost->toArray());

        // deal with post tags
        if (request()->has('tags')) {
            $tags = request()->input('tags');

            $item->addTags($tags);
        }

        // call media manager file handler
        call_user_func_array($this->fileHandler, [&$item, &$input, 'horizontal_featured_image']);
        call_user_func_array($this->fileHandler, [&$item, &$input, 'vertical_featured_image']);
        call_user_func_array($this->fileHandler, [&$item, &$input, 'gallery', false]);

        // set success message
        session()->flash('alert-success', 'News post saved successfully!');

        return redirect()->route('admin.post.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = PostFacade::find($id);

        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');

        $formOptions = [
            'route' => ['admin.post.update', $id],
            'method' => 'put',
            'files' => true,
        ];

        return view('pilot::admin.posts.form', compact('item', 'tags', 'formOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $item = PostFacade::find($id);

        $data = request()->except('image', 'gallery', 'tags');

        // deal with post tags
        if (request()->has('tags')) {
            $tags = request()->input('tags');

            $item->addTags($tags);
        } else {
            $item->tags()->detach();
        }

        $item->fill($data);

        if ($item->slug == '') {
            $item->slug = Str::slug($item->title);
        } else {
            $item->slug = Str::slug($item->slug);
        }

        // deal with if sticky box unchecked
        if (!request()->has('sticky')) {
            $item->sticky = 0;
        }

        $item->save();

        // set success message
        session()->flash('alert-success', 'News post saved successfully!');

        return redirect()->route('admin.post.edit', [$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // make sure the current user is an admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('auth.denied');
        }

        $post = PostFacade::find($id);

        $post->tags()->detach();

        $post->delete();

        // set success message
        session()->flash('alert-success', 'Post deleted successfully!');

        return redirect()->route('admin.post.index');
    }
}
