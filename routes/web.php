<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\PelangganWebController;
use App\Http\Controllers\Web\LayananWebController;
use App\Http\Controllers\Web\TransaksiWebController;
use App\Http\Controllers\Web\LaporanWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Pelanggan Web Routes
    Route::resource('pelanggan', PelangganWebController::class)->names([
        'index' => 'pelanggan.index',
        'create' => 'pelanggan.create',
        'store' => 'pelanggan.store',
        'show' => 'pelanggan.show',
        'edit' => 'pelanggan.edit',
        'update' => 'pelanggan.update',
        'destroy' => 'pelanggan.destroy',
    ]);
    
    // Layanan Web Routes
    Route::resource('layanan', LayananWebController::class)->names([
        'index' => 'layanan.index',
        'create' => 'layanan.create',
        'store' => 'layanan.store',
        'show' => 'layanan.show',
        'edit' => 'layanan.edit',
        'update' => 'layanan.update',
        'destroy' => 'layanan.destroy',
    ]);
    
    // Transaksi Web Routes
    Route::resource('transaksi', TransaksiWebController::class)->names([
        'index' => 'transaksi.index',
        'create' => 'transaksi.create',
        'store' => 'transaksi.store',
        'show' => 'transaksi.show',
        'edit' => 'transaksi.edit',
        'update' => 'transaksi.update',
        'destroy' => 'transaksi.destroy',
    ]);
    Route::get('transaksi/{id}/struk', [TransaksiWebController::class, 'generateStruk'])->name('transaksi.struk');

    
    // Laporan Web Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanWebController::class, 'index'])->name('index');
        Route::get('harian', [LaporanWebController::class, 'harian'])->name('harian');
        Route::get('bulanan', [LaporanWebController::class, 'bulanan'])->name('bulanan');
        Route::get('export-excel', [LaporanWebController::class, 'exportExcel'])->name('export-excel');
        Route::get('export-pdf', [LaporanWebController::class, 'exportPdf'])->name('export-pdf');
    });
    
    // Expense Management Routes
    Route::get('/pengeluaran', function () {
        return view('pengeluaran.index');
    })->middleware(['auth', 'verified'])->name('pengeluaran');
    
    Route::get('/pengeluaran/kategori', function () {
        return view('pengeluaran.kategori');
    })->middleware(['auth', 'verified'])->name('pengeluaran.kategori');
    
    Route::get('/pengeluaran/supplier', function () {
        return view('pengeluaran.supplier');
    })->middleware(['auth', 'verified'])->name('pengeluaran.supplier');
    
    Route::get('/pengeluaran/inventaris', function () {
        return view('pengeluaran.inventaris');
    })->middleware(['auth', 'verified'])->name('pengeluaran.inventaris');
    
    // Report Routes
    Route::get('/laporan/laba-rugi', function () {
        return view('laporan.laba-rugi');
    })->middleware(['auth', 'verified'])->name('laporan.laba-rugi');
    
    Route::get('/laporan/penggunaan-bahan', function () {
        return view('laporan.penggunaan-bahan');
    })->middleware(['auth', 'verified'])->name('laporan.penggunaan-bahan');
    
    Route::get('/laporan/pengeluaran-kategori', function () {
        return view('laporan.pengeluaran-kategori');
    })->middleware(['auth', 'verified'])->name('laporan.pengeluaran-kategori');

    Route::get('/laporan/pembulatan', function () {
        return view('laporan.pembulatan');
    })->name('laporan.pembulatan');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
