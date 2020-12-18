<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Site;

class SiteController extends AdminController
{
    public static $namespace = '\\Flex360\\Pilot\\Pilot\\';
    public static $model = 'Site';
    public static $viewFolder = 'sites';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $items = Site::all();

        return view('pilot::admin.' . static::$viewFolder . '.index', compact('items'));
    }

    /**
     * Renders a page with a button to clear server cache
     *
     * @return Response
     */
    public function clearServerCache()
    {
        return view('pilot::admin.clear.cache');
    }

    protected function getClass()
    {
        return Site::class;
    }
}
