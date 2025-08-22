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
    <div id="formModal" class="fixed inset-0 z-[100] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                                        <select name="kategori_pengeluaran_id" id="kategori_pengeluaran_id" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="handleKategoriChange()" required>
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div id="supplierGroup">
                                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                        <select name="supplier_id" id="supplier_id" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="">Pilih Supplier</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">*Wajib untuk pengeluaran yang berkaitan dengan pembelian barang</p>
                                        <span class="text-red-500 text-xs"></span>
                                    </div>
                                    
                                    <div id="recipientGroup" class="hidden">
                                        <label for="penerima" class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                                        <input type="text" name="penerima" id="penerima" placeholder="Nama penerima" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <p class="text-xs text-gray-500 mt-1">*Wajib untuk gaji, uang makan, atau pengeluaran lain yang memiliki penerima</p>
                                        <span class="text-red-500 text-xs"></span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Pengeluaran</label>
                                    <div class="border rounded-md p-4 bg-gray-50">
                                        <div class="mb-3">
                                            <div id="jenisDetailContainer" class="flex items-center space-x-4 mb-2">
                                                <label class="flex items-center">
                                                    <input type="radio" name="detailMode" id="detailMode_item" value="item" class="mr-2" checked>
                                                    <span>Item/Barang</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="detailMode" id="detailMode_payment" value="payment" class="mr-2">
                                                    <span>Pembayaran Lainnya</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div id="itemDetailsSection">
                                            <div id="detailContainer">
                                                <div class="detail-item grid grid-cols-12 gap-2 mb-2">
                                                    <div class="col-span-5">
                                                        <input type="text" name="detail_pengeluaran[0][nama]" placeholder="Nama Item" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <input type="number" name="detail_pengeluaran[0][qty]" placeholder="Qty" value="1" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-qty" min="1" required>
                                                    </div>
                                                    <div class="col-span-4">
                                                        <input type="number" name="detail_pengeluaran[0][harga]" placeholder="Harga" value="0" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-harga" min="0" required>
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
                                        
                                        <div id="paymentDetailsSection" class="hidden">
                                            <div class="mb-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                                <input type="text" id="deskripsi_pembayaran" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Misalnya: Gaji Bulan Juli untuk 5 orang">
                                            </div>
                                            <div class="mb-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Total</label>
                                                <input type="number" id="jumlah_pembayaran" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0" value="0">
                                            </div>
                                            </div>
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
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" id="btnSimpan" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" id="btnBatal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </div>
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
            // Define handleKategoriChange function at the top level scope
            function handleKategoriChange() {
                console.log("handleKategoriChange called");
                const kategoriSelect = document.getElementById('kategori_pengeluaran_id');
                const supplierGroup = document.getElementById('supplierGroup');
                const recipientGroup = document.getElementById('recipientGroup');
                
                if (!kategoriSelect) {
                    console.log("kategoriSelect not found");
                    return;
                }
                
                if (!supplierGroup) {
                    console.log("supplierGroup not found");
                    return;
                }
                
                if (!recipientGroup) {
                    console.log("recipientGroup not found");
                    return;
                }
                
                const selectedValue = kategoriSelect.value;
                console.log("Selected kategori:", selectedValue);
                
                // Hide/show supplier field based on selection
                if (selectedValue === '1' || selectedValue === '2') { // Assuming 1 & 2 are inventory related
                    supplierGroup.style.display = 'block';
                    document.getElementById('supplier_id').setAttribute('required', 'required');
                } else {
                    supplierGroup.style.display = 'none';
                    document.getElementById('supplier_id').removeAttribute('required');
                    document.getElementById('supplier_id').value = '';
                }
                
                // Hide/show recipient field based on selection
                if (selectedValue === '4') { // Assuming 4 is salary/payment to staff
                    recipientGroup.style.display = 'block';
                    document.getElementById('penerima').setAttribute('required', 'required');
                } else {
                    recipientGroup.style.display = 'none';
                    document.getElementById('penerima').removeAttribute('required');
                    document.getElementById('penerima').value = '';
                }
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                const apiBaseUrl = '/api/v1';
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                let currentPage = 1;
                let lastPage = 1;
                let filterData = {
                    page: 1,
                    tanggalMulai: '',
                    tanggalAkhir: '',
                    kategoriId: ''
                };
                
                // Configure fetch defaults for all requests
                const fetchConfig = {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin' // Include cookies
                };

            // Initialize date inputs with current month range
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            document.getElementById('tanggalMulai').valueAsDate = firstDay;
            document.getElementById('tanggalAkhir').valueAsDate = lastDay;
            document.getElementById('tanggal').valueAsDate = today;
            
            // Initialize handlers for the new form features
            initializeNewFormHandlers();
            
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
                        // Renumber the remaining items
                        renumberDetailItems();
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
                console.log('Loading kategori options...');
                fetch(`${apiBaseUrl}/kategori-pengeluaran`, fetchConfig)
                    .then(response => {
                        console.log('Kategori response status:', response.status);
                        
                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text.substring(0, 150) + '...');
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(result => {
                        console.log('Kategori data received:', result);
                        
                        if (result.status !== 'success') {
                            throw new Error('Failed to load kategori data');
                        }
                        
                        const data = result.data;
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
                            // Check if we have nama_kategori or nama field
                            const namaKategori = kategori.nama_kategori || kategori.nama || 'Unnamed';
                            
                            const filterOption = new Option(namaKategori, kategori.id);
                            filterSelect.add(filterOption);
                            
                            const formOption = new Option(namaKategori, kategori.id);
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
                console.log('Loading supplier options...');
                fetch(`${apiBaseUrl}/supplier`, fetchConfig)
                    .then(response => {
                        console.log('Supplier response status:', response.status);
                        
                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text.substring(0, 150) + '...');
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(result => {
                        console.log('Supplier data received:', result);
                        
                        if (result.status !== 'success') {
                            throw new Error('Failed to load supplier data');
                        }
                        
                        const data = result.data;
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
                console.log('Loading pengeluaran data...');
                const tbody = document.querySelector('#tablePengeluaran tbody');
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading data...</td></tr>';

                const params = new URLSearchParams();
                if (filterData.tanggalMulai) params.append('tanggal_mulai', filterData.tanggalMulai);
                if (filterData.tanggalAkhir) params.append('tanggal_akhir', filterData.tanggalAkhir);
                if (filterData.kategoriId) params.append('kategori_id', filterData.kategoriId);
                params.append('page', filterData.page);

                console.log('Fetching:', `${apiBaseUrl}/pengeluaran?${params.toString()}`);
                
                fetch(`${apiBaseUrl}/pengeluaran?${params.toString()}`, fetchConfig)
                    .then(response => {
                        console.log('Pengeluaran response status:', response.status);
                        
                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text.substring(0, 150) + '...');
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(response => {
                        console.log('Pengeluaran data received:', response);
                        
                        if (response.status !== 'success') {
                            throw new Error('Failed to load data');
                        }
                        
                        const data = response.data;
                        // If pagination is implemented in the API
                        if (data.current_page) {
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
                            
                            renderPengeluaranTable(data.data, data.from);
                        } else {
                            // For non-paginated data
                            const items = Array.isArray(data) ? data : [data];
                            renderPengeluaranTable(items, 1);
                            
                            // Update pagination info for non-paginated data
                            document.getElementById('fromData').textContent = items.length > 0 ? 1 : 0;
                            document.getElementById('toData').textContent = items.length;
                            document.getElementById('totalData').textContent = items.length;
                            
                            // Disable pagination for non-paginated data
                            document.getElementById('prevPage').disabled = true;
                            document.getElementById('prevPage').classList.add('opacity-50');
                            document.getElementById('nextPage').disabled = true;
                            document.getElementById('nextPage').classList.add('opacity-50');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading pengeluaran data:', error);
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                        Swal.fire('Error', 'Gagal memuat data pengeluaran', 'error');
                    });
            }
            
            function renderPengeluaranTable(items, startIndex) {
                const tbody = document.querySelector('#tablePengeluaran tbody');
                tbody.innerHTML = '';
                
                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>';
                    return;
                }
                
                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    
                    row.innerHTML = `
                        <td class="py-3 px-4">${(startIndex || 1) + index}</td>
                        <td class="py-3 px-4">${formatDate(item.tanggal)}</td>
                        <td class="py-3 px-4">${item.kode}</td>
                        <td class="py-3 px-4">${item.kategori_pengeluaran?.nama_kategori || '-'}</td>
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
                fetch(`${apiBaseUrl}/pengeluaran/${id}`, fetchConfig)
                    .then(response => {
                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text.substring(0, 150) + '...');
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            const data = response.data;
                            document.getElementById('detailKode').textContent = data.kode;
                            document.getElementById('detailTanggal').textContent = formatDate(data.tanggal);
                            document.getElementById('detailKategori').textContent = data.kategori_pengeluaran?.nama_kategori || '-';
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
                        } else {
                            throw new Error('Invalid response format');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching pengeluaran detail:', error);
                        Swal.fire('Error', 'Gagal memuat detail pengeluaran', 'error');
                    });
            }

            // Function to edit pengeluaran
            function editPengeluaran(id) {
                fetch(`${apiBaseUrl}/pengeluaran/${id}`, fetchConfig)
                    .then(response => {
                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text.substring(0, 150) + '...');
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            const data = response.data;
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
                        } else {
                            throw new Error('Invalid response format');
                        }
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
                            headers: fetchConfig.headers,
                            credentials: fetchConfig.credentials
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
                const detailMode = document.getElementById('detailMode_item')?.checked ? 'item' : 'payment';
                
                // Prepare form data
                const formData = {
                    tanggal: document.getElementById('tanggal').value,
                    kategori_pengeluaran_id: document.getElementById('kategori_pengeluaran_id').value,
                    supplier_id: null,
                    penerima: null,
                    keterangan: document.getElementById('keterangan').value,
                    detail_pengeluaran: [],
                    jumlah_total: 0
                };
                
                // Add supplier_id or penerima based on form fields visibility
                const supplierGroup = document.getElementById('supplierGroup');
                const recipientGroup = document.getElementById('recipientGroup');
                
                if (!supplierGroup.classList.contains('hidden')) {
                    formData.supplier_id = document.getElementById('supplier_id').value || null;
                }
                
                if (!recipientGroup.classList.contains('hidden')) {
                    formData.penerima = document.getElementById('penerima').value || null;
                }
                
                if (detailMode === 'item') {
                    // Get detail items for item mode
                    const detailItems = document.querySelectorAll('.detail-item');
                    detailItems.forEach((item, index) => {
                        // Use the input names with proper array notation
                        const namaInput = item.querySelector(`input[name^="detail_pengeluaran["][name$="[nama]"]`);
                        const qtyInput = item.querySelector(`input[name^="detail_pengeluaran["][name$="[qty]"]`);
                        const hargaInput = item.querySelector(`input[name^="detail_pengeluaran["][name$="[harga]"]`);
                        
                        if (!namaInput || !qtyInput || !hargaInput) {
                            console.error('Missing input field in detail item', index);
                            return;
                        }
                        
                        const nama = namaInput.value;
                        const qty = parseInt(qtyInput.value, 10);
                        const harga = parseInt(hargaInput.value, 10);
                        const subtotal = qty * harga;
                        
                        formData.detail_pengeluaran.push({
                            nama,
                            qty,
                            harga,
                            subtotal
                        });
                        
                        formData.jumlah_total += subtotal;
                    });
                } else {
                    // For payment mode, use the total payment amount as a single item
                    const paymentAmount = parseInt(document.getElementById('jumlah_pembayaran').value, 10) || 0;
                    const paymentDesc = document.getElementById('deskripsi_pembayaran').value || 'Pembayaran';
                    
                    formData.detail_pengeluaran.push({
                        nama: paymentDesc,
                        qty: 1,
                        harga: paymentAmount,
                        subtotal: paymentAmount
                    });
                    
                    formData.jumlah_total = paymentAmount;
                }
                
                // Submit form
                const url = isEdit ? `${apiBaseUrl}/pengeluaran/${pengeluaranId}` : `${apiBaseUrl}/pengeluaran`;
                const method = isEdit ? 'PUT' : 'POST';
                
                fetch(url, {
                    method: method,
                    headers: fetchConfig.headers,
                    credentials: fetchConfig.credentials,
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
            // Function to renumber all detail items after a deletion
            function renumberDetailItems() {
                const detailItems = document.querySelectorAll('.detail-item');
                
                detailItems.forEach((item, index) => {
                    // Update the name attributes with the new index
                    const namaInput = item.querySelector('input[name^="detail_pengeluaran["][name$="[nama]"]');
                    const qtyInput = item.querySelector('input[name^="detail_pengeluaran["][name$="[qty]"]');
                    const hargaInput = item.querySelector('input[name^="detail_pengeluaran["][name$="[harga]"]');
                    
                    if (namaInput) namaInput.name = `detail_pengeluaran[${index}][nama]`;
                    if (qtyInput) qtyInput.name = `detail_pengeluaran[${index}][qty]`;
                    if (hargaInput) hargaInput.name = `detail_pengeluaran[${index}][harga]`;
                });
            }

            function addDetailItem(nama = '', qty = 1, harga = 0) {
                const detailContainer = document.getElementById('detailContainer');
                const newItem = document.createElement('div');
                newItem.className = 'detail-item grid grid-cols-12 gap-2 mb-2';
                
                // Get the current index for the new item
                const currentIndex = document.querySelectorAll('.detail-item').length;
                
                newItem.innerHTML = `
                    <div class="col-span-5">
                        <input type="text" name="detail_pengeluaran[${currentIndex}][nama]" placeholder="Nama Item" value="${nama}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="detail_pengeluaran[${currentIndex}][qty]" placeholder="Qty" value="${qty}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-qty" min="1" required>
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="detail_pengeluaran[${currentIndex}][harga]" placeholder="Harga" value="${harga}" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 detail-harga" min="0" required>
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
                const detailMode = document.getElementById('detailMode_item')?.checked ? 'item' : 'payment';
                
                if (detailMode === 'item') {
                    // Calculate from item details
                    const detailItems = document.querySelectorAll('.detail-item');
                    detailItems.forEach(item => {
                        const qty = parseInt(item.querySelector('.detail-qty').value, 10) || 0;
                        const harga = parseInt(item.querySelector('.detail-harga').value, 10) || 0;
                        total += qty * harga;
                    });
                } else {
                    // Get from payment amount field
                    total = parseInt(document.getElementById('jumlah_pembayaran').value, 10) || 0;
                }
                
                document.getElementById('totalPengeluaran').textContent = formatRupiah(total);
                return total;
            }

            // Function to open form modal
            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('pengeluaranId').value = '';
                document.getElementById('tanggal').valueAsDate = new Date();
                document.getElementById('kategori_pengeluaran_id').value = '';
                document.getElementById('supplier_id').value = '';
                document.getElementById('penerima').value = '';
                document.getElementById('keterangan').value = '';
                
                // Reset payment mode fields
                document.getElementById('jumlah_pembayaran').value = '0';
                document.getElementById('deskripsi_pembayaran').value = '';
                
                // Default to item mode
                const itemModeRadio = document.getElementById('detailMode_item');
                if (itemModeRadio) {
                    itemModeRadio.checked = true;
                    toggleDetailMode('item');
                }
                
                // Clear detail items and add one empty row
                const detailContainer = document.getElementById('detailContainer');
                detailContainer.innerHTML = '';
                addDetailItem();
                
                // Initialize form handlers and trigger kategori change
                handleKategoriChange();
                calculateTotal();
                
                const modal = document.getElementById('formModal');
                modal.classList.remove('hidden');
                modal.style.display = 'block';
            }

            // Function to close form modal
            function closeFormModal() {
                const modal = document.getElementById('formModal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }

            // Function to open detail modal
            function openDetailModal() {
                const modal = document.getElementById('detailModal');
                modal.classList.remove('hidden');
                modal.style.display = 'block';
            }

            // Function to close detail modal
            function closeDetailModal() {
                const modal = document.getElementById('detailModal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
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

            // Initialize the new form handlers
            function initializeNewFormHandlers() {
                // Get radio buttons and form sections
                const itemModeRadio = document.getElementById('detailMode_item');
                const paymentModeRadio = document.getElementById('detailMode_payment');
                const itemDetailsSection = document.getElementById('itemDetailsSection');
                const paymentDetailsSection = document.getElementById('paymentDetailsSection');
                const kategoriSelect = document.getElementById('kategori');
                
                // Add event listeners to radio buttons
                if (itemModeRadio && paymentModeRadio) {
                    itemModeRadio.addEventListener('change', () => toggleDetailMode('item'));
                    paymentModeRadio.addEventListener('change', () => toggleDetailMode('payment'));
                }

                // Add event listener to kategori select
                if (kategoriSelect) {
                    kategoriSelect.addEventListener('change', handleKategoriChange);
                    
                    // Initialize the form based on current kategori value
                    handleKategoriChange();
                }

                // Initialize with the default mode
                const currentMode = itemModeRadio?.checked ? 'item' : 'payment';
                toggleDetailMode(currentMode);
            }

            // Toggle between item and payment mode
            function toggleDetailMode(mode) {
                const itemDetailsSection = document.getElementById('itemDetailsSection');
                const paymentDetailsSection = document.getElementById('paymentDetailsSection');
                
                if (!itemDetailsSection || !paymentDetailsSection) return;

                if (mode === 'item') {
                    itemDetailsSection.classList.remove('hidden');
                    paymentDetailsSection.classList.add('hidden');
                } else {
                    itemDetailsSection.classList.add('hidden');
                    paymentDetailsSection.classList.remove('hidden');
                }
            }

            // Handle kategori change to show/hide supplier or recipient fields
            function handleKategoriChange() {
                console.log("handleKategoriChange called");
                const kategoriSelect = document.getElementById('kategori_pengeluaran_id');
                const supplierGroup = document.getElementById('supplierGroup');
                const recipientGroup = document.getElementById('recipientGroup');
                
                if (!kategoriSelect) {
                    console.log("kategoriSelect not found");
                    return;
                }
                
                if (!supplierGroup) {
                    console.log("supplierGroup not found");
                    return;
                }
                
                if (!recipientGroup) {
                    console.log("recipientGroup not found");
                    return;
                }
                
                const selectedValue = kategoriSelect.value;
                const selectedText = kategoriSelect.options[kategoriSelect.selectedIndex]?.text || '';
                
                console.log("Selected kategori:", selectedValue, selectedText);
                
                // Check if the selected category needs supplier or recipient
                // For example: inventory related categories need supplier
                // Payroll, meals, etc. need recipient
                const needsSupplier = !['Gaji', 'Makan', 'Transportasi', 'Utilitas', 'Lainnya'].some(
                    item => selectedText.includes(item)
                );
                
                const needsRecipient = ['Gaji', 'Makan', 'Transportasi'].some(
                    item => selectedText.includes(item)
                );
                
                console.log("needsSupplier:", needsSupplier);
                console.log("needsRecipient:", needsRecipient);
                
                // Show/hide supplier field
                supplierGroup.classList.toggle('hidden', !needsSupplier);
                
                // Show/hide recipient field
                recipientGroup.classList.toggle('hidden', !needsRecipient);
                
                // Reset validation classes if hidden
                if (!needsSupplier) {
                    const supplierSelect = document.getElementById('supplier');
                    if (supplierSelect) {
                        supplierSelect.classList.remove('border-red-500');
                        const errorEl = supplierGroup.querySelector('.text-red-500');
                        if (errorEl) errorEl.textContent = '';
                    }
                }
                
                if (!needsRecipient) {
                    const recipientInput = document.getElementById('penerima');
                    if (recipientInput) {
                        recipientInput.classList.remove('border-red-500');
                        const errorEl = recipientGroup.querySelector('.text-red-500');
                        if (errorEl) errorEl.textContent = '';
                    }
                }
            }
            
            // Add event listener for payment amount changes
            document.getElementById('jumlah_pembayaran')?.addEventListener('input', function() {
                calculateTotal();
            });
            
            // Call initialization function
            initializeNewFormHandlers();
        });
    </script>
    @endpush
</x-app-layout>