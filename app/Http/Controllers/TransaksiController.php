<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $transaksi
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'pelanggan' => $pelanggan,
                'layanan' => $layanan
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
            'layanan' => 'required|array',
            'layanan.*.layanan_id' => 'required|exists:layanan,id',
            'layanan.*.jumlah' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $request->tanggal_selesai,
                'catatan' => $request->catatan,
                'total_harga' => 0,
                'status' => 'pending'
            ]);

            $totalHarga = 0;
            
            // Create detail transaksi
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

            // Update total harga
            $transaksi->update(['total_harga' => $totalHarga]);

            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaksi->load(['pelanggan', 'detailTransaksi.layanan'])
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->find($id);
        
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $transaksi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->find($id);
        
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
        
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'transaksi' => $transaksi,
                'pelanggan' => $pelanggan,
                'layanan' => $layanan
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::find($id);
        
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

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
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update transaksi
            $transaksi->update([
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
                'catatan' => $request->catatan,
            ]);

            // Delete existing detail transaksi
            DetailTransaksi::where('transaksi_id', $transaksi->id)->delete();

            $totalHarga = 0;
            
            // Create new detail transaksi
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

            // Update total harga
            $transaksi->update(['total_harga' => $totalHarga]);

            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil diupdate',
                'data' => $transaksi->load(['pelanggan', 'detailTransaksi.layanan'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaksi::find($id);
        
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Delete detail transaksi first
            DetailTransaksi::where('transaksi_id', $transaksi->id)->delete();
            
            // Delete transaksi
            $transaksi->delete();

            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate struk PDF
     */
    public function generateStruk(string $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan'])
            ->find($id);
        
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $pdf = PDF::loadView('struk.template', compact('transaksi'));
        
        return $pdf->stream('Struk-' . $transaksi->kode_transaksi . '.pdf');
    }
}
