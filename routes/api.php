<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\CommentsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);


// protected route
Route::group(['middleware'=>['auth:sanctum']], function(){
    //username
    Route::get('/logout',[AuthController::class, 'logout']);

    Route::get('/user',[AuthController::class, 'me']);
    Route::put('/user',[AuthController::class, 'update']);


    //postsController
    Route::get('/posts',[PostsController::class, 'index']);
    Route::post('/posts',[PostsController::class, 'store']);
    Route::get('/posts/{id}',[PostsController::class, 'show']);
    Route::put('/posts/{id}',[PostsController::class, 'update']);
    Route::delete('/posts/{id}',[PostsController::class, 'destroy']);

    //comments
    Route::get('/posts/{id}/comments',[CommentsController::class, 'index']);
    Route::post('/posts/{id}/comments',[CommentsController::class, 'store']);
    Route::put('/comments/{id}',[CommentsController::class, 'update']);
    Route::delete('/comments/{id}',[CommentsController::class, 'destroy']);

    // likes
    Route::post('/posts/{id}/likes',[LikesController::class, 'likeOrUnlike']);

});
