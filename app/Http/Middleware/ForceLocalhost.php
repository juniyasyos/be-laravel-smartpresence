<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceLocalhost
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->header('host', ''), '127.0.0.1')) {
            $newUrl = str_replace('127.0.0.1', 'localhost', $request->fullUrl());
            return redirect($newUrl);
        }
        return $next($request);
    }
}
