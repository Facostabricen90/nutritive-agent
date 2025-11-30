<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('SFHJTUFS'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Rutas de registro encriptadas/secretas
Route::get('/SFHJTUFS', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'create'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('SFHJTUFS.view');

Route::post('/SFHJTUFS', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('SFHJTUFS');

Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.available-slots');
Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
});
