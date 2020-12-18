<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use View;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\Setting;

class StyleController extends AdminController
{
    public function index()
    {
        $site = Site::getCurrent();

        $items = Setting::where('site_id', '=', $site->id)->get();

        $formOptions = [
            'route' => ['admin.site.update', $site->id],
            'method' => 'put',
        ];

        return View::make('pilot::admin.styles.index', compact('site', 'formOptions'));
    }
}
