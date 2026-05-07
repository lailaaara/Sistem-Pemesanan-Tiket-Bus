<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Alur Pemesanan (Booking Flow) ───────────────────────────────────
Route::get('/search',            [BookingController::class, 'search'])->name('booking.search');
Route::get('/booking/seat',      [BookingController::class, 'seat'])->name('booking.seat');
Route::get('/booking/checkout',  [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/booking/success',   [BookingController::class, 'success'])->name('booking.success');
