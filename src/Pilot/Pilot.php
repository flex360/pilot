<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

class Pilot
{
    public static function routesBefore()
    {
        Route::middleware(['web', 'pilot.global'])->namespace('\Flex360\Pilot\Http\Controllers')->group(function () {
            Route::get('/', 'SiteController@index');

            Route::get('/css/theme.css', 'SiteController@css');

            // asset routes (these are used by Froala editor)
            Route::get('/assets/get', 'Admin\AssetController@getAll')->name('assets.get');
            Route::group(['middleware' => ['auth.admin']], function () {
                Route::post('/assets/upload', 'Admin\AssetController@postUpload')->name('assets.upload');
                Route::post('/assets/delete', 'Admin\AssetController@postDelete')->name('assets.delete');
            });

            // // auth routes
            Route::get('/pilot/logout', 'Admin\AuthController@getLogout')->name('admin.logout');
            Route::get('/pilot/login', 'Admin\AuthController@getLogin')->name('admin.login');
            Route::post('/pilot/login', 'Admin\AuthController@postLogin')->name('admin.login.post');

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

            Route::get('/pilot/denied', 'Admin\AuthController@denied')->name('auth.denied');

            Route::group(['as' => 'admin.', 'prefix' => 'pilot', 'middleware' => ['auth.admin', 'backend']], function () {
                Route::any('/', 'Admin\PageController@index')->name('page.index');
                Route::get('/page/{id}/sync', 'Admin\PageController@syncType')->name('page.sync');
                Route::post('/page/reorder', 'Admin\PageController@reorder')->name('page.reorder');
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

                // Backend Routes for Routes for News module
                if (config('pilot.plugins.news.enabled', false)) {
                    Route::get('post/sticky', 'Admin\PostController@indexOfSticky')->name('post.sticky');
                    Route::get('post/scheduled', 'Admin\PostController@indexOfScheduled')->name('post.scheduled');
                    Route::get('post/drafts', 'Admin\PostController@indexOfDrafts')->name('post.draft');
                    Route::get('post/all', 'Admin\PostController@indexOfAll')->name('post.all');
                    Route::get('post/{id}/copy', 'Admin\PostController@copy')->name('post.copy');
                    Route::get('post/{id}/delete', 'Admin\PostController@destroy')
                        ->name('post.destroy');
                    Route::resource('post', 'Admin\PostController');
                }

                // Backend Routes for Standard Events module
                if (config('pilot.plugins.events.enabled', false)) {
                    Route::get('event/scheduled', 'Admin\EventController@indexOfScheduled')->name('event.scheduled');
                    Route::get('event/drafts', 'Admin\EventController@indexOfDrafts')->name('event.draft');
                    Route::get('event/past', 'Admin\EventController@indexOfPast')->name('event.past');
                    Route::get('event/all', 'Admin\EventController@indexOfAll')->name('event.all');
                    Route::get('/event/{id}/copy', 'Admin\EventController@copy')->name('event.copy');
                    Route::resource('event', 'Admin\EventController');
                }

                // Backend Routes for Standard Annoucement module
                if (config('pilot.plugins.annoucements.enabled', false)) {
                    Route::get('activate-annoucement/{id}', 'Admin\AnnoucementController@activate')
                        ->name('annoucement.activate');
                    Route::get('annoucement/{id}/copy', 'Admin\AnnoucementController@copy')->name('annoucement.copy');
                    Route::get('annoucement/{id}/delete', 'Admin\AnnoucementController@destroy')
                        ->name('annoucement.destroy');
                    Route::get('annoucement/deactivate', 'Admin\AnnoucementController@deactivate')
                        ->name('annoucement.deactivate');
                    Route::resource('annoucement', 'Admin\AnnoucementController');
                }

                // Backend Routes for Standard Resources & Resource Category Module
                if (config('pilot.plugins.resources.enabled', false)) {
                    Route::get('resource/{id}/copy', 'Admin\ResourceController@copy')->name('resource.copy');
                    Route::get('resource/{id}/delete', 'Admin\ResourceController@destroy')
                        ->name('resource.destroy');
                    Route::resource('resource', 'Admin\ResourceController');
                    Route::get('resourcecategory/{id}/copy', 'Admin\ResourceCategoryController@copy')->name('resourceCategory.copy');
                    Route::get('resourcecategory/{id}/delete', 'Admin\ResourceCategoryController@destroy')
                        ->name('resourceCategory.destroy');
                    Route::resource('resourcecategory', 'Admin\ResourceCategoryController');
                }

                // Backend Routes for Standard Employees & Departments Module
                if (config('pilot.plugins.employees.enabled', false)) {
                    Route::get('employee/{id}/copy', 'Admin\EmployeeController@copy')->name('employee.copy');
                    Route::get('employee/{id}/delete', 'Admin\EmployeeController@destroy')
                        ->name('employee.destroy');
                    Route::resource('employee', 'Admin\EmployeeController');
                
                    Route::get('department/{id}/copy', 'Admin\DepartmentController@copy')->name('department.copy');
                    Route::get('department/{id}/delete', 'Admin\DepartmentController@destroy')
                        ->name('department.destroy');
                    Route::get('/department-employees/{id}/staffers', 'Admin\DepartmentController@staffMembers')->name('department.staff');
                    Route::post('/department/reorderDepartments', 'Admin\DepartmentController@reorderDepartments')->name('department.reorder');
                    Route::post('/department-employees/{id}/staffers/reorderStaffWithinDepartments', 'Admin\DepartmentController@reorderStaffWithinDepartment')->name('departmentStaff.reorder');
                    Route::resource('department', 'Admin\DepartmentController');
                }

                // Backend Routes for Standard Testimonials Module
                if (config('pilot.plugins.testimonials.enabled', false)) {
                    Route::get('testimonial/{id}/copy', 'Admin\TestimonialController@copy')->name('testimonial.copy');
                    Route::get('testimonial/{id}/delete', 'Admin\TestimonialController@destroy')
                        ->name('testimonial.destroy');
                    Route::resource('testimonial', 'Admin\TestimonialController');
                }

                // Backend Routes for Standard FAQ & FAQ Categories Modules
                if (config('pilot.plugins.faqs.enabled', false)) {
                    Route::get('/faqcategory/{id}/faqs', 'Admin\FaqCategoryController@faqs')
                        ->name('faqcategory.faqs');
                    Route::post(
                        '/faqcategory/{faqcategory}/faqs/reorderFaqsWithinCategory',
                        'Admin\FaqCategoryController@reorderFaqsWithinCategory'
                    )->name('faqcategoryFaqs.reorder');
                    Route::resource('faqcategory', 'Admin\FaqCategoryController');
                    Route::get('/faq/{id}/copy', 'Admin\FaqController@copy')
                        ->name('faq.copy');
                    Route::get('/faq/{id}/delete', 'Admin\FaqController@destroy')
                        ->name('faq.destroy');
                    Route::resource('faq', 'Admin\FaqController');
                }

                // Backend Routes for Standard Product & Product Categories Modules
                if (config('pilot.plugins.products.enabled', false)) {
                    Route::get('/productcategory/{id}/products', 'Admin\ProductCategoryController@products')
                        ->name('productcategory.products');
                    Route::post(
                        '/product-category-products/{productcategory}/products/reorderProductsWithinCategory',
                        'Admin\ProductCategoryController@reorderProductsWithinCategory'
                    )->name('productcategoryProducts.reorder');
                    Route::get('/productcategory/{id}/copy', 'Admin\ProductCategoryController@copy')
                        ->name('productcategory.copy');
                    Route::resource('productcategory', 'Admin\ProductCategoryController');

                    Route::get('/product/{id}/copy', 'Admin\ProductController@copy')
                        ->name('product.copy');
                    Route::get('/product/{id}/delete', 'Admin\ProductController@destroy')
                        ->name('product.destroy');
                    Route::resource('product', 'Admin\ProductController');
                }

                // Backend Routes for Routes for Settings module
                Route::resource('setting', 'Admin\SettingController');
                Route::get('setting/{setting}', 'Admin\SettingController@settings')->name('setting.default');

                Route::resource('tag', 'Admin\TagController');

                Route::resource('block', 'Admin\BlockController');

                Route::resource('pagetype', 'Admin\PageTypeController');

                Route::post('/menu/{id}/reorder', 'Admin\MenuController@reorder')->name('menu.reorder');
                Route::get('/menu/{id}/items', 'Admin\MenuController@items')->name('menu.items');
                Route::resource('menu', 'Admin\MenuController');

                Route::get('/style', 'Admin\StyleController@index')->name('style.index');

                if (config('pilot.plugins.forms.enabled', false)) {
                    Route::get('/form', 'Admin\FormController@index')->name('form.index');
                    Route::get('/form/{hash}/configuration', 'Admin\FormController@configuration')
                        ->name('form.configuration');
                    Route::get('/form/{hash}/entries', 'Admin\FormController@entries')->name('form.entries');
                    Route::post('/form/{hash}/sync', 'Admin\FormController@sync')->name('form.sync');
                    Route::get('/form/{hash}/entry/{id}', 'Admin\FormController@entry')->name('form.entry');
                }

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

            /********************************************************
             *          END OF BACKEND ROUTES,                      *
             *          BEGIN FRONTEND ROUTES                       *
             *                                                      *
             ********************************************************/

            // Route::post('/webhook/wufoo/{hash}', ['as' => 'form.webhook', 'uses' => 'Admin\FormController@webhook']);

            // - note that all pilot module routes must be wrapped in the pilot.module middleware to check if this route
            // should be available on the frontend
            Route::group(['middleware' => []], function () {
                // blog frontend routes
                if (config('pilot.plugins.forms.enabled', false)) {
                    Route::get('/news', 'BlogController@index')->name('blog');
                    Route::get('/news/post/{id}/{slug}', 'BlogController@post')->name('blog.post');
                    Route::get('/news/tagged/{id}/{slug}', 'BlogController@tagged')->name('blog.tagged');
                    Route::get('/rss.xml', 'BlogController@rss')->name('rss');

                    //Blog Routes for 'Load More Post' Button
                    Route::get('/load-more-news', 'BlogController@loadMorePostIntoIndex')->name('blog.more');
                    Route::get('/load-more-tagged-news/{id}/{slug}', 'BlogController@loadMorePostIntoTagged')
                        ->name('blog.moreTagged');
                }

                // calendar frontend routes
                if (config('pilot.plugins.events.enabled', false)) {
                    Route::get('/calendar', 'CalendarController@index')->name('calendar');
                    Route::get('/calendar/month', 'CalendarController@month')->name('calendar.month');
                    Route::get('/calendar/json', 'CalendarController@json')->name('calendar.json');
                    Route::get('/calendar/event/{id}/{slug}', 'CalendarController@event')->name('calendar.event');
                    Route::get('/calendar/tagged/{id}/{slug}', 'CalendarController@tagged')->name('calendar.tagged');
                }

                // resource frontend routes
                if (config('pilot.plugins.resources.enabled', false)) {
                    Route::get('/resources', 'ResourceController@index')->name('resource.index');
                }

                // employee frontend routes
                if (config('pilot.plugins.employees.enabled', false)) {
                    Route::get('/employees', 'EmployeeController@index')->name('employee.index');
                    Route::get('/department/{department}/{slug}', 'EmployeeController@departmentLandingPage')->name('department.index');
                }

                // testimonial frontend routes
                if (config('pilot.plugins.testimonials.enabled', false)) {
                    Route::get('/testimonials', 'TestimonialController@index')->name('testimonial.index');
                }

                // faq frontend routes
                if (config('pilot.plugins.faqs.enabled', false)) {
                    Route::get('/faqs', 'FaqController@index')->name('faq.index');
                    Route::get('/faqs/{faq}/{slug}', 'FaqController@detail')->name('faq.detail');
                }

                // product frontend routes
                if (config('pilot.plugins.products.enabled', false)) {
                    Route::get('/products', 'ProductController@index')->name('product.index');
                    Route::get('/products/{id}/{slug}', 'ProductController@categoryIndex')->name('productCategory.index');
                    Route::get('/product/{id}/{slug}', 'ProductController@detail')->name('product.detail');
                }
            });

            // sitemap routes
            Route::get('/sitemap', 'SitemapController@index')->name('sitemap');
            Route::get('/sitemap.xml', 'SitemapController@xml')->name('sitemap.xml');

            //wufoo confirmation page
            Route::post('/wufoo/{hash}/confirm', 'WufooInterceptorController@confirm')->name('wufoo.confirm');

            Route::post('/page/auth', 'SiteController@pageAuth')->name('page.auth');

            Route::post('/form-handler', 'FormController@handler')->name('form.handler');
        });
    }

    public static function routesAfter()
    {
        Route::middleware(['web', 'pilot.global'])->namespace('\Flex360\Pilot\Http\Controllers')->group(function () {
            Route::get('{path?}', ['as' => 'pages',  'uses' => 'SiteController@view'])->where('path', '.+');
        });
    }
}
