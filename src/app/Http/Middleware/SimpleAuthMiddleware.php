<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // セッションに 'is_authenticated' がなければ認証画面へ飛ばす
        if (!$request->session()->has('is_authenticated')) {
            // ただし、認証処理自体のURLは除外しないと無限ループになる
            if (!$request->is('login-gate*')) {
                return redirect()->route('sinrinbo.login');
            }
        }

        return $next($request);
    }
}
