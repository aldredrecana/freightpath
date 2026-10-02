<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn () => redirect()->route(Auth::check() ? 'dashboard' : 'login'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('drivers', DriverController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'role:admin,dispatcher'])
    ->get('/drivers', [DriverController::class, 'index'])
    ->name('drivers.index');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('trucks', TruckController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'role:admin,dispatcher'])
    ->get('/trucks', [TruckController::class, 'index'])
    ->name('trucks.index');

Route::middleware(['auth', 'role:admin,dispatcher'])->group(function () {
    Route::resource('schedules', ScheduleController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])
        ->whereNumber('schedule')->name('schedules.show');
    Route::post('/schedules/{schedule}/status', [ScheduleController::class, 'updateStatus'])
        ->whereNumber('schedule')->name('schedules.status');

    Route::get('/my-deliveries', [ScheduleController::class, 'mine'])
        ->middleware('role:driver')->name('schedules.mine');
});