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
        $resourceRoutes = ['resource.index'];
        $employeeRoutes = ['employee.index'];
        $departmentRoutes = ['department.index'];
        $testimonialRoutes = ['testimonial.index'];
        $faqRoutes  = ['faq.index', 'faq.detail'];
        $productRoutes  = ['product.index', 'productCategory.index', 'product.detail'];
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

        // Resources module check
        if (in_array($currentRouteName, $resourceRoutes)) {
            if (!config('pilot.plugins')['resources']['enabled'] === true){
                abort(404);
            }
        }

        // Employee module check
        if (in_array($currentRouteName, $employeeRoutes)) {
            if (!config('pilot.plugins')['employees']['enabled'] === true){
                abort(404);
            }
        }

        // Department module check
        if (in_array($currentRouteName, $departmentRoutes)) {
            if (!config('pilot.plugins')['employees']['children']['departments']['enabled'] === true){
                abort(404);
            }
        }

        // Testimonial module check
        if (in_array($currentRouteName, $testimonialRoutes)) {
            if (!config('pilot.plugins')['testimonials']['enabled'] === true){
                abort(404);
            }
        }

        // Faq module check
        if (in_array($currentRouteName, $faqRoutes)) {
            if (!config('pilot.plugins')['faqs']['enabled'] === true){
                abort(404);
            }
        }

        // Product module check
        if (in_array($currentRouteName, $productRoutes)) {
            if (!config('pilot.plugins')['products']['enabled'] === true){
                abort(404);
            }
        }

        return $next($request);
    }
}
