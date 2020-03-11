<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;

class AuthController extends AdminController
{
    public function getLogin()
    {
        return view('pilot::admin.auth.login');
    }

    public function postLogin()
    {
        $credentials = [
            'username' => request()->input('username'),
            'password' => request()->input('password')
        ];
        
        if (Auth::attempt($credentials, true)) {
            return redirect()->route('admin.pages.index');
        } else {
            return redirect()->route('admin.login')->withErrors(['login' => 'Login failed!']);
        }
    }

    public function getLogout()
    {
        Auth::logout();

        return redirect()->route('admin.login');
    }

    public function denied()
    {
        return view('admin.auth.denied');
    }
}
