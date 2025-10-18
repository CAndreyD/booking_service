<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;

Route::get('/', function () {
    return redirect()->route('services.index');
});

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/services/{service}/week-slots', [ServiceController::class, 'weekSlots']);

Route::post('/services/{service}/bookings', [BookingController::class, 'store'])->name('bookings.store');

