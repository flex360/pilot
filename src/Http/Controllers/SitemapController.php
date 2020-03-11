<?php

namespace Flex360\Pilot\Http\Controllers;

use View;
use Flex360\Pilot\Pilot\Page;

class SitemapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $root = Page::getRoot();

        return view('frontend.sitemap.index', compact('root'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function xml()
    {
        $root = Page::getRoot();

        return view('frontend.sitemap.xml', compact('root'));
    }
}
