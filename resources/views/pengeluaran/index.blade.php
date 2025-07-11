<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">Daftar Pengeluaran</h3>
                        <button id="btnTambah" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-medium mb-2">Filter</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" id="tanggalMulai" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" id="tanggalAkhir" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select id="kategori" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Semua Kategori</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button id="btnFilter" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-filter mr-2"></i> Filter
                                </button>
                                <button id="btnReset" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded ml-2">
                                    <i class="fas fa-redo mr-2"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="tablePengeluaran" class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="py-3 px-4 text-left">No</th>
                                    <th class="py-3 px-4 text-left">Tanggal</th>
                                    <th class="py-3 px-4 text-left">Kode</th>
                                    <th class="py-3 px-4 text-left">Kategori</th>
                                    <th class="py-3 px-4 text-left">Supplier</th>
                                    <th class="py-3 px-4 text-left">Keterangan</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                        <div id="paginationContainer" class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                Menampilkan <span id="fromData">0</span> - <span id="toData">0</span> dari <span id="totalData">0</span> data
                            </div>
                            <div class="flex">
                                <button id="prevPage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded mr-2">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="nextPage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    <div id="formModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="formPengeluaran">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Pengeluaran</h3>

                                <input type="hidden" id="pengeluaranId">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal*</label>
                                        <input type="date" name="tanggal" id="tanggal" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    </div>
                                    
                                    <div>
                                        <label for="kategori_pengeluaran_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori*</label>
                                        <select name="kategori_pengeluaran_id" id="kategori_pengeluaran_id" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                        <select name="supplier_id" id="supplier_id" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="">Pilih Supplier</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                        <input type="text" name="keterangan" id="keterangan" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Pengeluaran</label>
                                    <div class="border rounded-md p-4 bg-gray-50">
                                        <div id="detailContainer">
                                            <div class="detail-item grid grid-cols-12 gap-2 mb-2">
                                                <div class="col-span-5">
                                                    <input type="text" name="detail_nama[]" placeholder="Nama Item" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                </div>
                                                <div class="col-span-2">
                                                    <input type="number" name="detail_qty[]" placeholder="Qty" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-qty" min="1" required>
                                                </div>
                                                <div class="col-span-4">
                                                    <input type="number" name="detail_harga[]" placeholder="Harga" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-harga" min="0" required>
                                                </div>
                                                <div class="col-span-1 flex items-center justify-center">
                                                    <button type="button" class="text-red-500 hover:text-red-700 btnRemoveDetail">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" id="btnAddDetail" class="text-sm bg-green-500 hover:bg-green-600 text-white font-medium py-1 px-2 rounded">
                                                <i class="fas fa-plus mr-1"></i> Tambah Item
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <div class="text-right">
                                        <div class="text-sm text-gray-600 mb-1">Total Pengeluaran:</div>
                                        <div class="text-xl font-bold" id="totalPengeluaran">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="btnSimpan" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" id="btnBatal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pengeluaran</h3>
                    
                    <div class="border-b pb-3 mb-3">
                        <div class="grid grid-cols-3 mb-1">
                            <div class="text-sm text-gray-600">Kode</div>
                            <div class="col-span-2 text-sm font-medium" id="detailKode">-</div>
                        </div>
                        <div class="grid grid-cols-3 mb-1">
                            <div class="text-sm text-gray-600">Tanggal</div>
                            <div class="col-span-2 text-sm font-medium" id="detailTanggal">-</div>
                        </div>
                        <div class="grid grid-cols-3 mb-1">
                            <div class="text-sm text-gray-600">Kategori</div>
                            <div class="col-span-2 text-sm font-medium" id="detailKategori">-</div>
                        </div>
                        <div class="grid grid-cols-3 mb-1">
                            <div class="text-sm text-gray-600">Supplier</div>
                            <div class="col-span-2 text-sm font-medium" id="detailSupplier">-</div>
                        </div>
                        <div class="grid grid-cols-3 mb-1">
                            <div class="text-sm text-gray-600">Keterangan</div>
                            <div class="col-span-2 text-sm font-medium" id="detailKeterangan">-</div>
                        </div>
                    </div>
                    
                    <h4 class="text-md font-medium mb-2">Item Pengeluaran</h4>
                    <div class="overflow-x-auto max-h-60">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="detailItemContainer">
                                <!-- Detail items will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-end mt-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-600 mb-1">Total</div>
                            <div class="text-lg font-bold" id="detailTotal">Rp 0</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="btnCloseDetail" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiBaseUrl = '/api';
            let currentPage = 1;
            let lastPage = 1;
            let filterData = {
                page: 1,
                tanggalMulai: '',
                tanggalAkhir: '',
                kategoriId: ''
            };

            // Initialize date inputs with current month range
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            document.getElementById('tanggalMulai').valueAsDate = firstDay;
            document.getElementById('tanggalAkhir').valueAsDate = lastDay;
            document.getElementById('tanggal').valueAsDate = today;
            
            // Load initial data
            loadKategoriOptions();
            loadSupplierOptions();
            loadPengeluaranData();

            // Event listeners for filter controls
            document.getElementById('btnFilter').addEventListener('click', function() {
                filterData.tanggalMulai = document.getElementById('tanggalMulai').value;
                filterData.tanggalAkhir = document.getElementById('tanggalAkhir').value;
                filterData.kategoriId = document.getElementById('kategori').value;
                filterData.page = 1;
                loadPengeluaranData();
            });

            document.getElementById('btnReset').addEventListener('click', function() {
                document.getElementById('tanggalMulai').valueAsDate = firstDay;
                document.getElementById('tanggalAkhir').valueAsDate = lastDay;
                document.getElementById('kategori').value = '';
                filterData = {
                    page: 1,
                    tanggalMulai: document.getElementById('tanggalMulai').value,
                    tanggalAkhir: document.getElementById('tanggalAkhir').value,
                    kategoriId: ''
                };
                loadPengeluaranData();
            });

            // Pagination event listeners
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    filterData.page = currentPage - 1;
                    loadPengeluaranData();
                }
            });

            document.getElementById('nextPage').addEventListener('click', function() {
                if (currentPage < lastPage) {
                    filterData.page = currentPage + 1;
                    loadPengeluaranData();
                }
            });

            // Form modal controls
            document.getElementById('btnTambah').addEventListener('click', function() {
                openFormModal('Tambah Pengeluaran');
            });

            document.getElementById('btnBatal').addEventListener('click', function() {
                closeFormModal();
            });

            document.getElementById('btnCloseDetail').addEventListener('click', function() {
                closeDetailModal();
            });

            // Add detail item
            document.getElementById('btnAddDetail').addEventListener('click', function() {
                addDetailItem();
            });

            // Remove detail item event delegation
            document.getElementById('detailContainer').addEventListener('click', function(e) {
                if (e.target.classList.contains('btnRemoveDetail') || e.target.parentElement.classList.contains('btnRemoveDetail')) {
                    const button = e.target.closest('.btnRemoveDetail');
                    const detailItem = button.closest('.detail-item');
                    if (document.querySelectorAll('.detail-item').length > 1) {
                        detailItem.remove();
                    }
                    calculateTotal();
                }
            });

            // Calculate total on input change
            document.getElementById('detailContainer').addEventListener('input', function(e) {
                if (e.target.classList.contains('detail-qty') || e.target.classList.contains('detail-harga')) {
                    calculateTotal();
                }
            });

            // Form submission
            document.getElementById('formPengeluaran').addEventListener('submit', function(e) {
                e.preventDefault();
                submitPengeluaran();
            });

            // Function to load kategori options
            function loadKategoriOptions() {
                fetch(`${apiBaseUrl}/kategori-pengeluaran`)
                    .then(response => response.json())
                    .then(data => {
                        const filterSelect = document.getElementById('kategori');
                        const formSelect = document.getElementById('kategori_pengeluaran_id');
                        
                        // Clear existing options except the first one
                        while (filterSelect.options.length > 1) {
                            filterSelect.remove(1);
                        }
                        
                        while (formSelect.options.length > 1) {
                            formSelect.remove(1);
                        }
                        
                        // Add new options
                        data.forEach(kategori => {
                            const filterOption = new Option(kategori.nama, kategori.id);
                            filterSelect.add(filterOption);
                            
                            const formOption = new Option(kategori.nama, kategori.id);
                            formSelect.add(formOption);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading kategori options:', error);
                        Swal.fire('Error', 'Gagal memuat data kategori', 'error');
                    });
            }

            // Function to load supplier options
            function loadSupplierOptions() {
                fetch(`${apiBaseUrl}/supplier`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('supplier_id');
                        
                        // Clear existing options except the first one
                        while (select.options.length > 1) {
                            select.remove(1);
                        }
                        
                        // Add new options
                        data.forEach(supplier => {
                            const option = new Option(supplier.nama, supplier.id);
                            select.add(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading supplier options:', error);
                        Swal.fire('Error', 'Gagal memuat data supplier', 'error');
                    });
            }

            // Function to load pengeluaran data
            function loadPengeluaranData() {
                const tbody = document.querySelector('#tablePengeluaran tbody');
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading data...</td></tr>';

                const params = new URLSearchParams();
                if (filterData.tanggalMulai) params.append('tanggal_mulai', filterData.tanggalMulai);
                if (filterData.tanggalAkhir) params.append('tanggal_akhir', filterData.tanggalAkhir);
                if (filterData.kategoriId) params.append('kategori_id', filterData.kategoriId);
                params.append('page', filterData.page);

                fetch(`${apiBaseUrl}/pengeluaran?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        currentPage = data.current_page;
                        lastPage = data.last_page;
                        
                        // Update pagination info
                        document.getElementById('fromData').textContent = data.from || 0;
                        document.getElementById('toData').textContent = data.to || 0;
                        document.getElementById('totalData').textContent = data.total;
                        
                        // Enable/disable pagination buttons
                        document.getElementById('prevPage').disabled = currentPage === 1;
                        document.getElementById('prevPage').classList.toggle('opacity-50', currentPage === 1);
                        document.getElementById('nextPage').disabled = currentPage === lastPage;
                        document.getElementById('nextPage').classList.toggle('opacity-50', currentPage === lastPage);
                        
                        // Render table rows
                        tbody.innerHTML = '';
                        
                        if (data.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>';
                            return;
                        }
                        
                        data.data.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                            
                            row.innerHTML = `
                                <td class="py-3 px-4">${data.from + index}</td>
                                <td class="py-3 px-4">${formatDate(item.tanggal)}</td>
                                <td class="py-3 px-4">${item.kode}</td>
                                <td class="py-3 px-4">${item.kategori_pengeluaran?.nama || '-'}</td>
                                <td class="py-3 px-4">${item.supplier?.nama || '-'}</td>
                                <td class="py-3 px-4">${item.keterangan || '-'}</td>
                                <td class="py-3 px-4 text-right">${formatRupiah(item.total)}</td>
                                <td class="py-3 px-4 text-center">
                                    <button class="text-blue-500 hover:text-blue-700 mr-2 btnDetail" data-id="${item.id}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-yellow-500 hover:text-yellow-700 mr-2 btnEdit" data-id="${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700 btnHapus" data-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            `;
                            
                            tbody.appendChild(row);
                        });
                        
                        // Add event listeners to action buttons
                        addActionButtonListeners();
                    })
                    .catch(error => {
                        console.error('Error loading pengeluaran data:', error);
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                        Swal.fire('Error', 'Gagal memuat data pengeluaran', 'error');
                    });
            }

            // Add event listeners to action buttons
            function addActionButtonListeners() {
                // Detail buttons
                document.querySelectorAll('.btnDetail').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        showPengeluaranDetail(id);
                    });
                });
                
                // Edit buttons
                document.querySelectorAll('.btnEdit').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        editPengeluaran(id);
                    });
                });
                
                // Delete buttons
                document.querySelectorAll('.btnHapus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        hapusPengeluaran(id);
                    });
                });
            }

            // Function to show pengeluaran detail
            function showPengeluaranDetail(id) {
                fetch(`${apiBaseUrl}/pengeluaran/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('detailKode').textContent = data.kode;
                        document.getElementById('detailTanggal').textContent = formatDate(data.tanggal);
                        document.getElementById('detailKategori').textContent = data.kategori_pengeluaran?.nama || '-';
                        document.getElementById('detailSupplier').textContent = data.supplier?.nama || '-';
                        document.getElementById('detailKeterangan').textContent = data.keterangan || '-';
                        document.getElementById('detailTotal').textContent = formatRupiah(data.total);
                        
                        const itemContainer = document.getElementById('detailItemContainer');
                        itemContainer.innerHTML = '';
                        
                        if (data.detail_pengeluaran && data.detail_pengeluaran.length > 0) {
                            data.detail_pengeluaran.forEach(item => {
                                const row = document.createElement('tr');
                                const subtotal = item.qty * item.harga;
                                
                                row.innerHTML = `
                                    <td class="px-3 py-2 whitespace-nowrap">${item.nama}</td>
                                    <td class="px-3 py-2 text-center whitespace-nowrap">${item.qty}</td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">${formatRupiah(item.harga)}</td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">${formatRupiah(subtotal)}</td>
                                `;
                                
                                itemContainer.appendChild(row);
                            });
                        } else {
                            itemContainer.innerHTML = '<tr><td colspan="4" class="px-3 py-2 text-center">Tidak ada detail item</td></tr>';
                        }
                        
                        openDetailModal();
                    })
                    .catch(error => {
                        console.error('Error fetching pengeluaran detail:', error);
                        Swal.fire('Error', 'Gagal memuat detail pengeluaran', 'error');
                    });
            }

            // Function to edit pengeluaran
            function editPengeluaran(id) {
                fetch(`${apiBaseUrl}/pengeluaran/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        openFormModal('Edit Pengeluaran');
                        
                        document.getElementById('pengeluaranId').value = data.id;
                        document.getElementById('tanggal').value = data.tanggal;
                        document.getElementById('kategori_pengeluaran_id').value = data.kategori_pengeluaran_id;
                        document.getElementById('supplier_id').value = data.supplier_id || '';
                        document.getElementById('keterangan').value = data.keterangan || '';
                        
                        // Clear and add detail items
                        const detailContainer = document.getElementById('detailContainer');
                        detailContainer.innerHTML = '';
                        
                        if (data.detail_pengeluaran && data.detail_pengeluaran.length > 0) {
                            data.detail_pengeluaran.forEach(item => {
                                addDetailItem(item.nama, item.qty, item.harga);
                            });
                        } else {
                            addDetailItem();
                        }
                        
                        calculateTotal();
                    })
                    .catch(error => {
                        console.error('Error fetching pengeluaran data for edit:', error);
                        Swal.fire('Error', 'Gagal memuat data pengeluaran untuk diedit', 'error');
                    });
            }

            // Function to delete pengeluaran
            function hapusPengeluaran(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Anda yakin ingin menghapus data pengeluaran ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${apiBaseUrl}/pengeluaran/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire('Berhasil!', 'Data pengeluaran berhasil dihapus', 'success');
                            loadPengeluaranData();
                        })
                        .catch(error => {
                            console.error('Error deleting pengeluaran:', error);
                            Swal.fire('Error', 'Gagal menghapus data pengeluaran', 'error');
                        });
                    }
                });
            }

            // Function to submit pengeluaran form
            function submitPengeluaran() {
                const pengeluaranId = document.getElementById('pengeluaranId').value;
                const isEdit = pengeluaranId !== '';
                
                // Prepare form data
                const formData = {
                    tanggal: document.getElementById('tanggal').value,
                    kategori_pengeluaran_id: document.getElementById('kategori_pengeluaran_id').value,
                    supplier_id: document.getElementById('supplier_id').value || null,
                    keterangan: document.getElementById('keterangan').value,
                    detail_pengeluaran: []
                };
                
                // Get detail items
                const detailItems = document.querySelectorAll('.detail-item');
                detailItems.forEach(item => {
                    const nama = item.querySelector('input[name="detail_nama[]"]').value;
                    const qty = parseInt(item.querySelector('input[name="detail_qty[]"]').value, 10);
                    const harga = parseInt(item.querySelector('input[name="detail_harga[]"]').value, 10);
                    
                    formData.detail_pengeluaran.push({
                        nama,
                        qty,
                        harga
                    });
                });
                
                // Submit form
                const url = isEdit ? `${apiBaseUrl}/pengeluaran/${pengeluaranId}` : `${apiBaseUrl}/pengeluaran`;
                const method = isEdit ? 'PUT' : 'POST';
                
                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Terjadi kesalahan');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Berhasil!', `Data pengeluaran berhasil ${isEdit ? 'diperbarui' : 'disimpan'}`, 'success');
                    closeFormModal();
                    loadPengeluaranData();
                })
                .catch(error => {
                    console.error('Error saving pengeluaran:', error);
                    Swal.fire('Error', error.message || 'Gagal menyimpan data pengeluaran', 'error');
                });
            }

            // Function to add detail item
            function addDetailItem(nama = '', qty = 1, harga = 0) {
                const detailContainer = document.getElementById('detailContainer');
                const newItem = document.createElement('div');
                newItem.className = 'detail-item grid grid-cols-12 gap-2 mb-2';
                
                newItem.innerHTML = `
                    <div class="col-span-5">
                        <input type="text" name="detail_nama[]" placeholder="Nama Item" value="${nama}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="detail_qty[]" placeholder="Qty" value="${qty}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-qty" min="1" required>
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="detail_harga[]" placeholder="Harga" value="${harga}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-harga" min="0" required>
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" class="text-red-500 hover:text-red-700 btnRemoveDetail">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                
                detailContainer.appendChild(newItem);
                calculateTotal();
            }

            // Function to calculate total
            function calculateTotal() {
                let total = 0;
                const detailItems = document.querySelectorAll('.detail-item');
                
                detailItems.forEach(item => {
                    const qty = parseInt(item.querySelector('.detail-qty').value, 10) || 0;
                    const harga = parseInt(item.querySelector('.detail-harga').value, 10) || 0;
                    total += qty * harga;
                });
                
                document.getElementById('totalPengeluaran').textContent = formatRupiah(total);
            }

            // Function to open form modal
            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('pengeluaranId').value = '';
                document.getElementById('tanggal').valueAsDate = new Date();
                document.getElementById('kategori_pengeluaran_id').value = '';
                document.getElementById('supplier_id').value = '';
                document.getElementById('keterangan').value = '';
                
                // Clear detail items and add one empty row
                const detailContainer = document.getElementById('detailContainer');
                detailContainer.innerHTML = '';
                addDetailItem();
                
                calculateTotal();
                document.getElementById('formModal').classList.remove('hidden');
            }

            // Function to close form modal
            function closeFormModal() {
                document.getElementById('formModal').classList.add('hidden');
            }

            // Function to open detail modal
            function openDetailModal() {
                document.getElementById('detailModal').classList.remove('hidden');
            }

            // Function to close detail modal
            function closeDetailModal() {
                document.getElementById('detailModal').classList.add('hidden');
            }

            // Helper function to format date
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            // Helper function to format currency
            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        });
    </script>
    @endpush
</x-app-layout>
