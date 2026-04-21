<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/rides/search', 'search')->name('rides.search');
    Route::get('/rides/publish', 'publishRide')->name('rides.publish');
    Route::get('/rides/{ride}', 'showRide')->name('rides.show');
});

Route::middleware('guest')->controller(FrontendController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::get('/signup', 'signup')->name('signup');
});

Route::middleware(['guest', 'throttle:6,1'])->controller(AuthenticatedSessionController::class)->group(function () {
    Route::post('/login', 'store')->name('login.store');
});

Route::middleware('auth')->controller(AuthenticatedSessionController::class)->group(function () {
    Route::post('/logout', 'destroy')->name('logout');
});

Route::middleware(['auth', 'role:admin'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/admin', 'adminDashboard')->name('dashboards.admin');
});

Route::middleware(['auth', 'role:driver'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/driver', 'driverDashboard')->name('dashboards.driver');
});

Route::middleware(['auth', 'role:traveler'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/traveler', 'travelerDashboard')->name('dashboards.traveler');
});
