<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkCreateTransaksiRequest;
use App\Models\DetailTransaksi;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BulkTransaksiController extends Controller
{
    public function create()
    {
        $pelanggan = Pelanggan::all();
        $layanan = Layanan::all();

        return view('transaksi.bulk-create', compact('pelanggan', 'layanan'));
    }

    public function store(BulkCreateTransaksiRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $createdKodes = [];

        DB::beginTransaction();
        try {
            foreach ($validated['transaksis'] as $transaksiData) {
                $layananData = $transaksiData['layanan'];
                unset($transaksiData['layanan']);

                $transaksi = Transaksi::create(array_merge($transaksiData, [
                    'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                    'status' => 'pending',
                    'status_pembayaran' => 'belum_lunas',
                    'total_harga' => 0,
                    'jumlah_dibayar' => 0,
                    'sisa_pembayaran' => 0,
                ]));

                $totalHarga = 0;

                foreach ($layananData as $item) {
                    $layanan = Layanan::find($item['layanan_id']);
                    $subtotal = $layanan->harga * $item['jumlah'];

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'layanan_id' => $item['layanan_id'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $layanan->harga,
                        'subtotal' => $subtotal,
                    ]);

                    $totalHarga += $subtotal;
                }

                $transaksi->update(['total_harga' => $totalHarga]);
                $createdKodes[] = $transaksi->kode_transaksi;
            }

            DB::commit();

            $count = count($createdKodes);
            $message = $count === 1
                ? "Transaksi berhasil dibuat: {$createdKodes[0]}"
                : "{$count} transaksi berhasil dibuat";

            return redirect()->route('transaksi.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Gagal membuat transaksi: '.$e->getMessage())
                ->withInput();
        }
    }
}
