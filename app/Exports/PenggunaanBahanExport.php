<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenggunaanBahanExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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

        // Title and period
        $rows[] = ['LAPORAN PENGGUNAAN BAHAN'];
        $rows[] = ['Periode: ' . date('d/m/Y', strtotime($this->tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($this->tanggalAkhir))];
        $rows[] = [''];

        // Summary
        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Nilai Bahan Terpakai', $this->formatRupiah($this->data['total_nilai_bahan'])];
        $rows[] = [''];

        // Bahan usage details
        $rows[] = ['RINCIAN PENGGUNAAN BAHAN'];
        $rows[] = ['No', 'Nama Bahan', 'Kategori', 'Jumlah', 'Rata-rata Harga', 'Total Nilai'];

        if (count($this->data['bahan_by_name']) > 0) {
            foreach ($this->data['bahan_by_name'] as $index => $item) {
                $rows[] = [
                    $index + 1,
                    $item['nama'],
                    $item['kategori'],
                    $item['qty'],
                    $this->formatRupiah($item['rata_harga']),
                    $this->formatRupiah($item['total_nilai'])
                ];
            }
            
            $rows[] = ['', '', '', '', 'Total', $this->formatRupiah($this->data['total_nilai_bahan'])];
        } else {
            $rows[] = ['Tidak ada data penggunaan bahan'];
        }
        
        $rows[] = [''];

        // Low stock items
        $rows[] = ['ITEM DENGAN STOK RENDAH'];
        $rows[] = ['Nama Item', 'Kategori', 'Stok', 'Stok Minimum', 'Satuan', 'Status'];

        if (count($this->data['low_stock_items']) > 0) {
            foreach ($this->data['low_stock_items'] as $item) {
                $rows[] = [
                    $item['nama'],
                    $item['kategori'],
                    $item['stok'],
                    $item['stok_minimum'],
                    $item['satuan'],
                    $item['status']
                ];
            }
        } else {
            $rows[] = ['Tidak ada item dengan stok rendah'];
        }

        return $rows;
    }

    public function headings(): array
    {
        return []; // Headings are included in the array
    }

    public function title(): string
    {
        return 'Laporan Penggunaan Bahan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            // Add a dynamic row number for the total row
            count($this->data['bahan_by_name']) + 9 => ['font' => ['bold' => true]],
            count($this->data['bahan_by_name']) + 11 => ['font' => ['bold' => true]],
            count($this->data['bahan_by_name']) + 12 => ['font' => ['bold' => true]],
        ];
    }

    private function formatRupiah($nominal)
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}
