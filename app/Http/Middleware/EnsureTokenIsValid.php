<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
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
        $acceptHeader = $request->header('Content-Type');
        if ($acceptHeader == 'application/json') {
            return $next($request);
        }
        else {
            return   response()->json(['message' => 'data Content-Type should be in JSON'], 406);
        }
    }
}
