<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Api\Auth\AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});
