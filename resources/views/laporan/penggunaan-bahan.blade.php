<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Penggunaan Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Laporan Penggunaan Bahan</h2>
                    </div>

                    <!-- Filter Form -->
                    <form id="filter-form" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                                    value="{{ date('Y-m-01') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" 
                                    value="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="kategori_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                <select name="kategori_id" id="kategori_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Semua Kategori</option>
                                    <!-- Kategori options will be loaded via JavaScript -->
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-biaya">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Biaya Bahan</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="total-transaksi">0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Jumlah Transaksi</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="rata-rata">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Rata-rata per Transaksi</div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button id="btn-export-excel" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export Excel
                        </button>
                        <button id="btn-export-pdf" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Export PDF
                        </button>
                        <button id="btn-print" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Print
                        </button>
                    </div>

                    <!-- Usage Trend Chart -->
                    <div class="mb-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-4">Trend Penggunaan Bahan</h3>
                                <div class="h-80">
                                    <canvas id="trend-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto mb-8">
                        <h3 class="text-lg font-semibold mb-4">Detail Penggunaan Bahan</h3>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Item
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Jumlah
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Satuan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Harga Satuan Rata-Rata
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Biaya
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Transaksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Loading data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Supplier Stats -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Penggunaan per Supplier</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-4">
                                    <div class="h-80">
                                        <canvas id="supplier-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-800">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Supplier
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Jumlah Transaksi
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Total Biaya
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="supplier-table-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        Loading data...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let trendChart = null;
            let supplierChart = null;
            
            // Load categories for filter dropdown
            loadCategories();
            
            // Load initial data
            loadData();
            
            // Handle filter form submission
            document.getElementById('filter-form').addEventListener('submit', function(e) {
                e.preventDefault();
                loadData();
            });
            
            function loadCategories() {
                fetch('/api/v1/kategori-pengeluaran')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const select = document.getElementById('kategori_id');
                            
                            data.data.forEach(kategori => {
                                const option = document.createElement('option');
                                option.value = kategori.id;
                                option.textContent = kategori.nama_kategori;
                                select.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading categories:', error);
                    });
            }
            
            function loadData() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                const kategoriId = document.getElementById('kategori_id').value;
                
                let url = `/api/v1/laporan/penggunaan-bahan?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}`;
                if (kategoriId) {
                    url += `&kategori_id=${kategoriId}`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateSummary(data.data.summary);
                            updateTable(data.data.penggunaan_bahan);
                            updateTrendChart(data.data.trend_harian);
                            updateSupplierChart(data.data.penggunaan_by_supplier);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    });
            }
            
            function updateSummary(summary) {
                document.getElementById('total-biaya').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.total_biaya);
                document.getElementById('total-transaksi').textContent = summary.total_transaksi;
                document.getElementById('rata-rata').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.rata_rata_per_transaksi);
            }
            
            function updateTable(items) {
                const tableBody = document.getElementById('table-body');
                tableBody.innerHTML = '';
                
                if (items.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data untuk periode yang dipilih
                        </td>
                    `;
                    tableBody.appendChild(row);
                    return;
                }
                
                items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${item.nama_item}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            ${new Intl.NumberFormat('id-ID').format(item.total_jumlah)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                            ${item.satuan}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.harga_satuan_rata)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.total_biaya)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                            ${item.jumlah_transaksi}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
            
            function updateTrendChart(trendData) {
                const ctx = document.getElementById('trend-chart').getContext('2d');
                
                // Prepare chart data
                const labels = trendData.map(item => new Date(item.tanggal).toLocaleDateString('id-ID'));
                const values = trendData.map(item => item.total_biaya);
                const itemCounts = trendData.map(item => item.jumlah_item);
                
                // Destroy previous chart instance if it exists
                if (trendChart) {
                    trendChart.destroy();
                }
                
                // Create new chart
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Biaya',
                                data: values,
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Jumlah Item',
                                data: itemCounts,
                                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                                borderColor: 'rgba(245, 158, 11, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Biaya (Rp)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Jumlah Item'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        
                                        if (context.dataset.yAxisID === 'y') {
                                            label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                        } else {
                                            label += context.raw;
                                        }
                                        
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            function updateSupplierChart(supplierData) {
                const tableBody = document.getElementById('supplier-table-body');
                tableBody.innerHTML = '';
                
                if (!supplierData || supplierData.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data supplier
                        </td>
                    `;
                    tableBody.appendChild(row);
                    
                    // Clear chart
                    if (supplierChart) {
                        supplierChart.destroy();
                        supplierChart = null;
                    }
                    return;
                }
                
                // Update table
                supplierData.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${item.supplier_nama || 'Tanpa Supplier'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            ${item.jumlah_transaksi}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.total_biaya)}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Update chart
                const ctx = document.getElementById('supplier-chart').getContext('2d');
                const labels = supplierData.map(item => item.supplier_nama || 'Tanpa Supplier');
                const values = supplierData.map(item => item.total_biaya);
                
                // Generate colors
                const colors = generateChartColors(supplierData.length);
                
                // Destroy previous chart instance if it exists
                if (supplierChart) {
                    supplierChart.destroy();
                }
                
                // Create new chart
                supplierChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Pembelian',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(c => c.replace('0.7', '1')),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = 'Total Pembelian: ';
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Helper function to generate chart colors
            function generateChartColors(count) {
                const baseColors = [
                    'rgba(239, 68, 68, 0.7)',    // red
                    'rgba(245, 158, 11, 0.7)',   // amber
                    'rgba(16, 185, 129, 0.7)',   // emerald
                    'rgba(59, 130, 246, 0.7)',   // blue
                    'rgba(139, 92, 246, 0.7)',   // violet
                    'rgba(236, 72, 153, 0.7)',   // pink
                    'rgba(14, 165, 233, 0.7)',   // sky
                    'rgba(168, 85, 247, 0.7)',   // purple
                    'rgba(251, 146, 60, 0.7)',   // orange
                    'rgba(20, 184, 166, 0.7)'    // teal
                ];
                
                const colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(baseColors[i % baseColors.length]);
                }
                return colors;
            }
            
            // Handle export buttons
            document.getElementById('btn-export-excel').addEventListener('click', function() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                const kategoriId = document.getElementById('kategori_id').value;
                
                let url = `/api/v1/laporan/export/penggunaan-bahan?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&format=xlsx`;
                if (kategoriId) {
                    url += `&kategori_id=${kategoriId}`;
                }
                
                window.location.href = url;
            });
            
            document.getElementById('btn-export-pdf').addEventListener('click', function() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                const kategoriId = document.getElementById('kategori_id').value;
                
                let url = `/api/v1/laporan/export/penggunaan-bahan?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&format=pdf`;
                if (kategoriId) {
                    url += `&kategori_id=${kategoriId}`;
                }
                
                window.location.href = url;
            });
            
            document.getElementById('btn-print').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
    @endpush
</x-app-layout>
