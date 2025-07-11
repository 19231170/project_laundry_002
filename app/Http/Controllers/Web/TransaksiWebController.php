<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.layanan']);
        
        // Filter by payment status if requested
        if ($request->has('filter')) {
            if ($request->filter === 'belum_lunas') {
                $query->where('status_pembayaran', 'belum_lunas');
            } elseif ($request->filter === 'lunas') {
                $query->where('status_pembayaran', 'lunas');
            }
        }
        
        // Filter by status if requested
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $transaksi = $query->latest()->paginate(10);
        
        // Preserve filter in pagination links
        $transaksi->appends($request->all());
        
        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();
        return view('transaksi.create', compact('pelanggan', 'layanan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'nullable|in:pending,proses,selesai,diambil',
            'status_pembayaran' => 'nullable|in:belum_lunas,lunas',
            'tanggal_pembayaran' => 'nullable|date',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'layanan' => 'required|array',
            'layanan.*.layanan_id' => 'required|exists:layanan,id',
            'layanan.*.jumlah' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status ?? 'pending',
                'status_pembayaran' => $request->status_pembayaran ?? 'belum_lunas',
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'catatan' => $request->catatan,
                'total_harga' => 0,
                'jumlah_dibayar' => 0,
                'sisa_pembayaran' => 0
            ]);

            $totalHarga = 0;
            
            foreach ($request->layanan as $layananData) {
                $layanan = Layanan::find($layananData['layanan_id']);
                $subtotal = $layanan->harga * $layananData['jumlah'];
                
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_id' => $layananData['layanan_id'],
                    'jumlah' => $layananData['jumlah'],
                    'harga_satuan' => $layanan->harga,
                    'subtotal' => $subtotal
                ]);
                
                $totalHarga += $subtotal;
            }

            // Handle pembulatan
            $pembulatan = $request->filled('pembulatan') ? $request->pembulatan : 0;
            $totalSetelahPembulatan = $request->filled('total_setelah_pembulatan') ? $request->total_setelah_pembulatan : $totalHarga;

            // Update total harga and payment info
            $jumlahDibayar = $request->filled('jumlah_dibayar') ? $request->jumlah_dibayar : 0;
            $sisaPembayaran = $totalSetelahPembulatan - $jumlahDibayar;
            
            // If marked as paid, set payment to total
            if ($request->status_pembayaran === 'lunas') {
                $jumlahDibayar = $totalSetelahPembulatan;
                $sisaPembayaran = 0;
            }
            
            $transaksi->update([
                'total_harga' => $totalHarga,
                'pembulatan' => $pembulatan,
                'total_setelah_pembulatan' => $totalSetelahPembulatan,
                'jumlah_dibayar' => $jumlahDibayar,
                'sisa_pembayaran' => $sisaPembayaran
            ]);

            DB::commit();
            
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dibuat dengan kode: ' . $transaksi->kode_transaksi);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['pelanggan', 'detailTransaksi.layanan']);
        
        // Get pengeluaran data related to the transaction date
        // We'll fetch pengeluaran from the same date as the transaction
        $pengeluaran = \App\Models\Pengeluaran::with(['kategori', 'detailPengeluaran.supplier'])
            ->where('tanggal', $transaksi->tanggal_masuk)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('transaksi.show', compact('transaksi', 'pengeluaran'));
    }

    public function edit(Transaksi $transaksi)
    {
        $transaksi->load(['pelanggan', 'detailTransaksi.layanan']);
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();
        return view('transaksi.edit', compact('transaksi', 'pelanggan', 'layanan'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'required|in:pending,proses,selesai,diambil',
            'status_pembayaran' => 'required|in:belum_lunas,lunas',
            'tanggal_pembayaran' => 'nullable|date',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'layanan' => 'required|array',
            'layanan.*.layanan_id' => 'required|exists:layanan,id',
            'layanan.*.jumlah' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $transaksi->update([
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'catatan' => $request->catatan,
            ]);

            DetailTransaksi::where('transaksi_id', $transaksi->id)->delete();

            $totalHarga = 0;
            
            foreach ($request->layanan as $layananData) {
                $layanan = Layanan::find($layananData['layanan_id']);
                $subtotal = $layanan->harga * $layananData['jumlah'];
                
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_id' => $layananData['layanan_id'],
                    'jumlah' => $layananData['jumlah'],
                    'harga_satuan' => $layanan->harga,
                    'subtotal' => $subtotal
                ]);
                
                $totalHarga += $subtotal;
            }

            // Handle pembulatan
            $pembulatan = $request->filled('pembulatan') ? $request->pembulatan : 0;
            $totalSetelahPembulatan = $request->filled('total_setelah_pembulatan') ? $request->total_setelah_pembulatan : $totalHarga;

            // Handle payment
            $jumlahDibayar = $request->filled('jumlah_dibayar') ? $request->jumlah_dibayar : 0;
            $sisaPembayaran = $totalSetelahPembulatan - $jumlahDibayar;
            
            // If marked as paid, set the payment amount to total
            if ($request->status_pembayaran === 'lunas') {
                $jumlahDibayar = $totalSetelahPembulatan;
                $sisaPembayaran = 0;
            }
            
            $transaksi->update([
                'total_harga' => $totalHarga,
                'pembulatan' => $pembulatan,
                'total_setelah_pembulatan' => $totalSetelahPembulatan,
                'jumlah_dibayar' => $jumlahDibayar,
                'sisa_pembayaran' => $sisaPembayaran
            ]);

            DB::commit();
            
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diupdate');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::beginTransaction();
        try {
            DetailTransaksi::where('transaksi_id', $transaksi->id)->delete();
            $transaksi->delete();

            DB::commit();
            
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('transaksi.index')->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
    
    public function generateStruk($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->findOrFail($id);

        $pdf = PDF::loadView('struk.template', compact('transaksi'));
        
        return $pdf->stream('Struk-' . $transaksi->kode_transaksi . '.pdf');
    }


}
