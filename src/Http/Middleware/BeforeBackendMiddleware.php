<?php

namespace Flex360\Pilot\Http\Middleware;

use Closure;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\Asset;

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

        Page::mimic([
            'title' => $title
        ]);

        $site = Site::getCurrent();

        // only executed in the admin
        // get the root page (homepage)
        $root = Page::getRoot();

        // if there is no root page, create it
        $root = $root->initRoot();

        // init learn pages
        $site->initLearnPages();

        // share learn pages
        $learnRoot = Page::findByPath('/learn');

        if (!empty($learnRoot)) {
            $learnPages = $learnRoot->getChildren();
            view()->share('learnPages', $learnPages);
        }

        // Froala Editor 2.7.3
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/froala_editor.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/themes/dark.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/froala_style.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/plugins/draggable.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/plugins/file.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/plugins/image.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/plugins/image_manager.min.css');
        Asset::css('/pilot-assets/components/froala-wysiwyg-editor/css/plugins/code_view.min.css');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/froala_editor.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/draggable.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/file.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/link.min.js');
        // \Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/font_size.min.js');
        // \Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/font_family.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/paragraph_format.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/paragraph_style.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/align.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/image.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/image_manager.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/third_party/image_aviary.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/lists.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/video.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/code_view.min.js');
        Asset::js('/pilot-assets/components/froala-wysiwyg-editor/js/plugins/code_beautifier.min.js');

        // color picker
        Asset::css('/pilot-assets/components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css');
        Asset::js('/pilot-assets/components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js');

        // Date Time Picker
        Asset::css('/pilot-assets/components/datetimepicker-2.5.20/jquery.datetimepicker.css');
        Asset::js('/pilot-assets/components/datetimepicker-2.5.20/jquery.datetimepicker.js');

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

        return $next($request);
    }
}
