<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'en';
        app()->setLocale($local);
        return $next($request);
    }
}
