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


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::get('/401', 'AuthController@unauthorized')->name('login');

    Route::post('/auth/login', 'AuthController@login');
    Route::post('/user', 'UserController@create');
    Route::post('/auth/logout', 'AuthController@logout');
    Route::post('/auth/refresh', 'AuthController@refresh');

    Route::put('/user', 'UserController@update');
    Route::post('/user/avatar', 'UserController@updateAvatar');
    Route::put('/user/cover', 'UserController@updateCover');

    Route::get('/feed', 'FeedController@read');
    Route::get('/user/feed', 'FeedController@userFeed');
    Route::get('/user/{id}/feed', 'FeedController@userFeed');

    Route::get('/user', 'UserController@read');
    Route::get('/user/{id}', 'UserController@read');

    Route::post('/feed', 'FeedController@create');

    Route::post('/post/{id}/like', 'PostController@like');
    Route::post('/post/{id}/comment', 'PostController@comment');

    Route::get('/search', 'SearchController@search');
});

