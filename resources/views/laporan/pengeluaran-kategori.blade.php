<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Pengeluaran per Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Laporan Pengeluaran per Kategori</h2>
                    </div>

                    <!-- Filter Form -->
                    <form id="filter-form" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="total-pengeluaran">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Pengeluaran</div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="jumlah-kategori">0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Kategori Terpakai</div>
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

                    <!-- Chart and Table -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-4">Distribusi Pengeluaran</h3>
                                <div class="h-80">
                                    <canvas id="category-pie-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-4">Detail per Kategori</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Kategori
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Jumlah Transaksi
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Total
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Persentase
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="category-table-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    Loading data...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trend -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-4">Trend Pengeluaran Bulanan per Kategori</h3>
                            <div class="h-80">
                                <canvas id="monthly-trend-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top 3 Categories Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div id="top-category-1" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-lg font-semibold mb-2" id="top-category-1-name">-</div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Pengeluaran:</span>
                                <span class="text-sm font-medium" id="top-category-1-value">Rp 0</span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Persentase:</span>
                                <span class="text-sm font-medium" id="top-category-1-percent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                                <div id="top-category-1-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>

                        <div id="top-category-2" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-lg font-semibold mb-2" id="top-category-2-name">-</div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Pengeluaran:</span>
                                <span class="text-sm font-medium" id="top-category-2-value">Rp 0</span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Persentase:</span>
                                <span class="text-sm font-medium" id="top-category-2-percent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                                <div id="top-category-2-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>

                        <div id="top-category-3" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-lg font-semibold mb-2" id="top-category-3-name">-</div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Pengeluaran:</span>
                                <span class="text-sm font-medium" id="top-category-3-value">Rp 0</span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Persentase:</span>
                                <span class="text-sm font-medium" id="top-category-3-percent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                                <div id="top-category-3-bar" class="bg-yellow-600 h-2.5 rounded-full" style="width: 0%"></div>
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
            let categoryPieChart = null;
            let monthlyTrendChart = null;
            
            // Load initial data
            loadData();
            
            // Handle filter form submission
            document.getElementById('filter-form').addEventListener('submit', function(e) {
                e.preventDefault();
                loadData();
            });
            
            function loadData() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                
                fetch(`/api/v1/laporan/pengeluaran-per-kategori?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateSummary(data.data.summary);
                            updateCategoryTable(data.data.pengeluaran_per_kategori);
                            updateCategoryChart(data.data.pengeluaran_per_kategori);
                            updateMonthlyTrendChart(data.data.trend_bulanan, data.data.kategori_list);
                            updateTopCategories(data.data.pengeluaran_per_kategori.slice(0, 3));
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    });
            }
            
            function updateSummary(summary) {
                document.getElementById('total-pengeluaran').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.total_pengeluaran);
                document.getElementById('jumlah-kategori').textContent = summary.jumlah_kategori;
            }
            
            function updateCategoryTable(categories) {
                const tableBody = document.getElementById('category-table-body');
                tableBody.innerHTML = '';
                
                if (categories.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data untuk periode yang dipilih
                        </td>
                    `;
                    tableBody.appendChild(row);
                    return;
                }
                
                categories.forEach(category => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${category.nama_kategori}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            ${category.jumlah_transaksi}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(category.total_biaya)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            ${category.persentase}%
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
            
            function updateCategoryChart(categories) {
                const ctx = document.getElementById('category-pie-chart').getContext('2d');
                
                // Prepare chart data
                const labels = categories.map(category => category.nama_kategori);
                const values = categories.map(category => category.total_biaya);
                
                // Generate colors
                const colors = generateChartColors(categories.length);
                
                // Destroy previous chart instance if it exists
                if (categoryPieChart) {
                    categoryPieChart.destroy();
                }
                
                // Create new chart
                categoryPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: '#ffffff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#1f2937'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        const value = context.raw;
                                        const percentage = Math.round(context.parsed * 100) / 100;
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        label += ' (' + percentage + '%)';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            function updateMonthlyTrendChart(trendData, categoryList) {
                const ctx = document.getElementById('monthly-trend-chart').getContext('2d');
                
                // Prepare chart data
                const labels = trendData.map(item => item.bulan);
                
                // Generate datasets for each category
                const datasets = [];
                const colors = generateChartColors(categoryList.length);
                
                categoryList.forEach((category, index) => {
                    const data = trendData.map(item => item[category] || 0);
                    datasets.push({
                        label: category,
                        data: data,
                        backgroundColor: colors[index],
                        borderColor: colors[index].replace('0.7', '1'),
                        borderWidth: 2
                    });
                });
                
                // Destroy previous chart instance if it exists
                if (monthlyTrendChart) {
                    monthlyTrendChart.destroy();
                }
                
                // Create new chart
                monthlyTrendChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
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
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            function updateTopCategories(topCategories) {
                // Reset if not enough categories
                if (topCategories.length < 3) {
                    for (let i = 1; i <= 3; i++) {
                        document.getElementById(`top-category-${i}-name`).textContent = '-';
                        document.getElementById(`top-category-${i}-value`).textContent = 'Rp 0';
                        document.getElementById(`top-category-${i}-percent`).textContent = '0%';
                        document.getElementById(`top-category-${i}-bar`).style.width = '0%';
                    }
                }
                
                // Update with available data
                topCategories.forEach((category, index) => {
                    const i = index + 1;
                    document.getElementById(`top-category-${i}-name`).textContent = category.nama_kategori;
                    document.getElementById(`top-category-${i}-value`).textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(category.total_biaya);
                    document.getElementById(`top-category-${i}-percent`).textContent = category.persentase + '%';
                    document.getElementById(`top-category-${i}-bar`).style.width = category.persentase + '%';
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
                
                window.location.href = `/api/v1/laporan/export/pengeluaran-per-kategori?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&format=xlsx`;
            });
            
            document.getElementById('btn-export-pdf').addEventListener('click', function() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                
                window.location.href = `/api/v1/laporan/export/pengeluaran-per-kategori?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&format=pdf`;
            });
            
            document.getElementById('btn-print').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
    @endpush
</x-app-layout>
