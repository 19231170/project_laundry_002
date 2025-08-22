<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use App\Exports\PemasukanPengeluaranExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanWebController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->format('Y-m-d'));
        $status = $request->get('status');
        
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai]);
            
        if ($status) {
            $query->where('status', $status);
        }
        
        $transaksi = $query->latest()->paginate(15);
        
        $summary = [
            'total_transaksi' => $query->count(),
            'total_pendapatan' => $query->sum('total_harga'),
            'transaksi_pending' => $query->where('status', 'pending')->count(),
            'transaksi_selesai' => $query->where('status', 'selesai')->count(),
        ];
        
        return view('laporan.index', compact('transaksi', 'summary'));
    }

    public function harian()
    {
        $tanggal = request('tanggal', Carbon::today()->format('Y-m-d'));
        
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->whereDate('tanggal_masuk', $tanggal)
            ->latest()
            ->paginate(10);
            
        $summary = [
            'total_transaksi' => Transaksi::whereDate('tanggal_masuk', $tanggal)->count(),
            'total_pendapatan' => Transaksi::whereDate('tanggal_masuk', $tanggal)->where('status', 'selesai')->sum('total_harga'),
            'transaksi_pending' => Transaksi::whereDate('tanggal_masuk', $tanggal)->where('status', 'pending')->count(),
            'transaksi_selesai' => Transaksi::whereDate('tanggal_masuk', $tanggal)->where('status', 'selesai')->count(),
        ];
        
        return view('laporan.harian', compact('transaksi', 'summary', 'tanggal'));
    }

    public function bulanan()
    {
        $bulan = request('bulan', Carbon::now()->format('Y-m'));
        
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth();
        
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->latest()
            ->paginate(10);
            
        $summary = [
            'total_transaksi' => Transaksi::whereBetween('tanggal_masuk', [$startDate, $endDate])->count(),
            'total_pendapatan' => Transaksi::whereBetween('tanggal_masuk', [$startDate, $endDate])->where('status', 'selesai')->sum('total_harga'),
            'transaksi_pending' => Transaksi::whereBetween('tanggal_masuk', [$startDate, $endDate])->where('status', 'pending')->count(),
            'transaksi_selesai' => Transaksi::whereBetween('tanggal_masuk', [$startDate, $endDate])->where('status', 'selesai')->count(),
        ];
        
        // Data harian dalam bulan
        $dailyData = Transaksi::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->selectRaw('DATE(tanggal_masuk) as tanggal, SUM(total_harga) as pendapatan, COUNT(*) as jumlah')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        return view('laporan.bulanan', compact('transaksi', 'summary', 'bulan', 'dailyData'));
    }

    public function export()
    {
        return view('laporan.export');
    }
    
    public function exportTransaksi(Request $request)
    {
        $filename = 'laporan_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new TransaksiExport($request->all()), $filename);
    }
    
    public function exportPemasukan(Request $request)
    {
        $filename = 'laporan_pemasukan_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PemasukanPengeluaranExport($request->all()), $filename);
    }
    
    public function exportExcel(Request $request)
    {
        $filename = 'laporan_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new TransaksiExport($request->all()), $filename);
    }
    
    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->format('Y-m-d'));
        $status = $request->get('status');
        
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai]);
            
        if ($status) {
            $query->where('status', $status);
        }
        
        $transaksi = $query->latest()->get();
        
        $summary = [
            'total_transaksi' => $query->count(),
            'total_pendapatan' => $query->sum('total_harga'),
            'transaksi_pending' => $query->where('status', 'pending')->count(),
            'transaksi_selesai' => $query->where('status', 'selesai')->count(),
        ];
        
        $pdf = Pdf::loadView('laporan.pdf', compact('transaksi', 'summary', 'tanggalMulai', 'tanggalSelesai'));
        
        return $pdf->stream('laporan_transaksi_' . date('Y-m-d') . '.pdf');
    }
}
