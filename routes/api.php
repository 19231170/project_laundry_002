<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes (untuk backend API)
Route::prefix('v1')->group(function () {
    // Pelanggan routes
    Route::apiResource('pelanggan', PelangganController::class);
    
    // Layanan routes
    Route::apiResource('layanan', LayananController::class);
    
    // Transaksi routes
    Route::apiResource('transaksi', TransaksiController::class);
    Route::get('transaksi/{id}/struk', [TransaksiController::class, 'generateStruk']);
    
    // Laporan routes
    Route::prefix('laporan')->group(function () {
        Route::get('dashboard', [LaporanController::class, 'getDashboardStats']);
        Route::get('pemasukan-pengeluaran', [LaporanController::class, 'getLaporanPemasukanPengeluaran']);
        Route::get('layanan-terlaris', [LaporanController::class, 'getLaporanLayananTerlaris']);
        
        // Export routes
        Route::get('export/transaksi', [LaporanController::class, 'exportTransaksi']);
        Route::get('export/pemasukan-pengeluaran', [LaporanController::class, 'exportPemasukanPengeluaran']);
        
        // Import routes
        Route::post('import/transaksi', [LaporanController::class, 'importTransaksi']);
    });
});
