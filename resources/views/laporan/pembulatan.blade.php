<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Pembulatan (Receh)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Laporan Pembulatan</h3>
                    <form id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                        
                        <div>
                            <label for="jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Pembulatan</label>
                            <select id="jenis" name="jenis" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="semua">Semua</option>
                                <option value="positif">Pembulatan Ke Atas (Positif)</option>
                                <option value="negatif">Pembulatan Ke Bawah (Negatif)</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                Tampilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ringkasan Pembulatan</h3>
                        <div>
                            <button id="btn-export-excel" class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Export Excel
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Total Transaksi dengan Pembulatan</h4>
                            <p class="text-2xl font-bold text-gray-800" id="total-transaksi-pembulatan">0</p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg border border-green-200 p-4 shadow-sm">
                            <h4 class="text-sm font-medium text-green-600 mb-2">Total Pembulatan Positif</h4>
                            <p class="text-2xl font-bold text-green-700" id="total-pembulatan-positif">Rp 0</p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg border border-red-200 p-4 shadow-sm">
                            <h4 class="text-sm font-medium text-red-600 mb-2">Total Pembulatan Negatif</h4>
                            <p class="text-2xl font-bold text-red-700" id="total-pembulatan-negatif">Rp 0</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar Transaksi dengan Pembulatan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kode Transaksi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pelanggan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Harga Asli
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pembulatan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Setelah Pembulatan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700" id="transaksi-pembulatan-table">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to load report
            function loadReport() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const jenis = document.getElementById('jenis').value;
                
                fetch(`/api/v1/laporan/pembulatan?start_date=${startDate}&end_date=${endDate}&jenis=${jenis}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Update summary stats
                            document.getElementById('total-transaksi-pembulatan').textContent = data.data.total_transaksi || 0;
                            document.getElementById('total-pembulatan-positif').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.total_pembulatan_positif || 0);
                            document.getElementById('total-pembulatan-negatif').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.total_pembulatan_negatif || 0);
                            
                            // Update table
                            const tableBody = document.getElementById('transaksi-pembulatan-table');
                            tableBody.innerHTML = '';
                            
                            if (data.data.transaksi.length > 0) {
                                data.data.transaksi.forEach(transaksi => {
                                    const row = document.createElement('tr');
                                    
                                    // Format date
                                    const tanggal = new Date(transaksi.tanggal_masuk).toLocaleDateString('id-ID', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    });
                                    
                                    // Format pembulatan with color
                                    const pembulatanValue = parseInt(transaksi.pembulatan);
                                    const pembulatanClass = pembulatanValue > 0 
                                        ? 'text-green-600 font-medium' 
                                        : (pembulatanValue < 0 ? 'text-red-600 font-medium' : 'text-gray-600');
                                    
                                    row.innerHTML = `
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${tanggal}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${transaksi.kode_transaksi}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${transaksi.pelanggan.nama}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            Rp ${new Intl.NumberFormat('id-ID').format(transaksi.total_harga)}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm ${pembulatanClass}">
                                            Rp ${new Intl.NumberFormat('id-ID').format(transaksi.pembulatan)}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                                            Rp ${new Intl.NumberFormat('id-ID').format(transaksi.total_setelah_pembulatan)}
                                        </td>
                                    `;
                                    
                                    tableBody.appendChild(row);
                                });
                            } else {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Tidak ada data transaksi dengan pembulatan
                                        </td>
                                    </tr>
                                `;
                            }
                        } else {
                            // Handle error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal memuat data laporan pembulatan',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching report:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat data',
                        });
                    });
            }
            
            // Handle form submission
            document.getElementById('filter-form').addEventListener('submit', function(e) {
                e.preventDefault();
                loadReport();
            });
            
            // Handle export button
            document.getElementById('btn-export-excel').addEventListener('click', function() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const jenis = document.getElementById('jenis').value;
                
                window.location.href = `/api/v1/laporan/export/pembulatan?start_date=${startDate}&end_date=${endDate}&jenis=${jenis}`;
            });
            
            // Load initial report
            loadReport();
        });
    </script>
</x-app-layout>
