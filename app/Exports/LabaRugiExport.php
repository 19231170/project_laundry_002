<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabaRugiExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $tanggalMulai;
    protected $tanggalAkhir;

    public function __construct($data, $tanggalMulai, $tanggalAkhir)
    {
        $this->data = $data;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function array(): array
    {
        $rows = [];

        // Summary section
        $rows[] = ['LAPORAN LABA RUGI'];
        $rows[] = ['Periode: ' . date('d/m/Y', strtotime($this->tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($this->tanggalAkhir))];
        $rows[] = [''];

        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Pemasukan', $this->formatRupiah($this->data['summary']['total_pemasukan'])];
        $rows[] = ['Total Pengeluaran', $this->formatRupiah($this->data['summary']['total_pengeluaran'])];
        $rows[] = ['Laba/Rugi', $this->formatRupiah($this->data['summary']['laba_rugi'])];
        $rows[] = ['Persentase Laba', $this->data['summary']['persentase_laba'] . '%'];
        $rows[] = [''];

        // Pemasukan section
        $rows[] = ['RINCIAN PEMASUKAN'];
        $rows[] = ['No', 'Tanggal', 'Kode', 'Customer', 'Total'];
        
        if (count($this->data['pemasukan']) > 0) {
            foreach ($this->data['pemasukan'] as $index => $item) {
                $rows[] = [
                    $index + 1,
                    date('d/m/Y', strtotime($item['tanggal'])),
                    $item['kode'],
                    $item['customer']['nama'] ?? '-',
                    $item['total']
                ];
            }
        } else {
            $rows[] = ['Tidak ada data pemasukan'];
        }
        
        $rows[] = ['', '', '', 'Total', $this->data['summary']['total_pemasukan']];
        $rows[] = [''];

        // Pengeluaran section
        $rows[] = ['RINCIAN PENGELUARAN'];
        $rows[] = ['No', 'Tanggal', 'Kode', 'Kategori', 'Keterangan', 'Total'];
        
        if (count($this->data['pengeluaran']) > 0) {
            foreach ($this->data['pengeluaran'] as $index => $item) {
                $rows[] = [
                    $index + 1,
                    date('d/m/Y', strtotime($item['tanggal'])),
                    $item['kode'],
                    $item['kategori_pengeluaran']['nama'] ?? '-',
                    $item['keterangan'] ?? '-',
                    $item['total']
                ];
            }
        } else {
            $rows[] = ['Tidak ada data pengeluaran'];
        }
        
        $rows[] = ['', '', '', '', 'Total', $this->data['summary']['total_pengeluaran']];
        $rows[] = [''];

        // Monthly data section
        $rows[] = ['TREN BULANAN'];
        $rows[] = ['Bulan', 'Pemasukan', 'Pengeluaran', 'Laba/Rugi'];
        
        foreach ($this->data['monthly_data'] as $item) {
            $rows[] = [
                $item['bulan'],
                $item['pemasukan'],
                $item['pengeluaran'],
                $item['laba']
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return []; // Headings are included in the array
    }

    public function title(): string
    {
        return 'Laporan Laba Rugi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
            13 => ['font' => ['bold' => true]],
        ];
    }

    private function formatRupiah($nominal)
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}
