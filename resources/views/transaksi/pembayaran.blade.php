<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="payment-form">
                        <div class="mb-6">
                            <label for="transaksi_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Transaksi</label>
                            <select id="transaksi_id" name="transaksi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Pilih transaksi untuk dibayar</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="total_harga" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total Harga</label>
                                <input type="text" id="total_harga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed" disabled readonly>
                            </div>
                            <div>
                                <label for="sisa_pembayaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sisa Pembayaran</label>
                                <input type="text" id="sisa_pembayaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed" disabled readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="jumlah_dibayar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Pembayaran</label>
                                <input type="number" id="jumlah_dibayar" name="jumlah_dibayar" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            </div>
                            <div>
                                <label for="tanggal_pembayaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pembayaran</label>
                                <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="catatan_pembayaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan Pembayaran</label>
                            <textarea id="catatan_pembayaran" name="catatan_pembayaran" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="is_lunas" type="checkbox" value="1" name="is_lunas" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_lunas" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tandai sebagai Lunas</label>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="window.history.back()" class="mr-2 px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Unpaid Transactions Table -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Daftar Transaksi Belum Lunas</h3>
                    
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Kode Transaksi</th>
                                    <th scope="col" class="px-6 py-3">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3">Tanggal</th>
                                    <th scope="col" class="px-6 py-3">Total</th>
                                    <th scope="col" class="px-6 py-3">Dibayar</th>
                                    <th scope="col" class="px-6 py-3">Sisa</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="unpaid-transactions">
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td colspan="8" class="px-6 py-4 text-center">Loading data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadUnpaidTransactions();
            
            // Form submission handler
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                e.preventDefault();
                savePayment();
            });
            
            // Transaction selection handler
            document.getElementById('transaksi_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('total_harga').value = selectedOption.dataset.total || '0';
                    document.getElementById('sisa_pembayaran').value = selectedOption.dataset.sisa || '0';
                    document.getElementById('jumlah_dibayar').value = selectedOption.dataset.sisa || '0';
                } else {
                    document.getElementById('total_harga').value = '';
                    document.getElementById('sisa_pembayaran').value = '';
                    document.getElementById('jumlah_dibayar').value = '';
                }
            });
        });
        
        function loadUnpaidTransactions() {
            fetch('/api/v1/transaksi?status_pembayaran=belum_lunas')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateTransactionTable(data.data.data);
                        populateTransactionDropdown(data.data.data);
                    }
                })
                .catch(error => {
                    console.error('Error loading unpaid transactions:', error);
                    alert('Gagal memuat data transaksi yang belum dibayar.');
                });
        }
        
        function updateTransactionTable(transactions) {
            const tableBody = document.getElementById('unpaid-transactions');
            tableBody.innerHTML = '';
            
            if (transactions.length === 0) {
                const row = document.createElement('tr');
                row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';
                row.innerHTML = `<td colspan="8" class="px-6 py-4 text-center">Tidak ada transaksi yang belum lunas</td>`;
                tableBody.appendChild(row);
                return;
            }
            
            transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';
                
                const statusClass = getStatusClass(transaction.status);
                const paymentStatusClass = transaction.status_pembayaran === 'lunas' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800';
                
                row.innerHTML = `
                    <td class="px-6 py-4">${transaction.kode_transaksi}</td>
                    <td class="px-6 py-4">${transaction.pelanggan?.nama || '-'}</td>
                    <td class="px-6 py-4">${new Date(transaction.tanggal_masuk).toLocaleDateString('id-ID')}</td>
                    <td class="px-6 py-4">Rp ${formatNumber(transaction.total_harga)}</td>
                    <td class="px-6 py-4">Rp ${formatNumber(transaction.jumlah_dibayar)}</td>
                    <td class="px-6 py-4">Rp ${formatNumber(transaction.sisa_pembayaran)}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${statusClass}">${transaction.status}</span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="selectTransaction(${transaction.id})" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Bayar</button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }
        
        function populateTransactionDropdown(transactions) {
            const dropdown = document.getElementById('transaksi_id');
            
            // Keep the first option and remove the rest
            while (dropdown.options.length > 1) {
                dropdown.remove(1);
            }
            
            transactions.forEach(transaction => {
                const option = document.createElement('option');
                option.value = transaction.id;
                option.textContent = `${transaction.kode_transaksi} - ${transaction.pelanggan?.nama || '-'} (Sisa: Rp ${formatNumber(transaction.sisa_pembayaran)})`;
                option.dataset.total = transaction.total_harga;
                option.dataset.sisa = transaction.sisa_pembayaran;
                dropdown.appendChild(option);
            });
        }
        
        function selectTransaction(id) {
            const dropdown = document.getElementById('transaksi_id');
            dropdown.value = id;
            dropdown.dispatchEvent(new Event('change'));
            
            // Scroll to form
            document.getElementById('payment-form').scrollIntoView({ behavior: 'smooth' });
        }
        
        function savePayment() {
            const transaksiId = document.getElementById('transaksi_id').value;
            const jumlahDibayar = parseFloat(document.getElementById('jumlah_dibayar').value);
            const tanggalPembayaran = document.getElementById('tanggal_pembayaran').value;
            const catatanPembayaran = document.getElementById('catatan_pembayaran').value;
            const isLunas = document.getElementById('is_lunas').checked;
            
            if (!transaksiId) {
                alert('Silakan pilih transaksi terlebih dahulu');
                return;
            }
            
            if (isNaN(jumlahDibayar) || jumlahDibayar <= 0) {
                alert('Jumlah pembayaran harus lebih dari 0');
                return;
            }
            
            const data = {
                transaksi_id: transaksiId,
                jumlah_dibayar: jumlahDibayar,
                tanggal_pembayaran: tanggalPembayaran,
                catatan: catatanPembayaran,
                status_pembayaran: isLunas ? 'lunas' : 'belum_lunas'
            };
            
            fetch('/api/v1/pembayaran', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Pembayaran berhasil disimpan');
                    // Reset form
                    document.getElementById('payment-form').reset();
                    // Reload transactions
                    loadUnpaidTransactions();
                } else {
                    alert('Gagal menyimpan pembayaran: ' + (result.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error saving payment:', error);
                alert('Gagal menyimpan pembayaran');
            });
        }
        
        function getStatusClass(status) {
            switch (status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'proses': return 'bg-blue-100 text-blue-800';
                case 'selesai': return 'bg-green-100 text-green-800';
                case 'diambil': return 'bg-purple-100 text-purple-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }
        
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
    @endpush
</x-app-layout>
