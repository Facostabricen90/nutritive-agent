<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/appointments/calendar', [App\Http\Controllers\AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/available-slots', [App\Http\Controllers\AppointmentController::class, 'getAvailableSlots'])->name('appointments.available-slots');
    Route::patch('/appointments/{appointment}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::resource('appointments', App\Http\Controllers\AppointmentController::class);
});
