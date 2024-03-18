<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;


Route::namespace('API')->group(function () {

    Route::controller('AuthController')->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::controller('AuthController')->group(function () {
            Route::post('logout', 'logout');
        });

        Route::controller('UserController')->prefix('user')->group(function () {
            Route::post('list', 'list');
            Route::get('profile/{id}', 'profile');
            Route::get('delete/{id}', 'delete');
            Route::post('statusChange', 'statusChange');
        });

        Route::controller('PostController')->prefix('post')->group(function () {
            Route::get('list', 'index');
            Route::post('create', 'create');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::post('destroy/{id}', 'destroy');
            Route::get('likeUnlike/{id}', 'likeUnlike');
            Route::post('commentAdd', 'commentAdd');
            Route::post('commentUpdate', 'commentUpdate');
            Route::post('commentDelete/{id}', 'commentDelete');
            Route::post('report', 'report');
        });
    });
});
