<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Transaksi;
use Carbon\Carbon;

class PemasukanPengeluaranExport implements FromCollection, WithHeadings, WithMapping
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
        $tanggalMulai = $this->filters['tanggal_mulai'] ?? Carbon::now()->startOfMonth();
        $tanggalSelesai = $this->filters['tanggal_selesai'] ?? Carbon::now()->endOfMonth();
        $tipe = $this->filters['tipe'] ?? 'harian';

        $query = Transaksi::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->where('status', 'selesai');

        switch ($tipe) {
            case 'harian':
                return $query->selectRaw('DATE(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
            case 'bulanan':
                return $query->selectRaw('DATE_FORMAT(tanggal_masuk, "%Y-%m") as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
            case 'tahunan':
                return $query->selectRaw('YEAR(tanggal_masuk) as periode, SUM(total_harga) as total_pemasukan, COUNT(*) as jumlah_transaksi')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->get();
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Periode',
            'Total Pemasukan',
            'Jumlah Transaksi',
            'Rata-rata per Transaksi'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $rataRata = $row->jumlah_transaksi > 0 ? $row->total_pemasukan / $row->jumlah_transaksi : 0;
        
        return [
            $row->periode,
            'Rp ' . number_format($row->total_pemasukan, 0, ',', '.'),
            $row->jumlah_transaksi,
            'Rp ' . number_format($rataRata, 0, ',', '.')
        ];
    }
}
