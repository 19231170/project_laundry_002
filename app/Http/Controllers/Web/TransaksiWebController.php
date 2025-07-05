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
    public function index()
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->latest()
            ->paginate(10);
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
                'catatan' => $request->catatan,
                'total_harga' => 0,
                'status' => $request->status ?? 'pending'
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

            $transaksi->update(['total_harga' => $totalHarga]);

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
        return view('transaksi.show', compact('transaksi'));
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

            $transaksi->update(['total_harga' => $totalHarga]);

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
