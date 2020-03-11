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

        $formOptions = array(
            'route' => array('admin.site.update', $site->id),
            'method' => 'put',
        );

        return View::make('admin.styles.index', compact('site', 'formOptions'));
    }
}
