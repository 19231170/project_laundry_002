<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Laba Rugi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Laporan Laba Rugi</h2>
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
                                <label for="tipe" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode</label>
                                <select name="tipe" id="tipe" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="harian">Harian</option>
                                    <option value="bulanan" selected>Bulanan</option>
                                    <option value="tahunan">Tahunan</option>
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
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-pemasukan">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Pemasukan</div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="total-pengeluaran">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Pengeluaran</div>
                        </div>
                        <div id="profit-container" class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total-laba-rugi">Rp 0</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Laba/Rugi</div>
                        </div>
                    </div>

                    <!-- Profit Margin Indicator -->
                    <div class="mb-6">
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Profit Margin:</span>
                                <span class="text-sm font-medium" id="profit-margin">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                                <div id="profit-margin-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
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

                    <!-- Chart -->
                    <div class="mb-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-4">Grafik Laba Rugi</h3>
                                <div class="h-80">
                                    <canvas id="laba-rugi-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Periode
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pemasukan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pengeluaran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Laba/Rugi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Profit Margin
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Loading data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kategori Pengeluaran -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Pengeluaran per Kategori</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-4">
                                    <div class="h-80">
                                        <canvas id="kategori-chart"></canvas>
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
                                                        Kategori
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Total
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Persentase
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="kategori-table-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
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
            let labaRugiChart = null;
            let kategoriChart = null;
            
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
                const tipe = document.getElementById('tipe').value;
                
                fetch(`/api/v1/laporan/laba-rugi?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&tipe=${tipe}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateSummary(data.data.summary);
                            updateTable(data.data.detail);
                            updateChart(data.data.detail);
                            updateKategoriChart(data.data.pengeluaran_per_kategori);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    });
            }
            
            function updateSummary(summary) {
                document.getElementById('total-pemasukan').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.total_pemasukan);
                document.getElementById('total-pengeluaran').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.total_pengeluaran);
                
                const profitContainer = document.getElementById('profit-container');
                const labaRugi = document.getElementById('total-laba-rugi');
                
                if (summary.total_laba_rugi >= 0) {
                    labaRugi.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(summary.total_laba_rugi);
                    labaRugi.classList.remove('text-red-600', 'dark:text-red-400');
                    labaRugi.classList.add('text-green-600', 'dark:text-green-400');
                    profitContainer.classList.remove('bg-red-50', 'dark:bg-red-900/20');
                    profitContainer.classList.add('bg-green-50', 'dark:bg-green-900/20');
                } else {
                    labaRugi.textContent = 'Rp -' + new Intl.NumberFormat('id-ID').format(Math.abs(summary.total_laba_rugi));
                    labaRugi.classList.remove('text-green-600', 'dark:text-green-400');
                    labaRugi.classList.add('text-red-600', 'dark:text-red-400');
                    profitContainer.classList.remove('bg-green-50', 'dark:bg-green-900/20');
                    profitContainer.classList.add('bg-red-50', 'dark:bg-red-900/20');
                }
                
                // Update profit margin
                const marginEl = document.getElementById('profit-margin');
                const marginBar = document.getElementById('profit-margin-bar');
                const margin = summary.profit_margin;
                
                marginEl.textContent = margin + '%';
                
                if (margin >= 0) {
                    marginEl.classList.remove('text-red-600');
                    marginEl.classList.add('text-green-600');
                    marginBar.classList.remove('bg-red-600');
                    marginBar.classList.add('bg-green-600');
                    marginBar.style.width = Math.min(margin * 2, 100) + '%';  // Scale for better visualization (max 50% margin = 100% width)
                } else {
                    marginEl.classList.remove('text-green-600');
                    marginEl.classList.add('text-red-600');
                    marginBar.classList.remove('bg-green-600');
                    marginBar.classList.add('bg-red-600');
                    marginBar.style.width = Math.min(Math.abs(margin) * 2, 100) + '%';
                }
            }
            
            function updateTable(details) {
                const tableBody = document.getElementById('table-body');
                tableBody.innerHTML = '';
                
                if (details.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data untuk periode yang dipilih
                        </td>
                    `;
                    tableBody.appendChild(row);
                    return;
                }
                
                details.forEach(item => {
                    const row = document.createElement('tr');
                    
                    // Format the laba rugi display
                    const labaRugiClass = item.laba_rugi >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                    const labaRugiText = item.laba_rugi >= 0 
                        ? 'Rp ' + new Intl.NumberFormat('id-ID').format(item.laba_rugi)
                        : 'Rp -' + new Intl.NumberFormat('id-ID').format(Math.abs(item.laba_rugi));
                    
                    // Format the profit margin display
                    const profitMarginClass = item.profit_margin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                    
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${item.periode}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.pemasukan)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.pengeluaran)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium ${labaRugiClass}">
                            ${labaRugiText}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium ${profitMarginClass}">
                            ${item.profit_margin}%
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
            
            function updateChart(details) {
                const ctx = document.getElementById('laba-rugi-chart').getContext('2d');
                
                // Prepare chart data
                const labels = details.map(item => item.periode);
                const pemasukanData = details.map(item => item.pemasukan);
                const pengeluaranData = details.map(item => item.pengeluaran);
                const labaRugiData = details.map(item => item.laba_rugi);
                
                // Destroy previous chart instance if it exists
                if (labaRugiChart) {
                    labaRugiChart.destroy();
                }
                
                // Create new chart
                labaRugiChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: pemasukanData,
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Pengeluaran',
                                data: pengeluaranData,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Laba/Rugi',
                                data: labaRugiData,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                type: 'line',
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
                                title: {
                                    display: true,
                                    text: 'Nilai (Rp)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Laba/Rugi (Rp)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
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
            
            function updateKategoriChart(kategoriData) {
                const tableBody = document.getElementById('kategori-table-body');
                tableBody.innerHTML = '';
                
                if (!kategoriData || kategoriData.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data kategori pengeluaran
                        </td>
                    `;
                    tableBody.appendChild(row);
                    
                    // Clear chart
                    if (kategoriChart) {
                        kategoriChart.destroy();
                        kategoriChart = null;
                    }
                    return;
                }
                
                // Update table
                kategoriData.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${item.nama_kategori}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.total_biaya)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            ${item.persentase}%
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Update chart
                const ctx = document.getElementById('kategori-chart').getContext('2d');
                const labels = kategoriData.map(item => item.nama_kategori);
                const values = kategoriData.map(item => item.total_biaya);
                
                // Generate colors
                const colors = generateChartColors(kategoriData.length);
                
                // Destroy previous chart instance if it exists
                if (kategoriChart) {
                    kategoriChart.destroy();
                }
                
                // Create new chart
                kategoriChart = new Chart(ctx, {
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
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                        const percentage = (context.raw / values.reduce((a, b) => a + b, 0) * 100).toFixed(1);
                                        return label + ' (' + percentage + '%)';
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
                const tipe = document.getElementById('tipe').value;
                
                window.location.href = `/api/v1/laporan/export/laba-rugi?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&tipe=${tipe}&format=xlsx`;
            });
            
            document.getElementById('btn-export-pdf').addEventListener('click', function() {
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;
                const tipe = document.getElementById('tipe').value;
                
                window.location.href = `/api/v1/laporan/export/laba-rugi?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&tipe=${tipe}&format=pdf`;
            });
            
            document.getElementById('btn-print').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
    @endpush
</x-app-layout>
