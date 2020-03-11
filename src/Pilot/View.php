<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Facades\View as LaravelView;

class View
{
    public static function locate($view)
    {
        if (LaravelView::exists($view)) {
            return $view;
        }

        if (LaravelView::exists('pilot::' . $view)) {
            return 'pilot::' . $view;
        }

        return null;
    }
}
