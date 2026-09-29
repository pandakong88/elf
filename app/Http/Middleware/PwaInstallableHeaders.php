<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Override Laravel default no-store cache headers for public PWA pages.
 * Chrome requires pages to NOT have Cache-Control: no-store to be installable as PWA.
 */
class PwaInstallableHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only override on GET requests that return HTML (not AJAX/Livewire/API)
        if (
            $request->isMethod('GET') &&
            !$request->ajax() &&
            !$request->wantsJson() &&
            str_contains($response->headers->get('Content-Type', ''), 'text/html')
        ) {
            $response->headers->set('Cache-Control', 'public, max-age=0, must-revalidate');
            $response->headers->remove('Pragma');
        }

        return $response;
    }
}