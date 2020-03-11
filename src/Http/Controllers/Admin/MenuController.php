<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Request as RequestFacade;
use Flex360\Pilot\Pilot\Menu;

class MenuController extends AdminController
{
    public static $area = 'admin';
    public static $namespace = 'Flex360\Pilot\Pilot\\';
    public static $model = 'Menu';
    public static $viewFolder = 'menus';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        mimic('Menu Manager');
        
        if (config('pilot.multisite')) {
            $items = Menu::where('site_id', '=', $this->site->id)->get();
        } else {
            $items = Menu::all();
            // $items = collect();
        }
        
        return view('pilot::admin.menus.index', compact('items'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = Menu::find($id);

        mimic($item->name . ' Menu');

        $formOptions = array(
            'route' => array('admin.' . strtolower($this->getClassNameFromModel()) . '.update', $id),
            'method' => 'put',
        );

        return view('pilot::admin.' . static::$viewFolder . '.form', compact('item', 'formOptions', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $item = Menu::find($id);
        $item->fill(request()->except('flex_data', 'page_id'));
        $item->save();

        if (method_exists($item, 'updateFlexData')) {
            $item->updateFlexData();
        }

        // attach page to menu
        if (request()->has('page_id')) {
            $page_id = request()->input('page_id');
            $item->pages()->attach($page_id);
        }

        // set success message
        session()->flash('alert-success', 'Menu saved successfully!');

        return redirect()->route('admin.menu.edit', array($id));
    }

    /**
     *  Facilitate reordering of pages
     *
     *  @return View
     */
    public function reorder($id)
    {
        $menu = Menu::find($id);

        $menu->reorder(request()->input('ids'));

        return response()->json(request()->input('ids'));
    }

    public function items($id)
    {
        $menu = Menu::findOrFail($id);

        $items = json_decode($menu->items);

        if (! is_array($items)) {
            $items = [];
        }

        return $items;
    }
}
