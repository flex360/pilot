<?php

namespace Flex360\Pilot\Http\Middleware;

use Closure;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\Asset;
use Illuminate\Support\Facades\Cache;

class BeforeBackendMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Site::$backend = true;

        // set a fake page for routes without a page
        $title = Site::getRequestTitle();

        mimic([
            'title' => $title
        ]);

        // set current site in config
        $site = Site::setCurrent();
        
        $site = Site::getCurrent();

        // only executed in the admin
        // get the root page (homepage)
        $root = Page::getRoot();

        // if there is no root page, create it
        $root = $root->initRoot();

        // init learn pages
        $site->initLearnPages();

        // share learn pages
        $learnRoot = Cache::rememberForever('pilot-learn-root', function () {
            return Page::findByPath('/learn');
        });

        view()->share('currentSite', $site);

        if (!empty($learnRoot)) {
            $learnPages = $learnRoot->getChildren();
            view()->share('learnPages', $learnPages);
        }

        // color picker
        Asset::css('/pilot-assets/components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css');
        Asset::js('/pilot-assets/components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js');

        // Date Time Picker
        // Asset::css('/pilot-assets/components/datetimepicker-2.5.20/jquery.datetimepicker.css');
        // Asset::js('/pilot-assets/components/datetimepicker-2.5.20/jquery.datetimepicker.js');

        // Select2
        Asset::css('/pilot-assets/components/select2/dist/css/select2.min.css');
        Asset::js('/pilot-assets/components/select2/dist/js/select2.full.min.js');

        // Sortable
        Asset::js('/pilot-assets/components/Sortable/Sortable.min.js');
        Asset::js('/pilot-assets/components/Sortable/jquery.binding.js');

        // Handlebars.js
        Asset::js('/pilot-assets/components/handlebars/handlebars.min.js');

        // FullCalendar
        Asset::js('/pilot-assets/components/moment/min/moment.min.js');
        Asset::js('/pilot-assets/components/fullcalendar/dist/fullcalendar.min.js');
        Asset::css('/pilot-assets/components/fullcalendar/dist/fullcalendar.min.css');
        Asset::css('/pilot-assets/components/fullcalendar/dist/fullcalendar.print.css', 'print');

        // Jquery resizimg
        Asset::js('//rawcdn.githack.com/RickStrahl/jquery-resizable/master/dist/jquery-resizable.min.js');

        return $next($request);
    }
}
