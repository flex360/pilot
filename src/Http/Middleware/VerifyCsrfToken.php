<?php

namespace Flex360\Pilot\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'pilot/tag',
        'pilot/tag/*',
        'pilot/page/*',
        'pilot/event/*',
        'pilot/post/*',
        'pilot/resource/*',
        'pilot/resourcecategory/*',
        'pilot/employee/*',
        'pilot/department/*',
        'pilot/department-employees/*',
        'pilot/page/reorder',
        'pilot/menu/*',
        'assets/upload',
        'webhook/*',
    ];

    protected function addCookieToResponse($request, $response)
    {
        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                time() + 60 * 120,
                '/',
                null,
                config('session.secure'),
                true
            )
        );

        return $response;
    }
}
