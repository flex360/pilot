<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Block;
use Flex360\Pilot\Pilot\MediaHandler;

class PageController extends AdminController
{
    public static $namespace = '\Flex36\Pilot\Pilot\\';
    public static $model = 'Page';
    public static $viewFolder = 'pages';

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
        $root = Page::getAdminRoot();

        return view('pilot::admin.page.index', compact('root'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $action = 'create';

        $page = new Page;

        // set parent
        $page->parent_id = request()->input('parent_id', Page::getRoot()->id);

        // set default layout
        $page->layout = $page->getLayout();

        $formOptions = ['route' => 'admin.page.store'];

        $parent_id = $page->parent_id ;

        $parent = Page::find($parent_id);

        $page->status = optional($parent)->status;

        return view('pilot::admin.pages.form', compact('action', 'page', 'formOptions', 'parent_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title' => 'required|max:255'
        ]);

        $data = request()->except('blocks', 'block_settings', 'featured_image');

        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = Str::slug($data['title']);
        }

        $page = Page::create($data);

        // call media manager file handler
        call_user_func_array($this->fileHandler, [&$page, &$data, 'featured_image']);

        // change page type
        $result = $page->changeType(request()->input('type_id'));

        // set success message
        session()->flash('alert-success', self::$model . ' saved successfully!');

        return redirect()->route('admin.page.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $page = Page::find($id);

        // set default layout
        $page->layout = $page->getLayout();

        $formOptions = [
            'route' => ['admin.page.update', $id],
            'method' => 'put',
        ];

        return view('pilot::admin.pages.form', compact('page', 'formOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $page = Page::find($id);

        $this->validate(request(), [
            'title' => 'required|max:255'
        ]);

        // change page type
        $result = $page->changeType(request()->input('type_id'));

        // update blocks
        $data = request()->except('blocks', 'block_order', 'block_settings');

        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $page->updateBlocks(
            request()->input('blocks', []),
            request()->input('block_order', []),
            request()->input('block_settings', [])
        );

        $page->fill($data);

        $page->save();

        // set success message
        session()->flash('alert-success', self::$model . ' saved successfully!');

        return redirect()->route('admin.page.edit', [$id]);
    }

    public function destroy($id)
    {
        // check to see if there is any Menu builder referencing this page or children of this page
        $page = Page::find($id);

        $dontDestroyChildren = false;
        foreach ($page->getChildren() as $child) {
            $used = $child->usedByMenu();
            if ($used) {
                $dontDestroyChildren = true;
            }
        }
        $dontDestroyChildrenParent = $page->usedByMenu();

        //if parent or child is used by a menu, don't destroy the page and tell the user they can't delete page
        // because a menuBuilder is using this page or one of its children
        if ($dontDestroyChildrenParent || $dontDestroyChildren) {
            
            session()->flash('alert-warning', self::$model . ' cannot be deleted while it or one of it\'s child pages are being referenced by a MenuBuilder.');

            return redirect()->route('admin.page.index');
        }

        Page::destroy($id);

        // set success message
        session()->flash('alert-success', self::$model . ' deleted successfully!');

        return redirect()->route('admin.page.index');
    }

    /**
     *  Facilitate reordering of pages
     *
     *  @return View
     */
    public function reorder()
    {
        Page::reorder(request()->input('ids'));

        return response()->json(request()->input('ids'));
    }

    public function syncType($id)
    {
        $page = Page::find($id);

        $typePage = Page::find($page->type->page_id);

        // $data = $order = $settings = [];

        foreach ($typePage->blocks as $block) {
            if (!$page->hasBlock($block->slug)) {
                Block::create([
                    'title' => $block->title,
                    'slug' => $block->slug,
                    'body' => '',
                    'page_id' => $page->id,
                    'type' => $block->type,
                    'position' => $block->position,
                    'settings' => $block->settings
                ]);
            }
        }

        return redirect()->route('admin.page.edit', [$id]);
    }

    public function selectList()
    {
        return Page::selectList();
    }

    public function updateParent(Page $page, $newParentPageId)
    {
        $page->parent_id = $newParentPageId;
        $page->update();
    }
}
