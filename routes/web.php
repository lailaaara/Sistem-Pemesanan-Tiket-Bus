<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Alur Pemesanan (Booking Flow) ───────────────────────────────────
Route::get('/search',            [BookingController::class, 'search'])->name('booking.search');
Route::get('/booking/seat',      [BookingController::class, 'seat'])->name('booking.seat');
Route::get('/booking/checkout',  [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/booking/success',   [BookingController::class, 'success'])->name('booking.success');

// ─── Kelola Tiket Saya (Fase 3) ───────────────────────────────────────
Route::get('/tickets',           [BookingController::class, 'ticketsIndex'])->name('booking.tickets');
Route::get('/tickets/{id}',      [BookingController::class, 'ticketsDetail'])->name('booking.tickets_detail');

// ─── Panel Admin (Fase 4) ─────────────────────────────────────────────
Route::prefix('admin')->group(function () {
    Route::get('/',              [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/operasional',   [AdminController::class, 'operasional'])->name('admin.operasional');
    Route::get('/operasional/tambah', [AdminController::class, 'tambahJadwal'])->name('admin.tambah_jadwal');
    Route::get('/transaksi',     [AdminController::class, 'transaksi'])->name('admin.transaksi');
    Route::get('/laporan',       [AdminController::class, 'laporan'])->name('admin.laporan');
});


