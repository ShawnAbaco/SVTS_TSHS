<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->is('adviser/*')) {
                return route('login');
            }

            if ($request->is('prefect/*')) {
                return route('login');
            }

            return route('login'); // fallback
        }

        return null;
    }
}
