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
    //Route::group(['middleware' => 'tokenEnsure'], function() {
        
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        Route::group(['middleware' => 'auth:api'], function() {
            Route::post('/comment', [CommentController::class, 'store']);


            Route::get('/all', [PostController::class, 'index']);
            Route::get('/log-all', [PostController::class, 'loggedInPosts']);
            Route::post('/', [PostController::class, 'store']);
            Route::post('/search', [PostController::class, 'search']);
            Route::post('/like', [PostController::class, 'likePost']);
            Route::post('/{id}', [PostController::class, 'update']);
            Route::get('/{id}', [PostController::class, 'show']);
            Route::delete('/{id}', [PostController::class, 'destroy']);
            
            
           // Route::post('post/', [UserController::class, 'postData']);
           // Route::get('post/', [UserController::class, 'postData']);
        });


   // });
});
