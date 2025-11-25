<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', LoginController::class)->name('login');
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::put('/change-password', ChangePasswordController::class)->name('change-password');
        });
    });
});
