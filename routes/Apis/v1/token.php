<?php

use App\Http\Controllers\User\VerifyTokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('token')->name('token.')->group(function () {
        Route::post('/verification', VerifyTokenController::class)->name('verify');
    });
});
