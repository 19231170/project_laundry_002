<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengeluaranKategoriExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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
        $rows[] = ['LAPORAN PENGELUARAN PER KATEGORI'];
        $rows[] = ['Periode: ' . date('d/m/Y', strtotime($this->tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($this->tanggalAkhir))];
        $rows[] = [''];

        // Summary
        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Pengeluaran', $this->formatRupiah($this->data['total_pengeluaran'])];
        $rows[] = ['Jumlah Kategori', count($this->data['kategori_data'])];
        $rows[] = [''];

        // Kategori Pengeluaran
        $rows[] = ['RINCIAN PENGELUARAN PER KATEGORI'];
        $rows[] = ['No', 'Nama Kategori', 'Jumlah Transaksi', 'Total Pengeluaran', 'Persentase'];

        if (count($this->data['kategori_data']) > 0) {
            foreach ($this->data['kategori_data'] as $index => $item) {
                $rows[] = [
                    $index + 1,
                    $item['nama'],
                    $item['jumlah_transaksi'],
                    $this->formatRupiah($item['total_pengeluaran']),
                    $item['persentase'] . '%'
                ];
            }
            
            $rows[] = ['', '', 'Total', $this->formatRupiah($this->data['total_pengeluaran']), '100%'];
        } else {
            $rows[] = ['Tidak ada data pengeluaran'];
        }
        
        $rows[] = [''];

        // Rincian per kategori
        foreach ($this->data['detail_by_kategori'] as $kategoriId => $kategoriDetail) {
            $rows[] = ['DETAIL KATEGORI: ' . $kategoriDetail['kategori']];
            $rows[] = ['No', 'Tanggal', 'Kode', 'Keterangan', 'Supplier', 'Total'];
            
            foreach ($kategoriDetail['pengeluaran'] as $index => $pengeluaran) {
                $rows[] = [
                    $index + 1,
                    date('d/m/Y', strtotime($pengeluaran['tanggal'])),
                    $pengeluaran['kode'],
                    $pengeluaran['keterangan'],
                    $pengeluaran['supplier'] ?? '-',
                    $this->formatRupiah($pengeluaran['total'])
                ];
            }
            
            $totalKategori = collect($kategoriDetail['pengeluaran'])->sum('total');
            $rows[] = ['', '', '', '', 'Total', $this->formatRupiah($totalKategori)];
            $rows[] = [''];
        }

        return $rows;
    }

    public function headings(): array
    {
        return []; // Headings are included in the array
    }

    public function title(): string
    {
        return 'Pengeluaran Per Kategori';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
        ];
    }

    private function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}
