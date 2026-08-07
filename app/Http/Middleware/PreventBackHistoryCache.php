<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops the browser from serving cached copies of authenticated pages via
 * the back/forward button after logout - without this, Cache-Control
 * defaults to cacheable and the browser can restore the previous page
 * straight from disk/bfcache without ever asking the server, so the
 * server-side session check in the `auth` middleware never gets a chance
 * to run and redirect to login.
 */
class PreventBackHistoryCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
