<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::put('/user/update', [UserController::class, 'update']);

    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::post('/user/cover', [UserController::class, 'updateCover']);

    Route::get('/feed', [FeedController::class, 'index']);
    Route::post('/feed', [FeedController::class, 'create']);

    Route::get('/user/feed', [FeedController::class, 'userFeed']);
    Route::get('/user/{id?}/feed', [FeedController::class, 'userFeed']);

    Route::get('/user', [UserController::class, 'read']);
    Route::get('/user/{id?}', [UserController::class, 'read']);

    Route::post('/post/{id}/like', [PostController::class, 'like']);
    Route::post('/post/{id}/comment', [PostController::class, 'comment']);



});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/user', [UserController::class, 'create']);
Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');



