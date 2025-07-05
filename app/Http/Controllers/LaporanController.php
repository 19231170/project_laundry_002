<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use App\Exports\PemasukanPengeluaranExport;
use App\Imports\TransaksiImport;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Export transaksi ke Excel
     */
    public function exportTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'nullable|in:pending,proses,selesai,diambil'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $filename = 'laporan_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new TransaksiExport($request->all()), $filename);
    }

    /**
     * Export laporan pemasukan pengeluaran
     */
    public function exportPemasukanPengeluaran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'tipe' => 'required|in:harian,bulanan,tahunan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $filename = 'laporan_pemasukan_pengeluaran_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new PemasukanPengeluaranExport($request->all()), $filename);
    }

    /**
     * Import transaksi dari Excel
     */
    public function importTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Excel::import(new TransaksiImport, $request->file('file'));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimport'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get laporan pemasukan pengeluaran
     */
    public function getLaporanPemasukanPengeluaran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'tipe' => 'required|in:harian,bulanan,tahunan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();

        $query = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('status', 'selesai');

        switch ($request->tipe) {
            case 'harian':
                $data = $query->selectRaw('DATE(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
                break;
            case 'bulanan':
                $data = $query->selectRaw('DATE_FORMAT(tanggal_masuk, "%Y-%m") as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
                break;
            case 'tahunan':
                $data = $query->selectRaw('YEAR(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
                break;
        }

        $totalPemasukan = $data->sum('total_pemasukan');
        $totalTransaksi = $data->sum('jumlah_transaksi');

        return response()->json([
            'status' => 'success',
            'data' => [
                'detail' => $data,
                'summary' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_transaksi' => $totalTransaksi,
                    'rata_rata_per_transaksi' => $totalTransaksi > 0 ? $totalPemasukan / $totalTransaksi : 0
                ]
            ]
        ]);
    }

    /**
     * Get laporan layanan terlaris
     */
    public function getLaporanLayananTerlaris(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();
        $limit = $request->limit ?? 10;

        $data = DetailTransaksi::join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->join('layanan', 'detail_transaksi.layanan_id', '=', 'layanan.id')
            ->whereBetween('transaksi.tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('transaksi.status', 'selesai')
            ->selectRaw('
                layanan.nama_layanan,
                layanan.satuan,
                SUM(detail_transaksi.jumlah) as total_jumlah,
                SUM(detail_transaksi.subtotal) as total_pendapatan,
                COUNT(detail_transaksi.id) as jumlah_order
            ')
            ->groupBy('layanan.id', 'layanan.nama_layanan', 'layanan.satuan')
            ->orderBy('total_pendapatan', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanLalu = Carbon::now()->subMonth()->startOfMonth();
        $akhirBulanLalu = Carbon::now()->subMonth()->endOfMonth();

        // Statistik bulan ini
        $transaksiBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)->count();
        $pendapatanBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik bulan lalu
        $transaksiBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])->count();
        $pendapatanBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik hari ini
        $hariIni = Carbon::now()->startOfDay();
        $transaksiHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)->count();
        $pendapatanHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Status transaksi
        $statusTransaksi = Transaksi::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->pluck('jumlah', 'status');

        return response()->json([
            'status' => 'success',
            'data' => [
                'bulan_ini' => [
                    'transaksi' => $transaksiBulanIni,
                    'pendapatan' => $pendapatanBulanIni,
                ],
                'bulan_lalu' => [
                    'transaksi' => $transaksiBulanLalu,
                    'pendapatan' => $pendapatanBulanLalu,
                ],
                'hari_ini' => [
                    'transaksi' => $transaksiHariIni,
                    'pendapatan' => $pendapatanHariIni,
                ],
                'status_transaksi' => $statusTransaksi,
                'total_pelanggan' => Pelanggan::count(),
                'total_layanan' => Layanan::count(),
            ]
        ]);
    }
}
