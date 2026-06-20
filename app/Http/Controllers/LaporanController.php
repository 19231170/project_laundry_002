<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use App\Exports\PemasukanPengeluaranExport;
use App\Exports\LabaRugiExport;
use App\Exports\PenggunaanBahanExport;
use App\Exports\PengeluaranKategoriExport;
use App\Exports\PembulatanExport;
use App\Imports\TransaksiImport;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LaporanController extends Controller
{
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

        $queryPemasukan = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('status', 'selesai');

        $queryPengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalSelesai]);

        switch ($request->tipe) {
            case 'harian':
                $pemasukan = $queryPemasukan->selectRaw('DATE(tanggal_masuk) as periode, SUM(total_setelah_pembulatan) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                $pengeluaran = $queryPengeluaran->selectRaw('DATE(date) as periode, SUM(amount) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                break;
            case 'bulanan':
                $pemasukan = $queryPemasukan->selectRaw('DATE_FORMAT(tanggal_masuk, "%Y-%m") as periode, SUM(total_setelah_pembulatan) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                $pengeluaran = $queryPengeluaran->selectRaw('DATE_FORMAT(date, "%Y-%m") as periode, SUM(amount) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                break;
            case 'tahunan':
                $pemasukan = $queryPemasukan->selectRaw('YEAR(tanggal_masuk) as periode, SUM(total_setelah_pembulatan) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                $pengeluaran = $queryPengeluaran->selectRaw('YEAR(date) as periode, SUM(amount) as total_pengeluaran, COUNT(*) as jumlah_pengeluaran')
                    ->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');
                break;
        }

        $allPeriods = collect($pemasukan->keys())->merge($pengeluaran->keys())->unique()->sort()->values();

        $mergedData = $allPeriods->map(function ($periode) use ($pemasukan, $pengeluaran) {
            $pItem = $pemasukan->get($periode, ['total_pemasukan' => 0, 'jumlah_transaksi' => 0]);
            $kItem = $pengeluaran->get($periode, ['total_pengeluaran' => 0, 'jumlah_pengeluaran' => 0]);
            $totalPemasukan = $pItem['total_pemasukan'] ?? 0;
            $totalPengeluaran = $kItem['total_pengeluaran'] ?? 0;
            return [
                'periode' => $periode,
                'total_pemasukan' => $totalPemasukan,
                'jumlah_transaksi' => $pItem['jumlah_transaksi'] ?? 0,
                'total_pengeluaran' => $totalPengeluaran,
                'jumlah_pengeluaran' => $kItem['jumlah_pengeluaran'] ?? 0,
                'laba_rugi' => $totalPemasukan - $totalPengeluaran
            ];
        })->values();

        $totalPemasukan = $pemasukan->sum('total_pemasukan');
        $totalTransaksi = $pemasukan->sum('jumlah_transaksi');
        $totalPengeluaran = $pengeluaran->sum('total_pengeluaran');
        $totalJumlahPengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        return response()->json([
            'status' => 'success',
            'data' => [
                'detail' => $mergedData,
                'summary' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_transaksi' => $totalTransaksi,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_jumlah_pengeluaran' => $totalJumlahPengeluaran,
                    'laba_rugi' => $totalPemasukan - $totalPengeluaran,
                    'rata_rata_per_transaksi' => $totalTransaksi > 0 ? $totalPemasukan / $totalTransaksi : 0,
                    'rata_rata_per_pengeluaran' => $totalJumlahPengeluaran > 0 ? $totalPengeluaran / $totalJumlahPengeluaran : 0
                ]
            ]
        ]);
    }

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
            ->selectRaw('layanan.nama_layanan, layanan.satuan, SUM(detail_transaksi.jumlah) as total_jumlah, SUM(detail_transaksi.subtotal) as total_pendapatan, COUNT(detail_transaksi.id) as jumlah_order')
            ->groupBy('layanan.id', 'layanan.nama_layanan', 'layanan.satuan')
            ->orderBy('total_pendapatan', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getDashboardStats(Request $request)
    {
        $bulanIni = Carbon::now()->startOfMonth();
        $akhirBulanIni = Carbon::now()->endOfMonth();
        $bulanLalu = Carbon::now()->subMonth()->startOfMonth();
        $akhirBulanLalu = Carbon::now()->subMonth()->endOfMonth();
        $hariIni = Carbon::now()->startOfDay();

        // Bulan ini
        $transaksiBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)->count();
        $pendapatanBulanIni = Transaksi::where('tanggal_masuk', '>=', $bulanIni)->where('status', 'selesai')->sum('total_harga');
        $totalPengeluaranBulanIni = Pengeluaran::whereBetween('date', [$bulanIni, $akhirBulanIni])->sum('amount');

        // Bulan lalu
        $transaksiBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])->count();
        $pendapatanBulanLalu = Transaksi::whereBetween('tanggal_masuk', [$bulanLalu, $akhirBulanLalu])->where('status', 'selesai')->sum('total_harga');
        $totalPengeluaranBulanLalu = Pengeluaran::whereBetween('date', [$bulanLalu, $akhirBulanLalu])->sum('amount');

        // Hari ini
        $transaksiHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)->count();
        $pendapatanHariIni = Transaksi::where('tanggal_masuk', '>=', $hariIni)->where('status', 'selesai')->sum('total_harga');
        $totalPengeluaranHariIni = Pengeluaran::where('date', '>=', $hariIni)->sum('amount');

        // Status
        $statusTransaksi = Transaksi::selectRaw('status, COUNT(*) as jumlah')->groupBy('status')->get()->pluck('jumlah', 'status');

        $totalBelumLunas = Transaksi::where('status_pembayaran', 'belum_lunas')->sum('sisa_pembayaran');
        $transaksiCount = Transaksi::count();
        $belumLunasCount = Transaksi::where('status_pembayaran', 'belum_lunas')->count();
        $lunasCount = Transaksi::where('status_pembayaran', 'lunas')->count();

        // Grafik 6 bulan terakhir
        $enamBulanLalu = Carbon::now()->subMonths(5)->startOfMonth();
        $grafikPerbandingan = [];
        $currentDate = Carbon::parse($enamBulanLalu);

        for ($i = 0; $i < 6; $i++) {
            $start = $currentDate->copy()->startOfMonth();
            $end = $currentDate->copy()->endOfMonth();
            $pemasukan = Transaksi::whereBetween('tanggal_masuk', [$start, $end])->where('status', 'selesai')->sum('total_harga');
            $pengeluaran = Pengeluaran::whereBetween('date', [$start, $end])->sum('amount');
            $grafikPerbandingan[] = [
                'bulan' => $currentDate->format('M Y'),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'laba_rugi' => $pemasukan - $pengeluaran
            ];
            $currentDate->addMonth();
        }

        // Pengeluaran terbaru
        $pengeluaranTerbaru = Pengeluaran::orderBy('date', 'desc')->limit(5)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'bulan_ini' => [
                    'transaksi' => $transaksiBulanIni,
                    'pendapatan' => $pendapatanBulanIni,
                    'pengeluaran' => ['jumlah' => Pengeluaran::whereBetween('date', [$bulanIni, $akhirBulanIni])->count(), 'total' => $totalPengeluaranBulanIni],
                    'laba_rugi' => $pendapatanBulanIni - $totalPengeluaranBulanIni
                ],
                'bulan_lalu' => [
                    'transaksi' => $transaksiBulanLalu,
                    'pendapatan' => $pendapatanBulanLalu,
                    'pengeluaran' => ['jumlah' => Pengeluaran::whereBetween('date', [$bulanLalu, $akhirBulanLalu])->count(), 'total' => $totalPengeluaranBulanLalu],
                    'laba_rugi' => $pendapatanBulanLalu - $totalPengeluaranBulanLalu
                ],
                'hari_ini' => [
                    'transaksi' => $transaksiHariIni,
                    'pendapatan' => $pendapatanHariIni,
                    'pengeluaran' => ['jumlah' => Pengeluaran::where('date', '>=', $hariIni)->count(), 'total' => $totalPengeluaranHariIni],
                    'laba_rugi' => $pendapatanHariIni - $totalPengeluaranHariIni
                ],
                'status_transaksi' => $statusTransaksi,
                'total_pelanggan' => Pelanggan::count(),
                'total_layanan' => Layanan::count(),
                'pengeluaran_terbaru' => $pengeluaranTerbaru,
                'pembayaran' => [
                    'total_belum_lunas' => $totalBelumLunas,
                    'transaksi_belum_lunas' => $belumLunasCount,
                    'transaksi_lunas' => $lunasCount,
                    'persentase_lunas' => $transaksiCount > 0 ? round(($lunasCount / $transaksiCount) * 100, 2) : 0
                ],
                'grafik_perbandingan' => $grafikPerbandingan,
            ]
        ]);
    }

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

        $queryPemasukan = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])->where('status', 'selesai');
        $queryPengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalSelesai]);

        switch ($request->tipe) {
            case 'harian':
                $periodeFormat = 'DATE(%s) as periode'; $groupBy = 'periode'; break;
            case 'bulanan':
                $periodeFormat = 'DATE_FORMAT(%s, "%%Y-%%m") as periode'; $groupBy = 'periode'; break;
            case 'tahunan':
                $periodeFormat = 'YEAR(%s) as periode'; $groupBy = 'periode'; break;
        }

        $pemasukan = $queryPemasukan->selectRaw(sprintf($periodeFormat, 'tanggal_masuk') . ', SUM(total_harga) as nilai')
            ->groupBy($groupBy)->orderBy('periode')->get()->keyBy('periode');

        $pengeluaran = $queryPengeluaran->selectRaw(sprintf($periodeFormat, 'date') . ', SUM(amount) as nilai')
            ->groupBy($groupBy)->orderBy('periode')->get()->keyBy('periode');

        $allPeriods = collect($pemasukan->keys())->merge($pengeluaran->keys())->unique()->sort()->values();

        $mergedData = $allPeriods->map(function ($periode) use ($pemasukan, $pengeluaran) {
            $nilaiP = $pemasukan->get($periode)['nilai'] ?? 0;
            $nilaiK = $pengeluaran->get($periode)['nilai'] ?? 0;
            return [
                'periode' => $periode,
                'pemasukan' => $nilaiP,
                'pengeluaran' => $nilaiK,
                'laba_rugi' => $nilaiP - $nilaiK,
                'profit_margin' => $nilaiP > 0 ? round((($nilaiP - $nilaiK) / $nilaiP) * 100, 2) : 0,
            ];
        })->values();

        $totalPemasukan = $mergedData->sum('pemasukan');
        $totalPengeluaran = $mergedData->sum('pengeluaran');
        $totalLabaRugi = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'status' => 'success',
            'data' => [
                'detail' => $mergedData,
                'summary' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_laba_rugi' => $totalLabaRugi,
                    'profit_margin' => $totalPemasukan > 0 ? round(($totalLabaRugi / $totalPemasukan) * 100, 2) : 0,
                ]
            ]
        ]);
    }

    public function getLaporanPenggunaanBahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();

        $pengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalSelesai]);
        $totalBiaya = $pengeluaran->sum('amount');
        $totalTransaksi = $pengeluaran->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'penggunaan_bahan' => [],
                'penggunaan_by_supplier' => [],
                'trend_harian' => [],
                'summary' => [
                    'total_biaya' => $totalBiaya,
                    'total_transaksi' => $totalTransaksi,
                    'rata_rata_per_transaksi' => $totalTransaksi > 0 ? $totalBiaya / $totalTransaksi : 0
                ]
            ]
        ]);
    }

    public function getLaporanPengeluaranPerKategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : Carbon::now()->endOfMonth();

        $totalPengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalSelesai])->sum('amount');
        $jumlahTransaksi = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalSelesai])->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'pengeluaran_per_kategori' => [],
                'kategori_list' => [],
                'trend_bulanan' => [],
                'summary' => [
                    'total_pengeluaran' => $totalPengeluaran,
                    'jumlah_transaksi' => $jumlahTransaksi,
                ]
            ]
        ]);
    }

    public function exportLabaRugi(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $data = $this->getLaporanLabaRugiData($tanggalMulai, $tanggalAkhir);
        $fileName = 'laporan_laba_rugi_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        return Excel::download(new LabaRugiExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    public function exportPenggunaanBahan(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $data = $this->getLaporanPenggunaanBahanData($tanggalMulai, $tanggalAkhir);
        $fileName = 'laporan_penggunaan_bahan_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        return Excel::download(new PenggunaanBahanExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    public function exportPengeluaranPerKategori(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $data = $this->getLaporanPengeluaranPerKategoriData($tanggalMulai, $tanggalAkhir);
        $fileName = 'laporan_pengeluaran_kategori_' . $tanggalMulai . '_sd_' . $tanggalAkhir . '.xlsx';
        return Excel::download(new PengeluaranKategoriExport($data, $tanggalMulai, $tanggalAkhir), $fileName);
    }

    public function getLaporanPembulatan(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $jenis = $request->input('jenis', 'semua');

        $query = Transaksi::with('pelanggan')->whereBetween('tanggal_masuk', [$startDate, $endDate])->where('pembulatan', '!=', 0);

        if ($jenis === 'positif') {
            $query->where('pembulatan', '>', 0);
        } elseif ($jenis === 'negatif') {
            $query->where('pembulatan', '<', 0);
        }

        $transaksi = $query->orderBy('tanggal_masuk', 'desc')->get();
        $totalPositif = $transaksi->where('pembulatan', '>', 0)->sum('pembulatan');
        $totalNegatif = $transaksi->where('pembulatan', '<', 0)->sum('pembulatan');

        return response()->json([
            'status' => 'success',
            'data' => [
                'transaksi' => $transaksi,
                'total_transaksi' => $transaksi->count(),
                'total_pembulatan_positif' => $totalPositif,
                'total_pembulatan_negatif' => $totalNegatif,
                'net_pembulatan' => $totalPositif + $totalNegatif,
                'periode' => ['start_date' => $startDate, 'end_date' => $endDate]
            ]
        ]);
    }

    public function exportPembulatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'jenis' => 'nullable|in:semua,positif,negatif'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $filename = 'laporan_pembulatan_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PembulatanExport($request->all()), $filename);
    }

    private function getLaporanLabaRugiData($tanggalMulai, $tanggalAkhir)
    {
        $pemasukan = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalAkhir])->where('status', 'selesai')->get();
        $totalPemasukan = $pemasukan->sum('total_harga');
        $pengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalAkhir])->get();
        $totalPengeluaran = $pengeluaran->sum('amount');
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $startDate = Carbon::parse($tanggalMulai)->startOfMonth();
        $endDate = Carbon::parse($tanggalAkhir)->endOfMonth();
        $monthlyData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthPemasukan = Transaksi::whereBetween('tanggal_masuk', [$monthStart, $monthEnd])->where('status', 'selesai')->sum('total_harga');
            $monthPengeluaran = Pengeluaran::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
            $monthlyData[] = [
                'bulan' => $currentDate->format('M Y'),
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
            'monthly_data' => $monthlyData,
            'periode' => ['mulai' => $tanggalMulai, 'akhir' => $tanggalAkhir]
        ];
    }

    private function getLaporanPenggunaanBahanData($tanggalMulai, $tanggalAkhir)
    {
        $totalBiaya = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalAkhir])->sum('amount');
        $lowStockItems = Inventaris::whereRaw('stok < stok_minimum')->get()->map(function ($item) {
            return [
                'nama' => $item->nama,
                'kategori' => $item->kategori->nama ?? 'Tanpa Kategori',
                'stok' => $item->stok,
                'stok_minimum' => $item->stok_minimum,
                'satuan' => $item->satuan,
                'status' => $item->stok <= 0 ? 'Habis' : 'Rendah'
            ];
        });

        return [
            'bahan_by_name' => [],
            'total_nilai_bahan' => $totalBiaya,
            'low_stock_items' => $lowStockItems,
            'periode' => ['mulai' => $tanggalMulai, 'akhir' => $tanggalAkhir]
        ];
    }

    private function getLaporanPengeluaranPerKategoriData($tanggalMulai, $tanggalAkhir)
    {
        $totalPengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalAkhir])->sum('amount');
        $pengeluaranList = Pengeluaran::whereBetween('date', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('date', 'desc')->get();

        return [
            'kategori_data' => [],
            'detail_by_kategori' => [],
            'total_pengeluaran' => $totalPengeluaran,
            'pengeluaran_list' => $pengeluaranList,
            'periode' => ['mulai' => $tanggalMulai, 'akhir' => $tanggalAkhir]
        ];
    }
}
