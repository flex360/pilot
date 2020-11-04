<?php

namespace Flex360\Pilot\Http\Middleware;

use Closure;

class PilotModuleEnabledMiddleware
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
        // get currentRouteName for this request
        $currentRouteName = \Request::route()->getName();

        // create arrays of routeNames for each Pilot module to check if Current route is equal to them
        $blogRouteNames = ['blog', 'blog.post', 'blog.tagged', 'rss', 'blog.more', 'blog.moreTagged'];
        $eventRoutes = ['calendar', 'calendar.month', 'calendar.json', 'calendar.event', 'calendar.tagged'];
        // if the current route is a Pilot module route, but that module is disabled,
        // abort this request and return 404 response
        
        // News module check
        if (in_array($currentRouteName, $blogRouteNames)) {
            if (!config('pilot.plugins')['news']['enabled'] === true){
                abort(404);
            }
        }

        // Events module check
        if (in_array($currentRouteName, $eventRoutes)) {
            if (!config('pilot.plugins')['events']['enabled'] === true){
                abort(404);
            }
        }

        return $next($request);
    }
}
