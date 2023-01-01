<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;


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

Route::group(['prefix' => 'v1'], function() {
    //authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::group(['middleware'=>'auth:api'], function(){
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    //article resource
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::get('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');

    //When no matching route found.
    Route::fallback(function() {
        return response()->json(['message' => 'Resource not found'], Response::HTTP_FORBIDDEN);
    });
});


//For site health check.
Route::get('/ping', function () {
    return response()->json(['message' => 'pong'], Response::HTTP_OK);
});

