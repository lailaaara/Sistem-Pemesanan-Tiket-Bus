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
Route::post('/booking/process',  [BookingController::class, 'process'])->name('booking.process');
Route::get('/booking/success',   [BookingController::class, 'success'])->name('booking.success');

// ─── Kelola Tiket Saya (Fase 3) ───────────────────────────────────────
Route::get('/tickets',           [BookingController::class, 'ticketsIndex'])->name('booking.tickets');
Route::post('/tickets/search',   [BookingController::class, 'searchTicket'])->name('booking.tickets_search');
Route::get('/tickets/{id}',      [BookingController::class, 'ticketsDetail'])->name('booking.tickets_detail');

// ─── Autentikasi (Admin Login) ──────────────────────────────────────────
Route::get('/login',             [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login',            [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout',           [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ─── Panel Admin (Fase 4) ─────────────────────────────────────────────
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/',              [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // ─── Operasional (Bus & Jadwal)
    Route::get('/operasional',   [AdminController::class, 'operasional'])->name('admin.operasional');
    
    // ─── Bus Routes
    Route::get('/operasional/tambah-bus', [AdminController::class, 'tambahBus'])->name('admin.tambah_bus');
    Route::post('/operasional/store-bus', [AdminController::class, 'storeBus'])->name('admin.store_bus');
    Route::get('/operasional/edit-bus/{id}', [AdminController::class, 'editBus'])->name('admin.edit_bus');
    Route::put('/operasional/update-bus/{id}', [AdminController::class, 'updateBus'])->name('admin.update_bus');
    Route::delete('/operasional/delete-bus/{id}', [AdminController::class, 'destroyBus'])->name('admin.destroy_bus');
    Route::delete('/operasional/force-delete-bus/{id}', [AdminController::class, 'forceDestroyBus'])->name('admin.force_destroy_bus');
    Route::post('/operasional/restore-bus/{id}', [AdminController::class, 'restoreBus'])->name('admin.restore_bus');
    
    // ─── Jadwal Routes
    Route::get('/operasional/tambah-jadwal', [AdminController::class, 'tambahJadwal'])->name('admin.tambah_jadwal');
    Route::post('/operasional/store-jadwal', [AdminController::class, 'storeJadwal'])->name('admin.store_jadwal');
    Route::get('/operasional/edit-jadwal/{id}', [AdminController::class, 'editJadwal'])->name('admin.edit_jadwal');
    Route::put('/operasional/update-jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('admin.update_jadwal');
    Route::delete('/operasional/delete-jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('admin.destroy_jadwal');
    Route::delete('/operasional/force-delete-jadwal/{id}', [AdminController::class, 'forceDestroyJadwal'])->name('admin.force_destroy_jadwal');
    Route::post('/operasional/restore-jadwal/{id}', [AdminController::class, 'restoreJadwal'])->name('admin.restore_jadwal');
    
    // ─── Rute Routes
    Route::get('/operasional/tambah-rute', [AdminController::class, 'tambahRute'])->name('admin.tambah_rute');
    Route::post('/operasional/store-rute', [AdminController::class, 'storeRute'])->name('admin.store_rute');
    Route::get('/operasional/edit-rute/{id}', [AdminController::class, 'editRute'])->name('admin.edit_rute');
    Route::put('/operasional/update-rute/{id}', [AdminController::class, 'updateRute'])->name('admin.update_rute');
    Route::delete('/operasional/delete-rute/{id}', [AdminController::class, 'destroyRute'])->name('admin.destroy_rute');
    
    Route::get('/transaksi',     [AdminController::class, 'transaksi'])->name('admin.transaksi');
    Route::get('/laporan',       [AdminController::class, 'laporan'])->name('admin.laporan');
});


