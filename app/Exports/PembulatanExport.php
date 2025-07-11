<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Transaksi;
use Carbon\Carbon;

class PembulatanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = Transaksi::with(['pelanggan'])
            ->where('pembulatan', '!=', 0);

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->where('tanggal_masuk', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('tanggal_masuk', '<=', $this->filters['end_date']);
        }

        // Filter berdasarkan jenis pembulatan
        if (!empty($this->filters['jenis'])) {
            if ($this->filters['jenis'] === 'positif') {
                $query->where('pembulatan', '>', 0);
            } elseif ($this->filters['jenis'] === 'negatif') {
                $query->where('pembulatan', '<', 0);
            }
        }

        return $query->orderBy('tanggal_masuk', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kode Transaksi',
            'Pelanggan',
            'Total Harga Asli',
            'Pembulatan',
            'Total Setelah Pembulatan'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $row->tanggal_masuk ? Carbon::parse($row->tanggal_masuk)->format('d/m/Y') : '',
            $row->kode_transaksi,
            $row->pelanggan->nama ?? '-',
            'Rp ' . number_format($row->total_harga, 0, ',', '.'),
            'Rp ' . number_format($row->pembulatan, 0, ',', '.'),
            'Rp ' . number_format($row->total_setelah_pembulatan, 0, ',', '.')
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Get the last row
        $lastRow = $sheet->getHighestRow();
        
        // Set report title
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Laporan Pembulatan (Receh)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Set period
        $startDate = !empty($this->filters['start_date']) ? Carbon::parse($this->filters['start_date'])->format('d/m/Y') : Carbon::now()->startOfMonth()->format('d/m/Y');
        $endDate = !empty($this->filters['end_date']) ? Carbon::parse($this->filters['end_date'])->format('d/m/Y') : Carbon::now()->format('d/m/Y');
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Periode: ' . $startDate . ' - ' . $endDate);
        
        // Add empty row
        $sheet->mergeCells('A3:G3');
        
        // Style headers
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'f0f0f0',
                ],
            ],
        ]);
        
        // Calculate summary row
        $summaryRow = $lastRow + 2;
        $sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Total Pembulatan');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
        
        // Sum the pembulatan column
        $sheet->setCellValue('F' . $summaryRow, '=SUM(F5:F' . $lastRow . ')');
        $sheet->getStyle('F' . $summaryRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        
        // Auto size columns
        foreach(range('A','G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        return [
            4 => ['font' => ['bold' => true]],
        ];
    }
}
