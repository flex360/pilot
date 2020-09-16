<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::middleware(['web', 'pilot.global'])->namespace('Flex360\Pilot\Http\Controllers')->group(function () {
    Route::get('/', 'SiteController@index');

    Route::get('/css/theme.css', 'SiteController@css');

    // asset routes (these are used by Froala editor)
    Route::get('/assets/get', ['as' => 'assets.get', 'uses' => 'Admin\AssetController@getAll']);
    Route::group(['middleware' => ['auth.admin']], function () {
        Route::post('/assets/upload', ['as' => 'assets.upload', 'uses' => 'Admin\AssetController@postUpload']);
        Route::post('/assets/delete', ['as' => 'assets.delete', 'uses' => 'Admin\AssetController@postDelete']);
    });

    // // auth routes
    Route::get('/pilot/logout', ['as' => 'admin.logout',      'uses' => 'Admin\AuthController@getLogout']);
    Route::get('/pilot/login', ['as' => 'admin.login',       'uses' => 'Admin\AuthController@getLogin']);
    Route::post('/pilot/login', ['as' => 'admin.login.post',  'uses' => 'Admin\AuthController@postLogin']);

    // // frontend auth routes
    if (config('auth.allow_frontend_login') === true) {
        Auth::routes();

        Route::get('/logout', 'Auth\LoginController@logout');

        if (config('auth.allow_registration') !== true) {
            Route::get('/register', function () {
                return redirect('/');
            });
            Route::post('/register', function () {
                return redirect('/');
            });
        }
    }

    Route::get('/pilot/denied', ['as' => 'auth.denied', 'uses' => 'Admin\AuthController@denied']);

    Route::group(['as' => 'admin.', 'prefix' => 'pilot', 'middleware' => ['auth.admin', 'backend']], function () {
        Route::any('/', ['as' => 'pages.index', 'uses' => 'Admin\PageController@index']);
        Route::get('/page/{id}/sync', ['as' => 'page.sync', 'uses' => 'Admin\PageController@syncType']);
        Route::post('/page/reorder', ['as' => 'page.reorder', 'uses' => 'Admin\PageController@reorder']);
        Route::post('/page/{page}/updateParent/{newParentPageId}', 'Admin\PageController@updateParent')
            ->name('page.updateParentPageId');
        Route::get('/page/select-list', 'Admin\PageController@selectList')->name('page.select-list');
        Route::resource('page', 'Admin\PageController');

        Route::resource('site', 'Admin\SiteController');

        Route::resource('user', 'Admin\UserController');

        Route::resource('role', 'Admin\RoleController');

        Route::get('/clear', 'Admin\SiteController@clearServerCache')->name('admin.clear.cache');

        Route::get('/clear-cache', function () {
            Artisan::call('cache:clear');

            //return 'Server Cache was cleared sucessfully';

            session([
                'alert-success' => 'Application Cache Cleared!',
            ]);

            return redirect('/pilot');
        });

        // Routes for News module
        Route::get('post/scheduled', 'Admin\PostController@indexOfScheduled')->name('post.scheduled');
        Route::get('post/drafts', 'Admin\PostController@indexOfDrafts')->name('post.draft');
        Route::get('post/all', 'Admin\PostController@indexOfAll')->name('post.all');
        Route::resource('post', 'Admin\PostController');

        // Routes for Events module
        Route::get('event/scheduled', 'Admin\EventController@indexOfScheduled')->name('event.scheduled');
        Route::get('event/drafts', 'Admin\EventController@indexOfDrafts')->name('event.draft');
        Route::get('event/past', 'Admin\EventController@indexOfPast')->name('event.past');
        Route::get('event/all', 'Admin\EventController@indexOfAll')->name('event.all');
        Route::get('/event/{id}/copy', 'Admin\EventController@copy')->name('event.copy');
        Route::resource('event', 'Admin\EventController');

        // Routes for Settings module
        Route::resource('setting', 'Admin\SettingController');
        Route::get('setting/{setting}', 'Admin\SettingController@settings')->name('setting.default');

        Route::resource('tag', 'Admin\TagController');

        Route::resource('block', 'Admin\BlockController');

        Route::resource('pagetype', 'Admin\PageTypeController');

        Route::post('/menu/{id}/reorder', ['as' => 'menu.reorder', 'uses' => 'Admin\MenuController@reorder']);
        Route::get('/menu/{id}/items', ['as' => 'menu.items', 'uses' => 'Admin\MenuController@items']);
        Route::resource('menu', 'Admin\MenuController');

        Route::get('/style', ['as' => 'style.index', 'uses' => 'Admin\StyleController@index']);

        Route::get('/form', ['as' => 'form.index', 'uses' => 'Admin\FormController@index']);
        Route::get('/form/{hash}/configuration', [
            'as' => 'form.configuration',
            'uses' => 'Admin\FormController@configuration'
        ]);
        Route::get('/form/{hash}/entries', ['as' => 'form.entries', 'uses' => 'Admin\FormController@entries']);
        Route::post('/form/{hash}/sync', ['as' => 'form.sync', 'uses' => 'Admin\FormController@sync']);
        Route::get('/form/{hash}/entry/{id}', ['as' => 'form.entry', 'uses' => 'Admin\FormController@entry']);

        // media routes
        Route::group(['prefix' => 'media'], function () {
            Route::post('/{id}/info', 'Admin\MediaController@info')->name('media.info');
            Route::post('/{id}/destroy', 'Admin\MediaController@destroy')->name('media.destroy');
            Route::post('/replace', 'Admin\MediaController@replace')->name('media.replace');
            Route::post('/order', 'Admin\MediaController@order')->name('media.order');
            Route::get('/types', 'Admin\MediaController@getTypes')->name('media.types');
            Route::get('/type', 'Admin\MediaController@getByType')->name('media.type');
            Route::post('/rename', 'Admin\MediaController@rename')->name('media.rename');
            Route::post('/upload', 'Admin\MediaController@upload')->name('media.upload');
            Route::post('/move', 'Admin\MediaController@move')->name('media.move');
            Route::get('/search', 'Admin\MediaController@search')->name('media.search');
        });

        // merge tags route
        Route::get('/merge-tags', 'Admin\TagController@mergeTags')->name('merge.tags');
        Route::post('/merge-tags', 'Admin\TagController@mergeTagsExecute');

        /* ---- Dynamo Routes ---- */
    });

    // Route::post('/webhook/wufoo/{hash}', ['as' => 'form.webhook', 'uses' => 'Admin\FormController@webhook']);

    if (config('plugins.news.enabled')) {
        // blog routes
        Route::get('/news', ['as' => 'blog', 'uses' => 'BlogController@index']);
        Route::get('/news/post/{id}/{slug}', ['as' => 'blog.post', 'uses' => 'BlogController@post']);
        Route::get('/news/tagged/{id}/{slug}', ['as' => 'blog.tagged', 'uses' => 'BlogController@tagged']);
        Route::get('/rss.xml', ['as' => 'rss', 'uses' => 'BlogController@rss']);

        //Blog Routes for 'Load More Post' Button
        Route::get('/load-more-news', ['as' => 'blog.more', 'uses' => 'BlogController@loadMorePostIntoIndex']);
        Route::get('/load-more-tagged-news/{id}/{slug}', [
            'as' => 'blog.moreTagged',
            'uses' => 'BlogController@loadMorePostIntoTagged'
        ]);
    }

    if (config('plugins.events.enabled')) {
        // calendar routes
        Route::get('/calendar', ['as' => 'calendar', 'uses' => 'CalendarController@index']);
        Route::get('/calendar/month', ['as' => 'calendar.month', 'uses' => 'CalendarController@month']);
        Route::get('/calendar/json', ['as' => 'calendar.json', 'uses' => 'CalendarController@json']);
        Route::get('/calendar/event/{id}/{slug}', ['as' => 'calendar.event', 'uses' => 'CalendarController@event']);
        Route::get('/calendar/tagged/{id}/{slug}', ['as' => 'calendar.tagged', 'uses' => 'CalendarController@tagged']);
    }

    // sitemap routes
    Route::get('/sitemap', ['as' => 'sitemap', 'uses' => 'SitemapController@index']);
    Route::get('/sitemap.xml', ['as' => 'sitemap.xml', 'uses' => 'SitemapController@xml']);

    //wufoo confirmation page
    Route::post('/wufoo/{hash}/confirm', [
        'as' => 'wufoo.confirm',
        'uses' => 'WufooInterceptorController@confirm'
    ]);

    Route::get('/wufoo/confirm', function () {
        //To use reCAPTCHA v3,
        //Step 1: Use this script on the page where the form is.
        //NOTE:: FIRST PARAM OF grecaptcha.execute() is the site key of reCAPTCHA

        //<script>
        // grecaptcha.ready(function() {
        //     grecaptcha.execute('6LfpWr8UAAAAAM9CyLOZMkZP5-od66uw4mffVHjh', {action: 'homepage'}).then(function(token) {
        //         document.getElementById('reCAPTCHAToken').value = token;
        //         document.getElementById('reCAPTCHAToken').classList.add("disabled", "disabled");

        //     });
        // });
        // </script>

        // <script>

        // jQuery(document).ready(function($){

        //     $('.reCAPTCHAForm').submit(function(e) {
        //         var $theForm = $(this);
        //         var success = false;
        //         var reCAPTCHAToken = document.getElementById('reCAPTCHAToken');
        //         // send xhr request
        //             $.ajax({
        //                 type: 'get',
        //                 url: '/wufoo/confirm',
        //                 async: false,
        //                 data: {"reCAPTCHAToken": reCAPTCHAToken.value,},
        //                 success: function(data) {
        //                     success = data.valid;
        //                 }
        //             });

        //         // if not a bot, will be true, if reCAPTCHA failed, will be false
        //         return success;
        //     });

        // });
        // </script>
        //Step 2: add .reCAPTCHAForm class on the <form> so the code above can select it.
        //Step 3: add <input type="hidden" id="reCAPTCHAToken" name="reCAPTCHAToken" value="" /> before closing </form> tag

        //When someone submits the form, the code below will run and return true or false. True meaning not a robot, and
        //false meaning its a robot and will stop the form submission

        $valid = Recaptcha::isValid(request()->reCAPTCHAToken);

        return ['valid' => $valid];
    });

    Route::post('/page/auth', ['as' => 'page.auth', 'uses' => 'SiteController@pageAuth']);

    Route::post('/form-handler', 'FormController@handler')->name('form.handler');

    Route::get('{path?}', ['as' => 'pages',  'uses' => 'SiteController@view'])->where('path', '.+');
});
