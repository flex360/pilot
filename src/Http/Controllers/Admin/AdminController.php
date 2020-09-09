<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Flex360\Pilot\Http\Controllers\Controller;

class AdminController extends Controller
{
    public static $area = 'admin';
    public static $namespace = null;
    public static $model = null;
    public static $viewFolder = 'crud';

    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $model = static::$model;
        $className = static::$namespace . $model;

        // get index columns
        $object = new $className();
        $indexColumns = $object->getIndexColumns();

        if (config('app.multisite')) {
            $items = $className::where('site_id', '=', $this->site->id)->get();
        } else {
            $items = $className::all();
        }

        return view('pilot::admin.' . static::$viewFolder . '.index', compact('items', 'model', 'indexColumns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $model = static::$model;
        $className = static::$namespace . $model;

        $item = new $className;

        mimic('Add ' . $this->getClassNameFromModel());

        $formOptions = ['route' => 'admin.' . strtolower($this->getClassNameFromModel()) . '.store'];

        return view('pilot::admin.' . static::$viewFolder . '.form', compact('item', 'formOptions', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $className = static::$namespace . static::$model;

        $item = $className::create(request()->except('fake_username_remembered', 'fake_password_remembered', 'redirect_to_route', 'flex_data'));

        if (method_exists($item, 'updateFlexData')) {
            $item->updateFlexData();
        }

        // set success message
        session()->flash('alert-success', $this->getClassNameFromModel() . ' saved successfully!');

        // check to see if the form is sending a custom redirect
        $redirect = request()->input('redirect_to_route');
        if (!empty($redirect)) {
            return redirect()->route($redirect);
        }

        return redirect()->route('admin.' . strtolower($this->getClassNameFromModel()) . '.index');
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
        $model = static::$model;
        $className = static::$namespace . $model;

        $item = $className::find($id);

        $formOptions = [
            'route' => ['admin.' . strtolower($this->getClassNameFromModel()) . '.update', $id],
            'method' => 'put',
        ];

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
        $className = static::$namespace . static::$model;

        $item = $className::find($id);
        $item->fill(request()->except('fake_username_remembered', 'fake_password_remembered', 'redirect_to_route', 'flex_data'));
        $item->save();

        if (method_exists($item, 'updateFlexData')) {
            $item->updateFlexData();
        }

        // set success message
        session()->flash('alert-success', $this->getClassNameFromModel() . ' saved successfully!');

        // check to see if the form is sending a custom redirect
        $redirect = request()->input('redirect_to_route');
        if (!empty($redirect)) {
            return redirect()->route($redirect);
        }

        return redirect()->route('admin.' . strtolower($this->getClassNameFromModel()) . '.edit', [$id]);
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

        $className = static::$namespace . static::$model;

        $className::destroy($id);

        // set success message
        session()->flash('alert-success', $this->getClassNameFromModel() . ' deleted successfully!');

        return redirect()->route('admin.' . strtolower($this->getClassNameFromModel()) . '.index');
    }

    public function getClassNameFromModel()
    {
        $model = static::$model;

        $parts = explode('\\', $model);

        return isset($parts[count($parts) - 1]) ? $parts[count($parts) - 1] : null;
    }
}
