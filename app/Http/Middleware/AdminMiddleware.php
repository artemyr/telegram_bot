<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->id()) {
            return redirect(route('home'));
        }

        if (auth()->id() > 0 && auth()->user()->role !== 'admin') {
            return redirect(route('home'));
        }

        return $next($request);
    }
}
