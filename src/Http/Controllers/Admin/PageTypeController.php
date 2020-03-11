<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Page;
use Input;
use Session;
use PageType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Request as RequestFacade;

class PageTypeController extends AdminController
{

    public static $model = 'PageType';
    public static $viewFolder = 'page_types';

    public function store()
    {
        $name = Input::get('name');

        $page_id = Input::get('page_id');

        $data = [
            'name' => $name,
            'slug' => Str::slug($name),
            'page_id' => $page_id
        ];

        $type = PageType::create($data);

        // set page type for the page of origin
        $page = Page::find($page_id);

        $page->type_id = $type->id;

        $page->save();

        // set alert message
        Session::put('alert-success', '<strong>' . $name . '</strong> page type created.');
    }
}
