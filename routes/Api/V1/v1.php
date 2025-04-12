<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.', 'middleware' => ['api']], function () {

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::apiResource('orders', \App\Http\Controllers\Api\V1\OrderController::class)->only([
            'index',
            'show',
            'store',
            'update',
        ]);
    });
});
