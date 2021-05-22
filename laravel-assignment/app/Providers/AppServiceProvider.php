<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Response::macro('succeedResponse', function ($code, $method, $data, $time_start) {
            $time_end = microtime(true);
            $execution_time = round(($time_end - $time_start)*1000, 2);
            return \Response::json([
                'status' => 'success',
                'method' => $method,
                'data' => $data,
                'error' => false,
                'errorMessage' => '',
                'processingTime' => $execution_time .' ms'
            ])->setStatusCode($code);
        });

        \Response::macro('errorResponse', function ($code, $data, $time_start) {
            $time_end = microtime(true);
            $execution_time = round(($time_end - $time_start)*1000, 2);
            return \Response::json([
                'error' => true,
                'message' => $data,
                'processingTime' => $execution_time.' ms'
            ])->setStatusCode($code);
        });
    }
}
