<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Laundry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pelanggan</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="total-pelanggan">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaksi Hari Ini</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="transaksi-hari-ini">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendapatan Hari Ini</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="pendapatan-hari-ini">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaksi Proses</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="transaksi-proses">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 15h.01"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Dibayar</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="belum-dibayar">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat</h3>
                        <div class="space-y-3">
                            <a href="{{ route('transaksi.create') }}" class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Transaksi Baru
                            </a>
                            <a href="{{ route('transaksi.index') }}?filter=belum_lunas" class="block w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Lihat Belum Lunas
                            </a>
                            <a href="{{ route('pelanggan.create') }}" class="block w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Pelanggan
                            </a>
                            <a href="{{ route('layanan.create') }}" class="block w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Layanan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Transaksi Terbaru</h3>
                        <div class="space-y-3" id="transaksi-terbaru">
                            <div class="text-gray-500 dark:text-gray-400">Memuat data...</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Status Pembayaran</h3>
                        <div id="payment-status" class="mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Lunas</span>
                                <span id="percent-paid" class="text-sm font-medium text-gray-900">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="payment-progress" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div class="bg-green-50 p-3 rounded-md">
                                    <p class="text-sm text-gray-500">Transaksi Lunas</p>
                                    <p id="count-paid" class="text-xl font-bold text-gray-800">0</p>
                                </div>
                                <div class="bg-red-50 p-3 rounded-md">
                                    <p class="text-sm text-gray-500">Transaksi Belum Lunas</p>
                                    <p id="count-unpaid" class="text-xl font-bold text-gray-800">0</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('transaksi.index') }}?filter=belum_lunas" class="block w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Lihat Belum Lunas
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Laporan</h3>
                        <div class="space-y-3">
                            <a href="{{ route('laporan.index') }}" class="block w-full bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Laporan Harian
                            </a>
                            <a href="{{ route('laporan.index') }}" class="block w-full bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Laporan Bulanan
                            </a>
                            <a href="{{ route('laporan.pembulatan') }}" class="block w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Laporan Pembulatan (Receh)
                            </a>
                            <a href="{{ route('laporan.index') }}" class="block w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard data
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/v1/laporan/dashboard')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Basic stats
                        document.getElementById('total-pelanggan').textContent = data.data.total_pelanggan;
                        document.getElementById('transaksi-hari-ini').textContent = data.data.hari_ini.transaksi;
                        document.getElementById('pendapatan-hari-ini').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.hari_ini.pendapatan);
                        document.getElementById('transaksi-proses').textContent = data.data.status_transaksi.proses || 0;
                        
                        // Payment stats
                        if (data.data.pembayaran) {
                            document.getElementById('belum-dibayar').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.pembayaran.total_belum_lunas || 0);
                            
                            // Update payment status card
                            document.getElementById('count-paid').textContent = data.data.pembayaran.transaksi_lunas || 0;
                            document.getElementById('count-unpaid').textContent = data.data.pembayaran.transaksi_belum_lunas || 0;
                            
                            const percentPaid = data.data.pembayaran.persentase_lunas || 0;
                            document.getElementById('percent-paid').textContent = percentPaid + '%';
                            document.getElementById('payment-progress').style.width = percentPaid + '%';
                        }
                    }
                })
                .catch(error => console.error('Error loading dashboard data:', error));

            // Load recent transactions
            fetch('/api/v1/transaksi?limit=5')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const container = document.getElementById('transaksi-terbaru');
                        container.innerHTML = '';
                        
                        if (data.data.data.length > 0) {
                            data.data.data.forEach(transaksi => {
                                const div = document.createElement('div');
                                div.className = 'flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded';
                                
                                // Payment status badge
                                const paymentBadgeClass = transaksi.status_pembayaran === 'lunas' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800';
                                const paymentStatus = transaksi.status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Lunas';
                                
                                div.innerHTML = `
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${transaksi.kode_transaksi}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${transaksi.pelanggan.nama}</p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs px-2 py-1 mb-1 rounded bg-blue-100 text-blue-800">${transaksi.status}</span>
                                        <span class="text-xs px-2 py-1 rounded ${paymentBadgeClass}">${paymentStatus}</span>
                                    </div>
                                `;
                                container.appendChild(div);
                            });
                        } else {
                            container.innerHTML = '<div class="text-gray-500 dark:text-gray-400">Belum ada transaksi</div>';
                        }
                    }
                })
                .catch(error => console.error('Error loading recent transactions:', error));
        });
    </script>
</x-app-layout>
