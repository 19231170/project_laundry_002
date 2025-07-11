<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\DetailPengeluaran;
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

        // Query for income (pemasukan)
        $queryPemasukan = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('status', 'selesai');
        
        // Query for expenses (pengeluaran)
        $queryPengeluaran = Pengeluaran::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        // Format data based on report type
        switch ($request->tipe) {
            case 'harian':
                $pemasukan = $queryPemasukan->selectRaw('DATE(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                
                $pengeluaran = $queryPengeluaran->selectRaw('DATE(tanggal) as periode, SUM(total_biaya) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                break;
                
            case 'bulanan':
                $pemasukan = $queryPemasukan->selectRaw('DATE_FORMAT(tanggal_masuk, "%Y-%m") as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                
                $pengeluaran = $queryPengeluaran->selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as periode, SUM(total_biaya) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                break;
                
            case 'tahunan':
                $pemasukan = $queryPemasukan->selectRaw('YEAR(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                
                $pengeluaran = $queryPengeluaran->selectRaw('YEAR(tanggal) as periode, SUM(total_biaya) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get()
                    ->keyBy('periode');
                break;
        }

        // Merge pemasukan and pengeluaran data
        $allPeriods = collect($pemasukan->keys())->merge($pengeluaran->keys())->unique()->sort()->values();
        
        $mergedData = $allPeriods->map(function ($periode) use ($pemasukan, $pengeluaran) {
            $pemasukanItem = $pemasukan->get($periode, ['total_pemasukan' => 0, 'jumlah_transaksi' => 0]);
            $pengeluaranItem = $pengeluaran->get($periode, ['total_pengeluaran' => 0, 'jumlah_pengeluaran' => 0]);
            
            $totalPemasukan = $pemasukanItem['total_pemasukan'] ?? 0;
            $totalPengeluaran = $pengeluaranItem['total_pengeluaran'] ?? 0;
            
            return [
                'periode' => $periode,
                'total_pemasukan' => $totalPemasukan,
                'jumlah_transaksi' => $pemasukanItem['jumlah_transaksi'] ?? 0,
                'total_pengeluaran' => $totalPengeluaran,
                'jumlah_pengeluaran' => $pengeluaranItem['jumlah_pengeluaran'] ?? 0,
                'laba_rugi' => $totalPemasukan - $totalPengeluaran
            ];
        })->values();

        // Calculate summary
        $totalPemasukan = $pemasukan->sum('total_pemasukan');
        $totalTransaksi = $pemasukan->sum('jumlah_transaksi');
        $totalPengeluaran = $pengeluaran->sum('total_pengeluaran');
        $totalJumlahPengeluaran = $pengeluaran->sum('jumlah_pengeluaran');
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'status' => 'success',
            'data' => [
                'detail' => $mergedData,
                'summary' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_transaksi' => $totalTransaksi,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_jumlah_pengeluaran' => $totalJumlahPengeluaran,
                    'laba_rugi' => $labaRugi,
                    'rata_rata_per_transaksi' => $totalTransaksi > 0 ? $totalPemasukan / $totalTransaksi : 0,
                    'rata_rata_per_pengeluaran' => $totalJumlahPengeluaran > 0 ? $totalPengeluaran / $totalJumlahPengeluaran : 0
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
        $akhirBulanIni = Carbon::now()->endOfMonth();
        $bulanLalu = Carbon::now()->subMonth()->startOfMonth();
        $akhirBulanLalu = Carbon::now()->subMonth()->endOfMonth();

        // Statistik bulan ini - Pemasukan
        $transaksiBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)->count();
        $pendapatanBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik bulan ini - Pengeluaran
        $pengeluaranBulanIni = Pengeluaran::whereBetween('tanggal', [$bulanIni, $akhirBulanIni])->count();
        $totalPengeluaranBulanIni = Pengeluaran::whereBetween('tanggal', [$bulanIni, $akhirBulanIni])
            ->sum('total_biaya');

        // Laba/Rugi bulan ini
        $labaRugiBulanIni = $pendapatanBulanIni - $totalPengeluaranBulanIni;

        // Statistik bulan lalu - Pemasukan
        $transaksiBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])->count();
        $pendapatanBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik bulan lalu - Pengeluaran
        $pengeluaranBulanLalu = Pengeluaran::whereBetween('tanggal', [$bulanLalu, $akhirBulanLalu])->count();
        $totalPengeluaranBulanLalu = Pengeluaran::whereBetween('tanggal', [$bulanLalu, $akhirBulanLalu])
            ->sum('total_biaya');

        // Laba/Rugi bulan lalu
        $labaRugiBulanLalu = $pendapatanBulanLalu - $totalPengeluaranBulanLalu;

        // Statistik hari ini - Pemasukan
        $hariIni = Carbon::now()->startOfDay();
        $transaksiHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)->count();
        $pendapatanHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik hari ini - Pengeluaran
        $pengeluaranHariIni = Pengeluaran::where('tanggal', '>=', $hariIni)->count();
        $totalPengeluaranHariIni = Pengeluaran::where('tanggal', '>=', $hariIni)
            ->sum('total_biaya');

        // Laba/Rugi hari ini
        $labaRugiHariIni = $pendapatanHariIni - $totalPengeluaranHariIni;

        // Status transaksi
        $statusTransaksi = Transaksi::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->pluck('jumlah', 'status');

        // Pengeluaran terbaru
        $pengeluaranTerbaru = Pengeluaran::with(['kategori', 'detailPengeluaran'])
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // Grafik perbandingan pemasukan vs pengeluaran (6 bulan terakhir)
        $enamBulanLalu = Carbon::now()->subMonths(5)->startOfMonth();
        
        $grafikPerbandingan = [];
        $currentDate = Carbon::parse($enamBulanLalu);
        
        for ($i = 0; $i < 6; $i++) {
            $startOfMonth = $currentDate->copy()->startOfMonth();
            $endOfMonth = $currentDate->copy()->endOfMonth();
            $monthLabel = $currentDate->format('M Y');
            
            $pemasukan = Transaksi::whereBetween('tanggal_masuk', [$startOfMonth, $endOfMonth])
                ->where('status', 'selesai')
                ->sum('total_harga');
                
            $pengeluaran = Pengeluaran::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('total_biaya');
                
            $grafikPerbandingan[] = [
                'bulan' => $monthLabel,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'laba_rugi' => $pemasukan - $pengeluaran
            ];
            
            $currentDate->addMonth();
        }

        // Pengeluaran per kategori (bulan ini)
        $pengeluaranPerKategori = KategoriPengeluaran::join('pengeluaran', 'kategori_pengeluaran.id', '=', 'pengeluaran.kategori_pengeluaran_id')
            ->whereBetween('pengeluaran.tanggal', [$bulanIni, $akhirBulanIni])
            ->selectRaw('kategori_pengeluaran.nama_kategori, SUM(pengeluaran.total_biaya) as total')
            ->groupBy('kategori_pengeluaran.id', 'kategori_pengeluaran.nama_kategori')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'bulan_ini' => [
                    'transaksi' => $transaksiBulanIni,
                    'pendapatan' => $pendapatanBulanIni,
                    'pengeluaran' => [
                        'jumlah' => $pengeluaranBulanIni,
                        'total' => $totalPengeluaranBulanIni
                    ],
                    'laba_rugi' => $labaRugiBulanIni
                ],
                'bulan_lalu' => [
                    'transaksi' => $transaksiBulanLalu,
                    'pendapatan' => $pendapatanBulanLalu,
                    'pengeluaran' => [
                        'jumlah' => $pengeluaranBulanLalu,
                        'total' => $totalPengeluaranBulanLalu
                    ],
                    'laba_rugi' => $labaRugiBulanLalu
                ],
                'hari_ini' => [
                    'transaksi' => $transaksiHariIni,
                    'pendapatan' => $pendapatanHariIni,
                    'pengeluaran' => [
                        'jumlah' => $pengeluaranHariIni,
                        'total' => $totalPengeluaranHariIni
                    ],
                    'laba_rugi' => $labaRugiHariIni
                ],
                'status_transaksi' => $statusTransaksi,
                'total_pelanggan' => Pelanggan::count(),
                'total_layanan' => Layanan::count(),
                'pengeluaran_terbaru' => $pengeluaranTerbaru,
                'grafik_perbandingan' => $grafikPerbandingan,
                'pengeluaran_per_kategori' => $pengeluaranPerKategori
            ]
        ]);
    }

    /**
     * Get laporan laba rugi
     */
    public function getLaporanLabaRugi(Request $request)
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

        // Query untuk pemasukan (dari transaksi selesai)
        $queryPemasukan = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('status', 'selesai');
            
        // Query untuk pengeluaran
        $queryPengeluaran = Pengeluaran::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        // Format data berdasarkan tipe laporan
        switch ($request->tipe) {
            case 'harian':
                $periodeFormat = 'DATE(%s) as periode';
                $groupByFormat = 'periode';
                break;
                
            case 'bulanan':
                $periodeFormat = 'DATE_FORMAT(%s, "%%Y-%%m") as periode';
                $groupByFormat = 'periode';
                break;
                
            case 'tahunan':
                $periodeFormat = 'YEAR(%s) as periode';
                $groupByFormat = 'periode';
                break;
        }

        // Get pemasukan data
        $pemasukan = $queryPemasukan->selectRaw(sprintf($periodeFormat, 'tanggal_masuk') . ', SUM(total_harga) as nilai')
            ->groupBy($groupByFormat)
            ->orderBy('periode')
            ->get()
            ->keyBy('periode');
            
        // Get pengeluaran data
        $pengeluaran = $queryPengeluaran->selectRaw(sprintf($periodeFormat, 'tanggal') . ', SUM(total_biaya) as nilai')
            ->groupBy($groupByFormat)
            ->orderBy('periode')
            ->get()
            ->keyBy('periode');
            
        // Get pengeluaran by kategori
        $pengeluaranByKategori = $queryPengeluaran
            ->join('kategori_pengeluaran', 'pengeluaran.kategori_pengeluaran_id', '=', 'kategori_pengeluaran.id')
            ->selectRaw('kategori_pengeluaran.id, kategori_pengeluaran.nama_kategori, SUM(pengeluaran.total_biaya) as total')
            ->groupBy('kategori_pengeluaran.id', 'kategori_pengeluaran.nama_kategori')
            ->orderBy('total', 'desc')
            ->get();

        // Merge pemasukan and pengeluaran data
        $allPeriods = collect($pemasukan->keys())->merge($pengeluaran->keys())->unique()->sort()->values();
        
        $mergedData = $allPeriods->map(function ($periode) use ($pemasukan, $pengeluaran) {
            $nilaiPemasukan = isset($pemasukan[$periode]) ? $pemasukan[$periode]['nilai'] : 0;
            $nilaiPengeluaran = isset($pengeluaran[$periode]) ? $pengeluaran[$periode]['nilai'] : 0;
            $labaRugi = $nilaiPemasukan - $nilaiPengeluaran;
            
            return [
                'periode' => $periode,
                'pemasukan' => $nilaiPemasukan,
                'pengeluaran' => $nilaiPengeluaran,
                'laba_rugi' => $labaRugi,
                'profit_margin' => $nilaiPemasukan > 0 ? round(($labaRugi / $nilaiPemasukan) * 100, 2) : 0,
            ];
        })->values();

        // Calculate summary
        $totalPemasukan = $mergedData->sum('pemasukan');
        $totalPengeluaran = $mergedData->sum('pengeluaran');
        $totalLabaRugi = $totalPemasukan - $totalPengeluaran;
        $profitMargin = $totalPemasukan > 0 ? round(($totalLabaRugi / $totalPemasukan) * 100, 2) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'detail' => $mergedData,
                'pengeluaran_per_kategori' => $pengeluaranByKategori,
                'summary' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_laba_rugi' => $totalLabaRugi,
                    'profit_margin' => $profitMargin,
                ]
            ]
        ]);
    }

    /**
     * Get laporan penggunaan bahan per periode
     */
    public function getLaporanPenggunaanBahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'kategori_id' => 'nullable|exists:kategori_pengeluaran,id'
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

        // Base query for all expense details
        $query = DetailPengeluaran::join('pengeluaran', 'detail_pengeluaran.pengeluaran_id', '=', 'pengeluaran.id')
            ->whereBetween('pengeluaran.tanggal', [$tanggalMulai, $tanggalSelesai]);
            
        // Filter by kategori if specified
        if ($request->has('kategori_id')) {
            $query->where('pengeluaran.kategori_pengeluaran_id', $request->kategori_id);
        }
            
        // Get summary of item usage
        $penggunaanBahan = $query->selectRaw('
            detail_pengeluaran.nama_item,
            SUM(detail_pengeluaran.jumlah) as total_jumlah,
            detail_pengeluaran.satuan,
            AVG(detail_pengeluaran.harga_satuan) as harga_satuan_rata,
            SUM(detail_pengeluaran.subtotal) as total_biaya,
            COUNT(DISTINCT pengeluaran.id) as jumlah_transaksi
        ')
        ->groupBy('detail_pengeluaran.nama_item', 'detail_pengeluaran.satuan')
        ->orderBy('total_biaya', 'desc')
        ->get();
        
        // Calculate total usage stats
        $totalBiaya = $penggunaanBahan->sum('total_biaya');
        $totalTransaksi = $query->distinct('pengeluaran.id')->count('pengeluaran.id');
        
        // Get usage by supplier
        $penggunaanBySupplier = $query->leftJoin('supplier', 'detail_pengeluaran.supplier_id', '=', 'supplier.id')
            ->selectRaw('
                supplier.id as supplier_id,
                supplier.nama as supplier_nama,
                COUNT(DISTINCT pengeluaran.id) as jumlah_transaksi,
                SUM(detail_pengeluaran.subtotal) as total_biaya
            ')
            ->whereNotNull('detail_pengeluaran.supplier_id')
            ->groupBy('supplier.id', 'supplier.nama')
            ->orderBy('total_biaya', 'desc')
            ->get();

        // Get monthly/daily trend
        $trendHarian = $query->selectRaw('
            DATE(pengeluaran.tanggal) as tanggal,
            SUM(detail_pengeluaran.subtotal) as total_biaya,
            COUNT(DISTINCT detail_pengeluaran.nama_item) as jumlah_item
        ')
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'penggunaan_bahan' => $penggunaanBahan,
                'penggunaan_by_supplier' => $penggunaanBySupplier,
                'trend_harian' => $trendHarian,
                'summary' => [
                    'total_biaya' => $totalBiaya,
                    'total_transaksi' => $totalTransaksi,
                    'rata_rata_per_transaksi' => $totalTransaksi > 0 ? $totalBiaya / $totalTransaksi : 0
                ]
            ]
        ]);
    }

    /**
     * Get laporan pengeluaran per kategori
     */
    public function getLaporanPengeluaranPerKategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date'
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

        // Pengeluaran per kategori
        $pengeluaranPerKategori = KategoriPengeluaran::leftJoin('pengeluaran', 'kategori_pengeluaran.id', '=', 'pengeluaran.kategori_pengeluaran_id')
            ->whereBetween('pengeluaran.tanggal', [$tanggalMulai, $tanggalSelesai])
            ->selectRaw('
                kategori_pengeluaran.id,
                kategori_pengeluaran.nama_kategori,
                kategori_pengeluaran.deskripsi,
                SUM(pengeluaran.total_biaya) as total_biaya,
                COUNT(pengeluaran.id) as jumlah_transaksi
            ')
            ->groupBy('kategori_pengeluaran.id', 'kategori_pengeluaran.nama_kategori', 'kategori_pengeluaran.deskripsi')
            ->orderBy('total_biaya', 'desc')
            ->get();

        // Calculate total and percentage
        $totalPengeluaran = $pengeluaranPerKategori->sum('total_biaya');
        
        $pengeluaranPerKategori = $pengeluaranPerKategori->map(function ($item) use ($totalPengeluaran) {
            $item['persentase'] = $totalPengeluaran > 0 ? round(($item['total_biaya'] / $totalPengeluaran) * 100, 2) : 0;
            return $item;
        });
        
        // Trend pengeluaran per kategori per bulan
        $trendBulanan = Pengeluaran::join('kategori_pengeluaran', 'pengeluaran.kategori_pengeluaran_id', '=', 'kategori_pengeluaran.id')
            ->whereBetween('pengeluaran.tanggal', [$tanggalMulai, $tanggalSelesai])
            ->selectRaw('
                DATE_FORMAT(pengeluaran.tanggal, "%Y-%m") as bulan,
                kategori_pengeluaran.nama_kategori,
                SUM(pengeluaran.total_biaya) as total_biaya
            ')
            ->groupBy('bulan', 'kategori_pengeluaran.nama_kategori')
            ->orderBy('bulan')
            ->get();
            
        // Reshape the trend data for easier frontend processing
        $bulanList = $trendBulanan->pluck('bulan')->unique()->values();
        $kategoriList = $trendBulanan->pluck('nama_kategori')->unique()->values();
        
        $trendData = [];
        foreach ($bulanList as $bulan) {
            $bulanData = ['bulan' => $bulan];
            
            foreach ($kategoriList as $kategori) {
                $item = $trendBulanan->where('bulan', $bulan)->where('nama_kategori', $kategori)->first();
                $bulanData[$kategori] = $item ? $item['total_biaya'] : 0;
            }
            
            $trendData[] = $bulanData;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'pengeluaran_per_kategori' => $pengeluaranPerKategori,
                'kategori_list' => $kategoriList,
                'trend_bulanan' => $trendData,
                'summary' => [
                    'total_pengeluaran' => $totalPengeluaran,
                    'jumlah_kategori' => $pengeluaranPerKategori->count(),
                ]
            ]
        ]);
    }

    /**
     * Export laporan laba rugi
     */
    public function exportLabaRugi(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        
        if (!$tanggalMulai) {
            $tanggalMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        
        if (!$tanggalAkhir) {
            $tanggalAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $data = $this->getLaporanLabaRugiData($tanggalMulai, $tanggalAkhir);
        
        $fileName = 'laporan_laba_rugi_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        
        return Excel::download(new LabaRugiExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    /**
     * Export laporan penggunaan bahan
     */
    public function exportPenggunaanBahan(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        
        if (!$tanggalMulai) {
            $tanggalMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        
        if (!$tanggalAkhir) {
            $tanggalAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $data = $this->getLaporanPenggunaanBahanData($tanggalMulai, $tanggalAkhir);
        
        $fileName = 'laporan_penggunaan_bahan_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        
        return Excel::download(new PenggunaanBahanExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    /**
     * Export laporan pengeluaran per kategori
     */
    public function exportPengeluaranPerKategori(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        
        if (!$tanggalMulai) {
            $tanggalMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        
        if (!$tanggalAkhir) {
            $tanggalAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $data = $this->getLaporanPengeluaranPerKategoriData($tanggalMulai, $tanggalAkhir);
        
        $fileName = 'laporan_pengeluaran_kategori_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        
        return Excel::download(new PengeluaranKategoriExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    /**
     * Helper for preparing data for laporan laba rugi
     */
    private function getLaporanLabaRugiData($tanggalMulai, $tanggalAkhir)
    {
        // Get pemasukan data
        $pemasukan = Transaksi::with(['customer'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->where('status', 'selesai')
            ->get();
            
        $totalPemasukan = $pemasukan->sum('total');
        
        // Get pengeluaran data
        $pengeluaran = Pengeluaran::with(['kategori_pengeluaran', 'supplier', 'detail_pengeluaran'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get();
            
        $totalPengeluaran = $pengeluaran->sum('total');
        
        // Calculate profit/loss
        $labaRugi = $totalPemasukan - $totalPengeluaran;
        
        // Group pengeluaran by category for the chart
        $pengeluaranByCategory = $pengeluaran->groupBy('kategori_pengeluaran.nama')
            ->map(function ($items, $kategori) {
                return [
                    'kategori' => $kategori ?? 'Tanpa Kategori',
                    'total' => $items->sum('total')
                ];
            })->values();
            
        // Prepare monthly data for trend chart
        $startDate = Carbon::parse($tanggalMulai)->startOfMonth();
        $endDate = Carbon::parse($tanggalAkhir)->endOfMonth();
        
        $monthlyData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $monthYear = $currentDate->format('M Y');
            $monthStart = $currentDate->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            
            $monthPemasukan = Transaksi::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->where('status', 'selesai')
                ->sum('total');
                
            $monthPengeluaran = Pengeluaran::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->sum('total');
                
            $monthlyData[] = [
                'bulan' => $monthYear,
                'pemasukan' => $monthPemasukan,
                'pengeluaran' => $monthPengeluaran,
                'laba' => $monthPemasukan - $monthPengeluaran
            ];
            
            $currentDate->addMonth();
        }
        
        return [
            'summary' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_rugi' => $labaRugi,
                'persentase_laba' => $totalPemasukan > 0 ? round(($labaRugi / $totalPemasukan) * 100, 2) : 0
            ],
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'pengeluaran_by_category' => $pengeluaranByCategory,
            'monthly_data' => $monthlyData,
            'periode' => [
                'mulai' => $tanggalMulai,
                'akhir' => $tanggalAkhir
            ]
        ];
    }

    /**
     * Helper for preparing data for laporan penggunaan bahan
     */
    private function getLaporanPenggunaanBahanData($tanggalMulai, $tanggalAkhir)
    {
        // Get all inventory items that were used in transactions during this period
        $detailPengeluaran = DetailPengeluaran::whereHas('pengeluaran', function($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->with(['pengeluaran', 'pengeluaran.kategori_pengeluaran'])
            ->get();
            
        // Group by item name
        $bahanByName = $detailPengeluaran->groupBy('nama')
            ->map(function ($items, $nama) {
                $totalQty = $items->sum('qty');
                $totalNilai = $items->sum(function ($item) {
                    return $item->qty * $item->harga;
                });
                
                $kategoriList = $items->pluck('pengeluaran.kategori_pengeluaran.nama')->unique()->filter()->implode(', ');
                
                return [
                    'nama' => $nama,
                    'kategori' => $kategoriList ?: 'Tanpa Kategori',
                    'qty' => $totalQty,
                    'total_nilai' => $totalNilai,
                    'rata_harga' => $totalQty > 0 ? $totalNilai / $totalQty : 0
                ];
            })->values()->sortByDesc('total_nilai')->values();
            
        // Calculate totals
        $totalNilaiBahan = $bahanByName->sum('total_nilai');
        
        // Get inventory items with low stock
        $lowStockItems = Inventaris::whereRaw('stok < stok_minimum')
            ->with('kategori_pengeluaran')
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama,
                    'kategori' => $item->kategori_pengeluaran->nama ?? 'Tanpa Kategori',
                    'stok' => $item->stok,
                    'stok_minimum' => $item->stok_minimum,
                    'satuan' => $item->satuan,
                    'status' => $item->stok <= 0 ? 'Habis' : 'Rendah'
                ];
            });
            
        return [
            'bahan_by_name' => $bahanByName,
            'total_nilai_bahan' => $totalNilaiBahan,
            'low_stock_items' => $lowStockItems,
            'periode' => [
                'mulai' => $tanggalMulai,
                'akhir' => $tanggalAkhir
            ]
        ];
    }

    /**
     * Helper for preparing data for laporan pengeluaran per kategori
     */
    private function getLaporanPengeluaranPerKategoriData($tanggalMulai, $tanggalAkhir)
    {
        // Get all categories with pengeluaran
        $kategoriList = KategoriPengeluaran::with(['pengeluaran' => function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            }])
            ->get();
            
        // Calculate total for each category and prepare data
        $kategoriData = $kategoriList->map(function ($kategori) {
            $totalPengeluaran = $kategori->pengeluaran->sum('total');
            $jumlahTransaksi = $kategori->pengeluaran->count();
            
            return [
                'id' => $kategori->id,
                'nama' => $kategori->nama,
                'deskripsi' => $kategori->deskripsi,
                'total_pengeluaran' => $totalPengeluaran,
                'jumlah_transaksi' => $jumlahTransaksi
            ];
        })->sortByDesc('total_pengeluaran')->values();
        
        // Calculate grand total
        $totalPengeluaran = $kategoriData->sum('total_pengeluaran');
        
        // Add percentage to each category
        $kategoriData = $kategoriData->map(function ($item) use ($totalPengeluaran) {
            $item['persentase'] = $totalPengeluaran > 0 ? round(($item['total_pengeluaran'] / $totalPengeluaran) * 100, 2) : 0;
            return $item;
        });
        
        // Get details for each category
        $detailByKategori = [];
        
        foreach ($kategoriList as $kategori) {
            if ($kategori->pengeluaran->count() > 0) {
                $pengeluaranList = $kategori->pengeluaran->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'tanggal' => $p->tanggal,
                        'kode' => $p->kode,
                        'keterangan' => $p->keterangan,
                        'supplier' => $p->supplier->nama ?? null,
                        'total' => $p->total,
                        'detail_items' => $p->detail_pengeluaran->map(function ($item) {
                            return [
                                'nama' => $item->nama,
                                'qty' => $item->qty,
                                'harga' => $item->harga,
                                'subtotal' => $item->qty * $item->harga
                            ];
                        })
                    ];
                });
                
                $detailByKategori[$kategori->id] = [
                    'kategori' => $kategori->nama,
                    'pengeluaran' => $pengeluaranList
                ];
            }
        }
        
        return [
            'kategori_data' => $kategoriData,
            'detail_by_kategori' => $detailByKategori,
            'total_pengeluaran' => $totalPengeluaran,
            'periode' => [
                'mulai' => $tanggalMulai,
                'akhir' => $tanggalAkhir
            ]
        ];
    }
}
