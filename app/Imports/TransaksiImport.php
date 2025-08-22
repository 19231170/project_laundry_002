<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Validasi data yang diperlukan
            if (empty($row['nama_pelanggan']) || empty($row['layanan']) || empty($row['total_harga'])) {
                continue;
            }

            DB::beginTransaction();
            try {
                // Cari atau buat pelanggan
                $pelanggan = Pelanggan::firstOrCreate(
                    ['nama' => $row['nama_pelanggan']],
                    [
                        'telepon' => $row['telepon_pelanggan'] ?? null,
                        'alamat' => $row['alamat_pelanggan'] ?? null,
                        'email' => $row['email_pelanggan'] ?? null,
                    ]
                );

                // Buat transaksi
                $transaksi = Transaksi::create([
                    'kode_transaksi' => $row['kode_transaksi'] ?? Transaksi::generateKodeTransaksi(),
                    'pelanggan_id' => $pelanggan->id,
                    'total_harga' => $row['total_harga'],
                    'tanggal_masuk' => $row['tanggal_masuk'] ? Carbon::parse($row['tanggal_masuk']) : Carbon::now(),
                    'tanggal_selesai' => $row['tanggal_selesai'] ? Carbon::parse($row['tanggal_selesai']) : null,
                    'status' => $row['status'] ?? 'pending',
                    'catatan' => $row['catatan'] ?? null,
                ]);

                // Parse detail layanan (format: "Layanan1 (2 KG), Layanan2 (1 PCS)")
                $detailLayanan = explode(',', $row['detail_layanan']);
                foreach ($detailLayanan as $detail) {
                    $detail = trim($detail);
                    if (preg_match('/(.+)\s*\((\d+(?:\.\d+)?)\s*(\w+)\)/', $detail, $matches)) {
                        $namaLayanan = trim($matches[1]);
                        $jumlah = floatval($matches[2]);
                        $satuan = trim($matches[3]);

                        $layanan = Layanan::where('nama_layanan', $namaLayanan)
                            ->where('satuan', $satuan)
                            ->first();

                        if ($layanan) {
                            DetailTransaksi::create([
                                'transaksi_id' => $transaksi->id,
                                'layanan_id' => $layanan->id,
                                'jumlah' => $jumlah,
                                'harga_satuan' => $layanan->harga,
                                'subtotal' => $layanan->harga * $jumlah,
                            ]);
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                // Log error atau handle sesuai kebutuhan
            }
        }
    }
}
