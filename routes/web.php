<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

// ==========================================================
// 1. PUBLIC ROUTES (dapat diakses semua pengguna / guest)
// ==========================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog/{buku}', [HomeController::class, 'show'])->name('buku.publik.show');

// Rute autentikasi (hanya untuk pengguna yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (hanya untuk pengguna yang sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================================
// 2. ADMIN ROUTES (khusus pengguna dengan role: admin)
// ==========================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD data buku (dengan fitur upload cover)
    Route::resource('buku', BukuController::class);

    // CRUD data anggota
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);

    // Peminjaman & transaksi pengembalian
    Route::resource('peminjaman', PeminjamanController::class);
    Route::put('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])->name('peminjaman.kembali');
});