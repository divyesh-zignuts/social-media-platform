<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
            Route::get('edit/{id}', 'edit');
            Route::get('delete/{id}', 'delete');
        });
    });
});
