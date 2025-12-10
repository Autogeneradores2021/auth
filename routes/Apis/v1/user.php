<?php

use App\Http\Controllers\User\CodeVerificationController;
use App\Http\Controllers\User\CreateUserController;
use App\Http\Controllers\User\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::post('/', CreateUserController::class)->name('create');
        Route::post('/email-verification', EmailVerificationController::class)->name('email.verify');
        Route::post('/verification-code', CodeVerificationController::class)->name('verification.code');
    });
});
