<?php

namespace Flex360\Pilot\Providers;

use Flex360\Pilot\Pilot\Tag;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Post;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\User;
use Flex360\Pilot\Pilot\View;
use Flex360\Pilot\Pilot\Asset;
use Flex360\Pilot\Pilot\Event;
use Flex360\Pilot\Pilot\Setting;
use Flex360\Pilot\Pilot\PageType;
use Flex360\Pilot\Pilot\UrlHelper;
use Illuminate\Support\ServiceProvider;
use Flex360\Pilot\Console\Commands\SyncForms;
use Flex360\Pilot\Pilot\Forms\Wufoo\WufooForm;
use Flex360\Pilot\Console\Commands\PilotTakeoff;
use Flex360\Pilot\Console\Commands\PilotMakeUser;
use Flex360\Pilot\Console\Commands\SyncNewsFeeds;
use Flex360\Pilot\Http\Middleware\BeforeMiddleware;
use Flex360\Pilot\Http\Middleware\AuthenticateAdmin;
use Flex360\Pilot\Http\Middleware\BeforeBackendMiddleware;

class PilotServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish configurations
        $this->publishes([
            __DIR__ . '/../../config/pilot.php' => config_path('pilot.php'),
            __DIR__ . '/../../config/settings.php' => config_path('settings.php'),
            __DIR__ . '/../../config/medialibrary.php' => config_path('medialibrary.php'),
            __DIR__ . '/../../config/ignition.php' => config_path('ignition.php'),
        ], 'pilot-config');

        // publish assets
        $this->publishes([
            __DIR__.'/../../dist' => public_path('pilot-assets'),
        ], 'pilot-public');

        // publish template files
        $this->publishes([
            __DIR__.'/../../resources/views/layouts/template.blade.php' => base_path('resources/views/layouts/template.blade.php'),
            __DIR__.'/../../resources/views/layouts/home.blade.php' => base_path('resources/views/layouts/home.blade.php'),
            __DIR__.'/../../resources/views/layouts/internal.blade.php' => base_path('resources/views/layouts/internal.blade.php'),
            __DIR__.'/../../resources/views/partials/header.blade.php' => base_path('resources/views/partials/header.blade.php'),
            __DIR__.'/../../resources/views/partials/footer.blade.php' => base_path('resources/views/partials/footer.blade.php'),
            __DIR__.'/../../resources/views/page.blade.php' => base_path('resources/views/vendor/pilot/page.blade.php'),
        ], 'pilot-templates');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'pilot');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PilotMakeUser::class,
                PilotTakeoff::class,
                SyncForms::class,
                SyncNewsFeeds::class,
            ]);
        }

        $router = $this->app['router'];

        $router->aliasMiddleware('auth.admin', AuthenticateAdmin::class);
        $router->aliasMiddleware('backend', BeforeBackendMiddleware::class);
        $router->pushMiddlewareToGroup('pilot.global', BeforeMiddleware::class);

        class_alias(UrlHelper::class, 'UrlHelper');
        class_alias(Asset::class, 'PilotAsset');
        class_alias(WufooForm::class, 'WufooForm');
        class_alias(Page::class, 'PilotPage');
        class_alias(PageType::class, 'PilotPageType');
        class_alias(Event::class, 'PilotEvent');
        class_alias(Setting::class, 'PilotSetting');
        class_alias(Site::class, 'PilotSite');
        class_alias(Post::class, 'PilotPost');
        class_alias(View::class, 'PilotView');
        class_alias(User::class, 'PilotUser');
        class_alias(Tag::class, 'PilotTag');
    }
}
