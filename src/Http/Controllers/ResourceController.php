<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Facades\ResourceCategory as ResourceCategoryFacade;
use Flex360\Pilot\Facades\Page as PageFacade;

class ResourceController extends Controller
{
    /**
     * Load /resources page; by default shows accordian's of category with resources inside them
     *
     * @return View
     */
    public function index()
    {
        //get all resource categories, order by name
        $categories = ResourceCategoryFacade::with('resources')->orderBy('name');

        PageFacade::mimic([
            'title' => 'Resources'
        ]);

        return view('pilot::frontend.resources.index', compact('categories'));
    }
}
