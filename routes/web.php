<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/rides/search', 'search')->name('rides.search');
    Route::get('/rides/publish', 'publishRide')->name('rides.publish');
    Route::get('/login', 'login')->name('login');
    Route::get('/signup', 'signup')->name('signup');
    Route::get('/dashboards/admin', 'adminDashboard')->name('dashboards.admin');
    Route::get('/dashboards/driver', 'driverDashboard')->name('dashboards.driver');
    Route::get('/dashboards/traveler', 'travelerDashboard')->name('dashboards.traveler');
    Route::get('/rides/{ride}', 'showRide')->name('rides.show');
});
