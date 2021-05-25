<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\CommentController;

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
    Route::group(['middleware' => ['executaionTime','localization']], function() {
        
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        Route::group(['middleware' => 'auth:api'], function() {
            Route::apiResource('posts', PostController::class);
            Route::post('/comment', [CommentController::class, 'store']);
            Route::post('/like', [PostController::class, 'likePost']);
            Route::get('/log-all', [PostController::class, 'loggedInPosts']);
            Route::post('/search', [PostController::class, 'search']);
            
            // Route::get('/all', [PostController::class, 'index']);
            // Route::post('/', [PostController::class, 'store']);
            // Route::post('/{id}', [PostController::class, 'update']);
            // Route::get('/{id}', [PostController::class, 'show']);
            // Route::delete('/{id}', [PostController::class, 'destroy']);
           // Route::post('post/', [UserController::class, 'postData']);
           // Route::get('post/', [UserController::class, 'postData']);
        });
    });
});
