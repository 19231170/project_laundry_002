<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Detail Transaksi</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('transaksi.struk', $transaksi->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Cetak Struk
                        </a>
                        <a href="{{ route('transaksi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Transaksi</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="font-medium text-gray-700">Kode Transaksi:</span>
                                <span class="text-gray-900">{{ $transaksi->kode_transaksi }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tanggal Masuk:</span>
                                <span class="text-gray-900">{{ $transaksi->tanggal_masuk ? $transaksi->tanggal_masuk->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tanggal Selesai:</span>
                                <span class="text-gray-900">{{ $transaksi->tanggal_selesai ? $transaksi->tanggal_selesai->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $transaksi->status == 'selesai' ? 'bg-green-100 text-green-800' : 
                                       ($transaksi->status == 'proses' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($transaksi->status) }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Total Asli:</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                            </div>
                            @if($transaksi->pembulatan != 0)
                            <div>
                                <span class="font-medium text-gray-700">Pembulatan:</span>
                                <span class="text-lg font-medium {{ $transaksi->pembulatan > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($transaksi->pembulatan, 0, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Total Setelah Pembulatan:</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ number_format($transaksi->total_setelah_pembulatan, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pelanggan</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="font-medium text-gray-700">Nama:</span>
                                <span class="text-gray-900">{{ $transaksi->pelanggan->nama }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Nomor Telepon:</span>
                                <span class="text-gray-900">{{ $transaksi->pelanggan->nomor_telepon }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Alamat:</span>
                                <span class="text-gray-900">{{ $transaksi->pelanggan->alamat ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($transaksi->catatan)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan</h3>
                        <p class="text-gray-900">{{ $transaksi->catatan }}</p>
                    </div>
                @endif

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Layanan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Layanan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Satuan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transaksi->detailTransaksi as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $detail->layanan->nama_layanan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $detail->layanan->satuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $detail->jumlah }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($pengeluaran) && $pengeluaran->count() > 0)
    <div class="mt-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pengeluaran Terkait</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keterangan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Biaya
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Detail
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pengeluaran as $p)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $p->kategori->nama_kategori ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $p->keterangan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($p->total_biaya, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <button type="button" 
                                            class="bg-blue-500 hover:bg-blue-700 text-white text-xs py-1 px-2 rounded"
                                            onclick="toggleDetails('pengeluaran-{{ $p->id }}')">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <tr id="pengeluaran-{{ $p->id }}" class="hidden bg-gray-50">
                                    <td colspan="4" class="px-6 py-4">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-700">Detail Pengeluaran:</p>
                                            @if($p->detailPengeluaran->count() > 0)
                                                <table class="min-w-full mt-2 divide-y divide-gray-200">
                                                    <thead class="bg-gray-100">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Jumlah</th>
                                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Harga Satuan</th>
                                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($p->detailPengeluaran as $detail)
                                                            <tr>
                                                                <td class="px-4 py-2 text-xs">{{ $detail->nama_item }}</td>
                                                                <td class="px-4 py-2 text-xs">{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                                                                <td class="px-4 py-2 text-xs">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                                                <td class="px-4 py-2 text-xs">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-gray-500 mt-1">Tidak ada detail pengeluaran</p>
                                            @endif
                                            @if($p->bukti_pembayaran)
                                                <div class="mt-2">
                                                    <p class="font-medium text-gray-700">Bukti Pembayaran:</p>
                                                    <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:underline">
                                                        Lihat Bukti
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>

<script>
    function toggleDetails(id) {
        const element = document.getElementById(id);
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    }
</script>
