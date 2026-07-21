<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessAdmin
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canAccessAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke panel admin.');
        }

        foreach ($abilities as $ability) {
            if (! $user->canDo($ability)) {
                abort(403, 'Anda tidak memiliki izin untuk tindakan ini.');
            }
        }

        return $next($request);
    }
}
