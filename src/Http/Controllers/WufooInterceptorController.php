<?php

namespace Flex360\Pilot\Http\Controllers;

use Illuminate\Http\Request;

use Flex360\Pilot\Http\Controllers\Controller;
use Flex360\Pilot\Http\Requests;
use Flex360\Pilot\Pilot\Forms\Wufoo\WufooInterceptor;
use Flex360\Pilot\Models\Flex\Helpers\Recaptcha;

class WufooInterceptorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function confirm($hash)
    {
        \Page::mimic([
            'title' => 'Confirm Submission'
        ]);

        Recaptcha::init();

        $interceptor = app()->make($hash); //WufooInterceptor::make($hash);

        $interceptor->loadData(request()->all());

        return view('pilot::frontend.wufoo.confirm', compact('interceptor'));
    }
}
