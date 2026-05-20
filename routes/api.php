<?php

use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\BookingController;
use App\Http\Controllers\Api\Mobile\CityController;
use App\Http\Controllers\Api\Mobile\RideController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->name('api.mobile.')->group(function () {
    Route::post('/signup', [AuthController::class, 'register'])->name('signup');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/rides', [RideController::class, 'index'])->name('rides.index');
    Route::get('/rides/{ride}', [RideController::class, 'show'])->name('rides.show');

    Route::middleware('mobile.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('mobile.role:traveler')->group(function () {
            Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
            Route::post('/rides/{ride}/book', [BookingController::class, 'store'])->name('rides.book');
            Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        });
    });
});
