<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Detail Pelanggan</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('pelanggan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pelanggan</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Nama Pelanggan:</span>
                                <span class="text-gray-900">{{ $pelanggan->nama }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Telepon:</span>
                                <span class="text-gray-900">{{ $pelanggan->telepon ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Email:</span>
                                <span class="text-gray-900">{{ $pelanggan->email ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Alamat</h3>
                        <p class="text-gray-900">{{ $pelanggan->alamat ?: 'Tidak ada alamat tercatat' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $pelanggan->transaksi->count() }}</div>
                            <div class="text-sm text-gray-600">Total Transaksi</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pelanggan->transaksi->count() > 0 ? $pelanggan->transaksi->sortByDesc('created_at')->first()->created_at->diffForHumans() : '-' }}
                            </div>
                            <div class="text-sm text-gray-600">Transaksi Terakhir</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                Rp {{ number_format($pelanggan->transaksi->sum('total_harga'), 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-600">Total Pengeluaran</div>
                        </div>
                    </div>
                </div>

                @if($pelanggan->transaksi->count() > 0)
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
                                            Tanggal Masuk
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Selesai
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pelanggan->transaksi->sortByDesc('created_at')->take(5) as $transaksi)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('transaksi.show', $transaksi->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $transaksi->kode_transaksi }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaksi->tanggal_masuk ? $transaksi->tanggal_masuk->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaksi->tanggal_selesai ? $transaksi->tanggal_selesai->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $transaksi->status == 'selesai' ? 'bg-green-100 text-green-800' : 
                                                    ($transaksi->status == 'proses' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($transaksi->status) }}
                                                </span>
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
</div>
</x-app-layout>
