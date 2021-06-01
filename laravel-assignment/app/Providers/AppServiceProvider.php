<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Database\Query\Builder;


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
        \Response::macro('succeedResponse', function ($code, $data) {
            return \Response::json([
                'status' => 'success',
                'data' => $data,
                'error' => false,
                'errorMessage' => '',
            ])->setStatusCode($code);
        });

        \Response::macro('errorResponse', function ($code, $data) {
            return \Response::json([
                'error' => true,
                'message' => $data,
            ])->setStatusCode($code);
        });

        Builder::macro('searchClause', function (string $column, string $search) {
            return where($column, 'LIKE', "%{$search}%")->get();
        });
    }
}
