<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExecutionTime
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
        $response = $next($request);
        $executionTime = microtime(true) - LARAVEL_START;
        $roundTime = round($executionTime, 2);
        $content = json_decode($response->getContent(), true) + ['processingTime' => $roundTime.' ms'];
        $response->setContent(json_encode($content));
        return $response;
    }
}
