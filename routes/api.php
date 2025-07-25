<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventarisController;

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

// API routes with authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Pelanggan routes
    Route::apiResource('pelanggan', PelangganController::class);
    
    // Layanan routes
    Route::apiResource('layanan', LayananController::class);
    
    // Transaksi routes
    Route::apiResource('transaksi', TransaksiController::class);
    Route::get('transaksi/{id}/struk', [TransaksiController::class, 'generateStruk']);
    
    // Pengeluaran routes
    Route::apiResource('pengeluaran', PengeluaranController::class);
    Route::apiResource('kategori-pengeluaran', KategoriPengeluaranController::class);
    Route::apiResource('supplier', SupplierController::class);
    Route::apiResource('inventaris', InventarisController::class);
    
    // Laporan routes
    Route::prefix('laporan')->group(function () {
        Route::get('dashboard', [LaporanController::class, 'getDashboardStats']);
        Route::get('pemasukan-pengeluaran', [LaporanController::class, 'getLaporanPemasukanPengeluaran']);
        Route::get('layanan-terlaris', [LaporanController::class, 'getLaporanLayananTerlaris']);
        Route::get('laba-rugi', [LaporanController::class, 'getLaporanLabaRugi']);
        Route::get('penggunaan-bahan', [LaporanController::class, 'getLaporanPenggunaanBahan']);
        Route::get('pengeluaran-per-kategori', [LaporanController::class, 'getLaporanPengeluaranPerKategori']);
        Route::get('pembulatan', [LaporanController::class, 'getLaporanPembulatan']);
        
        // Export routes
        Route::get('export/transaksi', [LaporanController::class, 'exportTransaksi']);
        Route::get('export/pemasukan-pengeluaran', [LaporanController::class, 'exportPemasukanPengeluaran']);
        Route::get('export/laba-rugi', [LaporanController::class, 'exportLabaRugi']);
        Route::get('export/penggunaan-bahan', [LaporanController::class, 'exportPenggunaanBahan']);
        Route::get('export/pengeluaran-per-kategori', [LaporanController::class, 'exportPengeluaranPerKategori']);
        Route::get('export/laporan-harian', [LaporanController::class, 'exportLaporanHarian']);
        Route::get('export/laporan-mingguan', [LaporanController::class, 'exportLaporanMingguan']);
        Route::get('export/laporan-bulanan', [LaporanController::class, 'exportLaporanBulanan']);
        Route::get('export/pembulatan', [LaporanController::class, 'exportPembulatan']);
        
        // Import routes
        Route::post('import/transaksi', [LaporanController::class, 'importTransaksi']);
    });
});
