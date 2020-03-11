<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\User;
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
        $items = User::all();

        return view('pilot::admin.' . static::$viewFolder . '.index', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $item = User::create(request()->except('fake_username_remembered', 'fake_password_remembered'));

        $password = request()->input('password');
        if (! empty($password)) {
            $item->password = Hash::make($password);
            $item->save();
        }

        // set success message
        session()->flash('alert-success', 'User saved successfully!');

        return redirect()->route('admin.user.index');
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
        $item->fill(request()->except('fake_username_remembered', 'fake_password_remembered'));
        $item->save();

        $password = request()->input('password');
        if (! empty($password)) {
            $item->password = Hash::make($password);
            $item->save();
        }

        // set success message
        session()->flash('alert-success', 'User saved successfully!');

        return redirect()->route('admin.user.edit', array($id));
    }
}
