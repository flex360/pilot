<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Flex360\Pilot\Http\Requests;
use Flex360\Pilot\Http\Controllers\Controller;

class LearnController extends Controller
{
    public function show($view)
    {
        \Page::mimic([
            'title' => 'Learn'
        ]);

        return view('admin.learn.' . $view);
    }
}
