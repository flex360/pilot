<?php

namespace Flex360\Pilot\Http\Middleware;

use Closure;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Site;

class BeforeMiddleware
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
        // check to see is auth is required
        if (Site::needsAuthRedirect()) {
            return redirect()->guest('login');
        }

        // determine the domain and set a config setting
        Site::setDomain();

        // create a site if none exist
        Site::init();

        // set current site in config
        $site = Site::setCurrent();

        // \View::share('page', new \Page);
        Page::mimic();

        // redirect if wrong domain or protocol is used
        if ($site->needsRedirect()) {
            return $site->getRedirect();
        }

        // share site across views
        view()->share('currentSite', $site);

        $response = $next($request);

        // stop development sites from being indexed
        if (env('APP_ENV') !== 'production') {
            $response->header('X-Robots-Tag', 'noindex');
        }

        return $response;
    }
}
