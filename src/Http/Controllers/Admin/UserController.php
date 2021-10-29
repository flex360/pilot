<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Role;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    public static $namespace = '\Flex360\Pilot\Pilot\\';
    public static $model = 'User';
    public static $viewFolder = 'users';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (Auth::user()->isSuperAdmin()) {
            $items = User::all();
        } else {
            $currentSite = Site::getCurrent();
            $items = User::where('site_id', $currentSite->id)->get();
        }

        return view('pilot::admin.' . static::$viewFolder . '.index', compact('items'));
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

        $excludedRoleKeys = Auth::user()->hasRole('super') ? [] : ['super'];
        $roles = Role::whereNotIn('key', $excludedRoleKeys)->pluck('name', 'id');
        $sites = Site::pluck('name', 'id');
        $sites = array_merge(["" => "All Websites"], $sites->toArray());

        return view('pilot::admin.' . static::$viewFolder . '.form', compact('item', 'formOptions', 'model', 'roles', 'sites'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $item = User::create(request()->except('fake_username_remembered', 'fake_password_remembered', 'password'));

        $password = request()->input('password');
        if (!empty($password)) {
            $item->password = Hash::make($password);
            $item->save();
        }

        // set success message
        session()->flash('alert-success', 'User saved successfully!');

        return redirect()->route('admin.user.index');
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

        $excludedRoleKeys = Auth::user()->hasRole('super') ? [] : ['super'];
        $roles = Role::whereNotIn('key', $excludedRoleKeys)->pluck('name', 'id');
        $sites = Site::pluck('name', 'id');
        $sites = array_merge(["" => "All Websites"], $sites->toArray());

        return view('pilot::admin.' . static::$viewFolder . '.form', compact('item', 'formOptions', 'model', 'roles', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $item = User::find($id);
        $item->fill(request()->except('fake_username_remembered', 'fake_password_remembered', 'password'));
        $item->save();

        $password = request()->input('password');
        if (!empty($password)) {
            $item->password = Hash::make($password);
            $item->save();
        }

        // set success message
        session()->flash('alert-success', 'User saved successfully!');

        return redirect()->route('admin.user.edit', [$id]);
    }

    protected function getClass()
    {
        return User::class;
    }
}
