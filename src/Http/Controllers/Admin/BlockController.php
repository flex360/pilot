<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Block;
use Input;
use Session;
use Illuminate\Support\Str;

class BlockController extends AdminController
{

    public static $model = 'Block';
    public static $viewFolder = 'blocks';

    public function index()
    {
        //
    }

    public function store()
    {
        $data = Input::except('_token');

        $data['slug'] = Str::slug($data['title']);

        // dd($data);

        $block = Block::create($data);

        Session::put('alert-success', '<strong>' . $data['title'] . '</strong> created!');

        if (Input::has('page_id')) {
            return redirect()->route('admin.page.edit', Input::get('page_id'));
        } else {
            return redirect()->route('admin.block.edit', $block->id);
        }
    }
}
