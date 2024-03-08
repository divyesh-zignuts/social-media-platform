<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::namespace('API')->group(function () {

    Route::controller('AuthController')->group(function () {
        Route::post('login', 'login');
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::controller('AuthController')->group(function () {
            Route::post('logout', 'logout');
        });
    });
});
