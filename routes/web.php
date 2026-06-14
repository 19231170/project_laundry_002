<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\Pos\PosAuthController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Web\LaporanWebController;
use App\Http\Controllers\Web\LayananWebController;
use App\Http\Controllers\Web\PelangganWebController;
use App\Http\Controllers\Web\TransaksiWebController;
use App\Http\Controllers\Web\UserPinController;
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
    Route::put('transaksi/bulk-action', [TransaksiWebController::class, 'bulkAction'])->name('transaksi.bulk-action');
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
    Route::put('transaksi/{transaksi}/mark-as-paid', [TransaksiWebController::class, 'markAsPaid'])->name('transaksi.mark-as-paid');

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

    // Web API routes (using session authentication for JavaScript fetch)
    Route::prefix('web-api')->name('web-api.')->group(function () {
        Route::apiResource('pengeluaran', PengeluaranController::class);
        Route::apiResource('kategori-pengeluaran', KategoriPengeluaranController::class);
        Route::apiResource('supplier', SupplierController::class);
        Route::apiResource('inventaris', InventarisController::class);
        Route::apiResource('pelanggan', PelangganController::class);
        Route::apiResource('layanan', LayananController::class);
        Route::apiResource('transaksi', TransaksiController::class);

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
            Route::get('export/pembulatan', [LaporanController::class, 'exportPembulatan']);
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes for PIN management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/pin-management', [UserPinController::class, 'index'])->name('pin-management');
        Route::put('/users/{user}/pin', [UserPinController::class, 'updatePin'])->name('users.update-pin');
        Route::delete('/users/{user}/pin', [UserPinController::class, 'removePin'])->name('users.remove-pin');
        Route::put('/users/{user}/toggle-admin', [UserPinController::class, 'toggleAdmin'])->name('users.toggle-admin');
    });
});

/*
|--------------------------------------------------------------------------
| POS Routes (Separate PIN Authentication)
|--------------------------------------------------------------------------
*/

// POS Login (no auth required - uses PIN)
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/login', [PosAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/verify-pin', [PosAuthController::class, 'verifyPin'])->name('verify-pin');
    Route::post('/logout', [PosAuthController::class, 'logout'])->name('logout');
});

// POS Protected Routes (requires POS session)
Route::prefix('pos')->name('pos.')->middleware('pos.session')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/search-pelanggan', [PosController::class, 'searchPelanggan'])->name('search-pelanggan');
    Route::post('/store-pelanggan', [PosController::class, 'storePelanggan'])->name('store-pelanggan');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
    Route::get('/receipt/{transaksi}', [PosController::class, 'receipt'])->name('receipt');
});

require __DIR__.'/auth.php';
