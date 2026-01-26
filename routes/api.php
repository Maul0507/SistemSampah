<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InformasiSampahController;
use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PenukaranSaldoController;


/*
|--------------------------------------------------------------------------
| 1. Rute API Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Informasi Sampah (PUBLIC)
Route::get('/informasi-sampah', [InformasiSampahController::class, 'index']);

// ✅ TAMBAHAN ALIAS (UNTUK FLUTTER – JANGAN HAPUS)
Route::get('/jenis-sampah', [InformasiSampahController::class, 'index']);


/*
|--------------------------------------------------------------------------
| 2. Rute API Testing / Admin (Tanpa Login)
|--------------------------------------------------------------------------
*/

// CRUD Sampah
Route::post('/informasi-sampah', [InformasiSampahController::class, 'store']);
Route::post('/informasi-sampah/{id}', [InformasiSampahController::class, 'update']);
Route::delete('/informasi-sampah/{id}', [InformasiSampahController::class, 'destroy']);

// CRUD User
Route::get('/users', [AuthController::class, 'index']);
Route::post('/users', [AuthController::class, 'storeUser']);
Route::put('/users/{id}', [AuthController::class, 'updateUser']);
Route::delete('/users/{id}', [AuthController::class, 'destroyUser']);
Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'me']);
// Transaksi
Route::get('/transaksi/saldo', [TransactionController::class, 'balance']);
Route::get('/transaksi/riwayat', [TransactionController::class, 'history']);
Route::post('/transaksi', [TransactionController::class, 'store']);


/*
|--------------------------------------------------------------------------
| 3. Rute Terproteksi (Login / Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // PERMINTAAN USER
    Route::post('/permintaan-penjemputan', [PermintaanController::class, 'store']);
    Route::get('/permintaan', [PermintaanController::class, 'riwayatUser']);


    Route::post('/penukaran-saldo', [PenukaranSaldoCSontroller::class, 'store']);
    Route::get('/penukaran-saldo/me', [PenukaranSaldoController::class, 'myHistory']);

    Route::get('/penukaran-saldo', [PenukaranSaldoController::class, 'index']);
    
    // Hasil URL: PUT /api/admin/penukaran-saldo/{id}
    Route::put('/penukaran-saldo/{id}', [PenukaranSaldoController::class, 'updateStatus']);

    // PETUGAS / DRIVER
    Route::get('/permintaan/masuk', [PermintaanController::class, 'tugasMasuk']);
    Route::get('/permintaan/tugas-aktif', [PermintaanController::class, 'tugasAktif']);
    Route::get('/permintaan/riwayat-petugas', [PermintaanController::class, 'riwayatPetugas']);
    Route::patch('/permintaan/{id}/update-status', [PermintaanController::class, 'updateStatus']);
    Route::get('/me', [AuthController::class, 'me']);

});
