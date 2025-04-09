<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.', 'middleware' => ['api']], function () {

    Route::get('/test', function () {
        return response()->json(['message' => 'Hello, World!']);
    })->name('test');
});
