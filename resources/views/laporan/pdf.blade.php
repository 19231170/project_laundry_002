<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .summary-item {
            background: #f5f5f5;
            padding: 15px;
            margin: 5px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            min-width: 150px;
        }
        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .summary-item .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.pending {
            background-color: #ffeaa7;
            color: #d63031;
        }
        .status.proses {
            background-color: #fdcb6e;
            color: #e17055;
        }
        .status.selesai {
            background-color: #55efc4;
            color: #00b894;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI LAUNDRY</h1>
        <p>Periode: {{ date('d/m/Y', strtotime($tanggalMulai)) }} - {{ date('d/m/Y', strtotime($tanggalSelesai)) }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_transaksi']) }}</div>
            <div class="label">Total Transaksi</div>
        </div>
        <div class="summary-item">
            <div class="value">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="label">Total Pendapatan</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ number_format($summary['transaksi_selesai']) }}</div>
            <div class="label">Transaksi Selesai</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ number_format($summary['transaksi_pending']) }}</div>
            <div class="label">Transaksi Pending</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Pelanggan</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->pelanggan->nama }}</td>
                    <td class="text-center">{{ $t->tanggal_masuk ? $t->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $t->tanggal_selesai ? $t->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        <span class="status {{ $t->status }}">{{ ucfirst($t->status) }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat otomatis oleh sistem laundry pada {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
