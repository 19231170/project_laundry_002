<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    /**
     * Show main POS interface.
     */
    public function index()
    {
        $layanan = Layanan::orderBy('satuan')->orderBy('nama_layanan')->get();
        $pelanggan = Pelanggan::orderBy('nama')->get();

        // Group layanan by satuan for display
        $layananGrouped = $layanan->groupBy('satuan');

        return view('pos.index', compact('layanan', 'layananGrouped', 'pelanggan'));
    }

    /**
     * Search customers for autocomplete.
     */
    public function searchPelanggan(Request $request)
    {
        $query = $request->get('q', '');

        $pelanggan = Pelanggan::where('nama', 'like', "%{$query}%")
            ->orWhere('telepon', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'nama', 'telepon', 'alamat']);

        return response()->json($pelanggan);
    }

    /**
     * Quick create customer from POS.
     */
    public function storePelanggan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $pelanggan = Pelanggan::create($request->only(['nama', 'telepon', 'alamat']));

        return response()->json(['success' => true, 'pelanggan' => $pelanggan]);
    }

    /**
     * Process checkout and create transaction.
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'items' => 'required|array|min:1',
            'items.*.layanan_id' => 'required|exists:layanan,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'pembulatan' => 'nullable|integer',
            'total_setelah_pembulatan' => 'required|numeric|min:0',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:belum_lunas,lunas',
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(' ', $validator->errors()->all()), 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $totalSetelahPembulatan = $request->total_setelah_pembulatan;
            $jumlahDibayar = (float) ($request->jumlah_dibayar ?? 0);
            $sisaPembayaran = $totalSetelahPembulatan - $jumlahDibayar;

            // If status is lunas, set payment to full
            if ($request->status_pembayaran === 'lunas') {
                $jumlahDibayar = $totalSetelahPembulatan;
                $sisaPembayaran = 0;
            }

            $transaksi = Transaksi::create([
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'pelanggan_id' => $request->pelanggan_id,
                'user_id' => session('pos_user_id'), // Track who created this transaction
                'tanggal_masuk' => now()->toDateString(),
                'tanggal_selesai' => null,
                'status' => 'pending',
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_pembayaran' => $request->status_pembayaran === 'lunas' ? now()->toDateString() : null,
                'total_harga' => $request->total_harga,
                'pembulatan' => $request->pembulatan ?? 0,
                'total_setelah_pembulatan' => $totalSetelahPembulatan,
                'jumlah_dibayar' => $jumlahDibayar,
                'sisa_pembayaran' => $sisaPembayaran,
                'catatan' => $request->catatan,
            ]);

            // Create detail transaksi
            foreach ($request->items as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_id' => $item['layanan_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            // Calculate change
            $kembalian = max(0, $jumlahDibayar - $totalSetelahPembulatan);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat!',
                'transaksi' => [
                    'id' => $transaksi->id,
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'total' => $totalSetelahPembulatan,
                    'dibayar' => $jumlahDibayar,
                    'kembalian' => $kembalian,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get receipt data for printing.
     */
    public function receipt(Transaksi $transaksi)
    {
        $transaksi->load(['pelanggan', 'detailTransaksi.layanan', 'user']);

        return response()->json([
            'success' => true,
            'transaksi' => $transaksi,
        ]);
    }
}
