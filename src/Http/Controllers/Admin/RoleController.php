<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Role;

class RoleController extends AdminController
{
    public static $namespace = '\Flex360\Pilot\Pilot\\';
    public static $model = 'Role';
    public static $viewFolder = 'roles';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $className = static::$namespace . static::$model;

        $items = $className::all();

        return view('pilot::admin.' . static::$viewFolder . '.index', compact('items'));
    }

    protected function getClass()
    {
        return Role::class;
    }
}
