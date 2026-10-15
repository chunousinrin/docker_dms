<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        abort_unless(
            auth()->check() &&
                auth()->user()->isAdmin(),
            403
        );

        return $next($request);
    }
}
