<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // CSP realistis untuk Blade + Vite (bukan overly strict yang mematahkan asset)
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; ".
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'; ".
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; ".
                "font-src 'self' https://fonts.bunny.net data:; ".
                "img-src 'self' data: blob: https:; ".
                "connect-src 'self'; ".
                "frame-src 'self' https://www.openstreetmap.org https://maps.google.com https://www.google.com; ".
                "base-uri 'self'; ".
                "form-action 'self'; ".
                "frame-ancestors 'self'",
            );
        }

        return $response;
    }
}
