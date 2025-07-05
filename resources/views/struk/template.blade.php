<!DOCTYPE html>
<html>
<head>
    <title>Struk Laundry - {{ $transaksi->kode_transaksi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        .info-value {
            width: 60%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-label {
            font-weight: bold;
            font-size: 14px;
        }
        .total-value {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-proses {
            background-color: #17a2b8;
            color: #fff;
        }
        .status-selesai {
            background-color: #28a745;
            color: #fff;
        }
        .status-diambil {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAUNDRY DIGITAL</h1>
        <p>Jl. Contoh No. 123, Kota Contoh</p>
        <p>Telp: (021) 1234567 | Email: info@laundrydigital.com</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Kode Transaksi:</div>
            <div class="info-value">{{ $transaksi->kode_transaksi }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $transaksi->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Pelanggan:</div>
            <div class="info-value">{{ $transaksi->pelanggan->nama }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Telepon:</div>
            <div class="info-value">{{ $transaksi->pelanggan->telepon ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Alamat:</div>
            <div class="info-value">{{ $transaksi->pelanggan->alamat ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Masuk:</div>
            <div class="info-value">{{ $transaksi->tanggal_masuk ? \Carbon\Carbon::parse($transaksi->tanggal_masuk)->format('d/m/Y') : '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Selesai:</div>
            <div class="info-value">{{ $transaksi->tanggal_selesai ? \Carbon\Carbon::parse($transaksi->tanggal_selesai)->format('d/m/Y') : '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $transaksi->status }}">
                    {{ ucfirst($transaksi->status) }}
                </span>
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Layanan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksi as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detail->layanan->nama_layanan }}</td>
                <td class="text-center">{{ number_format($detail->jumlah, 2) }}</td>
                <td class="text-center">{{ $detail->layanan->satuan }}</td>
                <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">TOTAL PEMBAYARAN:</div>
            <div class="total-value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($transaksi->catatan)
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Catatan:</div>
            <div class="info-value">{{ $transaksi->catatan }}</div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Terima kasih telah menggunakan layanan kami!</p>
        <p>Struk ini dicetak pada {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
