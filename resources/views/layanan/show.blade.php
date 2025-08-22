<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Detail Layanan</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('layanan.edit', $layanan->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('layanan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Layanan</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Nama Layanan:</span>
                                <span class="text-gray-900">{{ $layanan->nama_layanan }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Satuan:</span>
                                <span class="text-gray-900">{{ $layanan->satuan }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Harga per Satuan:</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ number_format($layanan->harga, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Estimasi Waktu:</span>
                                <span class="text-gray-900">{{ $layanan->estimasi_waktu ?? '1' }} hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi</h3>
                        <p class="text-gray-900">{{ $layanan->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Layanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $layanan->detailTransaksi->count() }}</div>
                            <div class="text-sm text-gray-600">Total Transaksi</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $layanan->detailTransaksi->sum('jumlah') }}
                            </div>
                            <div class="text-sm text-gray-600">Total {{ $layanan->satuan }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                Rp {{ number_format($layanan->detailTransaksi->sum('subtotal'), 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-600">Total Pendapatan</div>
                        </div>
                    </div>
                </div>

                @if($layanan->detailTransaksi->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Transaksi Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kode Transaksi
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pelanggan
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
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
                                    @foreach($layanan->detailTransaksi->take(5) as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('transaksi.show', $detail->transaksi->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $detail->transaksi->kode_transaksi }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->transaksi->pelanggan->nama }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->transaksi->tanggal_masuk->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->jumlah }} {{ $layanan->satuan }}
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
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
