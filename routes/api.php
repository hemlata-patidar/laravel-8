<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\PhotoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'tokenEnsure'], function() {

        Route::get('/products', [ProductController::class, 'index']);
        Route::get('product/{id}', [ProductController::class, 'show']);
        Route::post('product/', [ProductController::class, 'store']);
        Route::put('product/{id}', [ProductController::class, 'update']);
        Route::delete('product/{id}', [ProductController::class, 'destroy']);
        
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::group(['middleware' => 'auth:api'], function() {
            Route::get('/userdetails', [UserController::class, 'index']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });
    });
});


Route::apiResource('photos', PhotoController::class);
