<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::now()->startOfDay();

        // Basic stats
        $totalPelanggan = Pelanggan::count();
        $transaksiHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)->count();
        $pendapatanHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_harga');

        // Status transaksi
        $statusTransaksi = Transaksi::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->pluck('jumlah', 'status');
        $transaksiProses = $statusTransaksi['proses'] ?? 0;

        // Payment stats
        $totalBelumLunas = Transaksi::where('status_pembayaran', 'belum_lunas')
            ->sum('sisa_pembayaran');
        $transaksiCount = Transaksi::count();
        $belumLunasCount = Transaksi::where('status_pembayaran', 'belum_lunas')->count();
        $lunasCount = Transaksi::where('status_pembayaran', 'lunas')->count();
        $persentaseLunas = $transaksiCount > 0 ? round(($lunasCount / $transaksiCount) * 100, 2) : 0;

        // Recent transactions
        $transaksiTerbaru = Transaksi::with('pelanggan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPelanggan',
            'transaksiHariIni',
            'pendapatanHariIni',
            'transaksiProses',
            'totalBelumLunas',
            'lunasCount',
            'belumLunasCount',
            'persentaseLunas',
            'transaksiTerbaru'
        ));
    }
}
