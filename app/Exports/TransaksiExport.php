<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;
    protected $rows = [];

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

        $transaksis = $query->orderBy('created_at', 'desc')->get();
        
        // Prepare rows with flattened detail transaksi
        $rows = new Collection();
        
        foreach ($transaksis as $t) {
            if ($t->detailTransaksi->count() > 0) {
                // Add a row for each detail
                foreach ($t->detailTransaksi as $detail) {
                    $rows->push([
                        'transaksi' => $t,
                        'detail' => $detail
                    ]);
                }
            } else {
                // Add a row without detail
                $rows->push([
                    'transaksi' => $t,
                    'detail' => null
                ]);
            }
        }
        
        return $rows;
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
            'Pembulatan',
            'Total Setelah Pembulatan',
            'Nama Layanan',
            'Jumlah',
            'Satuan',
            'Harga Satuan',
            'Subtotal',
            'Catatan'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $transaksi = $row['transaksi'];
        $detail = $row['detail'];
        
        $baseData = [
            $transaksi->kode_transaksi,
            $transaksi->pelanggan ? $transaksi->pelanggan->nama : '-',
            $transaksi->pelanggan ? $transaksi->pelanggan->telepon : '-',
            $transaksi->tanggal_masuk ? Carbon::parse($transaksi->tanggal_masuk)->format('d/m/Y') : '',
            $transaksi->tanggal_selesai ? Carbon::parse($transaksi->tanggal_selesai)->format('d/m/Y') : '',
            ucfirst($transaksi->status),
            'Rp ' . number_format($transaksi->total_harga, 0, ',', '.'),
            'Rp ' . number_format($transaksi->pembulatan ?? 0, 0, ',', '.'),
            'Rp ' . number_format($transaksi->total_setelah_pembulatan ?? $transaksi->total_harga, 0, ',', '.')
        ];
        
        // Add detail information if available
        $detailData = [];
        if ($detail) {
            $detailData = [
                $detail->layanan->nama_layanan ?? '',
                $detail->jumlah,
                $detail->layanan->satuan ?? '',
                'Rp ' . number_format($detail->harga_satuan, 0, ',', '.'),
                'Rp ' . number_format($detail->subtotal, 0, ',', '.'),
            ];
        } else {
            $detailData = ['', '', '', '', ''];
        }
        
        // Add catatan
        $detailData[] = $transaksi->catatan ?? '';
        
        return array_merge($baseData, $detailData);
    }
    
    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
