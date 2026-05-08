<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AdminWorkflowController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DriverOnboardingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\RideWorkflowController;
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

Route::middleware(['guest', 'throttle:6,1'])->controller(RegisteredUserController::class)->group(function () {
    Route::post('/signup', 'store')->name('signup.store');
});

Route::middleware('auth')->controller(AuthenticatedSessionController::class)->group(function () {
    Route::post('/logout', 'destroy')->name('logout');
});

Route::middleware(['auth', 'active'])->controller(AccountSettingsController::class)->group(function () {
    Route::get('/account/settings', 'edit')->name('account.settings.edit');
    Route::patch('/account/settings/profile', 'updateProfile')->name('account.settings.profile.update');
    Route::patch('/account/settings/password', 'updatePassword')->name('account.settings.password.update');
});

Route::middleware(['auth', 'active', 'role:driver'])->controller(RideWorkflowController::class)->group(function () {
    Route::post('/rides/publish', 'store')->name('rides.publish.store');
    Route::patch('/rides/{ride}/complete', 'completeRide')->name('rides.complete');
    Route::patch('/bookings/{booking}/confirm', 'confirmBooking')->name('bookings.confirm');
    Route::patch('/bookings/{booking}/reject', 'rejectBooking')->name('bookings.reject');
});

Route::middleware(['auth', 'active', 'role:traveler'])->controller(RideWorkflowController::class)->group(function () {
    Route::post('/rides/{ride}/book', 'book')->name('rides.book');
    Route::patch('/bookings/{booking}/cancel', 'cancelBooking')->name('bookings.cancel');
    Route::post('/bookings/{booking}/reviews', 'reviewBooking')->name('bookings.reviews.store');
});

Route::middleware(['auth', 'active', 'role:traveler'])->controller(DriverOnboardingController::class)->group(function () {
    Route::get('/drivers/onboarding', 'create')->name('drivers.onboarding.create');
    Route::post('/drivers/onboarding', 'store')->name('drivers.onboarding.store');
});

Route::middleware(['auth', 'active', 'role:admin'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/admin', 'adminDashboard')->name('dashboards.admin');
    Route::get('/dashboards/admin/driver-verification', 'adminDriverVerification')->name('dashboards.admin.driver-verification');
    Route::get('/dashboards/admin/users', 'adminUsers')->name('dashboards.admin.users');
    Route::get('/dashboards/admin/rides', 'adminRideActivity')->name('dashboards.admin.rides');
});

Route::middleware(['auth', 'active', 'role:admin'])->controller(AdminWorkflowController::class)->group(function () {
    Route::patch('/admin/driver-profiles/{driverProfile}/verify', 'verifyDriverProfile')->name('admin.driver-profiles.verify');
    Route::get('/admin/driver-profiles/{driverProfile}/cin/{side}', 'showDriverProfileCinPhoto')
        ->whereIn('side', ['front', 'back'])
        ->name('admin.driver-profiles.cin');
});

Route::middleware(['auth', 'active', 'role:driver'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/driver', 'driverDashboard')->name('dashboards.driver');
});

Route::middleware(['auth', 'active', 'role:traveler'])->controller(FrontendController::class)->group(function () {
    Route::get('/dashboards/traveler', 'travelerDashboard')->name('dashboards.traveler');
});
