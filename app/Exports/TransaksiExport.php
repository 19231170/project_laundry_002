<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.layanan']);

        // Apply filters
        if (!empty($this->filters['tanggal_mulai'])) {
            $query->where('tanggal_masuk', '>=', $this->filters['tanggal_mulai']);
        }

        if (!empty($this->filters['tanggal_selesai'])) {
            $query->where('tanggal_masuk', '<=', $this->filters['tanggal_selesai']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Nama Pelanggan',
            'Telepon Pelanggan',
            'Tanggal Masuk',
            'Tanggal Selesai',
            'Status',
            'Total Harga',
            'Detail Layanan',
            'Catatan'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Format detail layanan
        $detailLayanan = $row->detailTransaksi->map(function ($detail) {
            return $detail->layanan->nama_layanan . ' (' . $detail->jumlah . ' ' . $detail->layanan->satuan . ')';
        })->implode(', ');

        return [
            $row->kode_transaksi,
            $row->pelanggan->nama,
            $row->pelanggan->telepon,
            $row->tanggal_masuk ? Carbon::parse($row->tanggal_masuk)->format('d/m/Y') : '',
            $row->tanggal_selesai ? Carbon::parse($row->tanggal_selesai)->format('d/m/Y') : '',
            ucfirst($row->status),
            'Rp ' . number_format($row->total_harga, 0, ',', '.'),
            $detailLayanan,
            $row->catatan
        ];
    }
}
