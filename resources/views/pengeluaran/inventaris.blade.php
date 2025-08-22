<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">Daftar Inventaris</h3>
                        <button id="btnTambah" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Tambah Inventaris
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-medium mb-2">Filter</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                                <input type="text" id="filterNama" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                                <select id="filterStok" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Semua</option>
                                    <option value="low">Stok Rendah</option>
                                    <option value="available">Tersedia</option>
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
                        <table id="tableInventaris" class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="py-3 px-4 text-left">No</th>
                                    <th class="py-3 px-4 text-left">Nama Item</th>
                                    <th class="py-3 px-4 text-left">Kategori</th>
                                    <th class="py-3 px-4 text-center">Satuan</th>
                                    <th class="py-3 px-4 text-center">Stok</th>
                                    <th class="py-3 px-4 text-center">Stok Minimum</th>
                                    <th class="py-3 px-4 text-center">Status</th>
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
    <div id="formModal" class="fixed inset-0 z-[100] overflow-y-auto hidden" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="formInventaris">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Inventaris</h3>

                                <input type="hidden" id="inventarisId">

                                <div class="mb-4">
                                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Item*</label>
                                    <input type="text" name="nama_barang" id="nama" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                </div>

                                <div class="mb-4">
                                    <label for="kategori_pengeluaran_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori*</label>
                                    <select name="kategori_id" id="kategori_pengeluaran_id" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan*</label>
                                    <input type="text" name="satuan" id="satuan" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Contoh: Kg, Liter, Box, Pcs" required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini*</label>
                                        <input type="number" name="jumlah_stok" id="stok" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0" required>
                                    </div>
                                    <div>
                                        <label for="stok_minimum" class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum*</label>
                                        <input type="number" name="minimal_stok" id="stok_minimum" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" rows="2" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
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

    <!-- Stok Adjustment Modal -->
    <div id="stokModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="formAdjustStok">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Adjustment Stok</h3>

                                <input type="hidden" id="adjustInventarisId">

                                <div class="mb-4">
                                    <div class="text-sm text-gray-700 mb-1">Nama Item:</div>
                                    <div class="font-medium" id="adjustNamaItem">-</div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-sm text-gray-700 mb-1">Stok Saat Ini:</div>
                                    <div class="font-medium" id="adjustStokSaatIni">-</div>
                                </div>

                                <div class="mb-4">
                                    <label for="adjustmentType" class="block text-sm font-medium text-gray-700 mb-1">Jenis Adjustment*</label>
                                    <select name="adjustmentType" id="adjustmentType" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="add">Tambah Stok</option>
                                        <option value="subtract">Kurangi Stok</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="adjustmentValue" class="block text-sm font-medium text-gray-700 mb-1">Jumlah*</label>
                                    <input type="number" name="adjustmentValue" id="adjustmentValue" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="1" required>
                                </div>

                                <div class="mb-4">
                                    <label for="adjustmentNotes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                    <textarea name="adjustmentNotes" id="adjustmentNotes" rows="2" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Alasan adjustment stok"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="btnSimpanAdjust" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" id="btnBatalAdjust" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiBaseUrl = '/api/v1';
            let currentPage = 1;
            let lastPage = 1;
            let filterData = {
                page: 1,
                nama: '',
                status: ''
            };

            // Load initial data
            loadKategoriOptions();
            loadInventarisData();

            // Event listeners for filter controls
            document.getElementById('btnFilter').addEventListener('click', function() {
                filterData.nama_barang = document.getElementById('filterNama').value;
                filterData.status = document.getElementById('filterStok').value;
                filterData.page = 1;
                loadInventarisData();
            });

            document.getElementById('btnReset').addEventListener('click', function() {
                document.getElementById('filterNama').value = '';
                document.getElementById('filterStok').value = '';
                filterData = {
                    page: 1,
                    nama: '',
                    status: ''
                };
                loadInventarisData();
            });

            // Event listeners for pagination
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    filterData.page = currentPage - 1;
                    loadInventarisData();
                }
            });

            document.getElementById('nextPage').addEventListener('click', function() {
                if (currentPage < lastPage) {
                    filterData.page = currentPage + 1;
                    loadInventarisData();
                }
            });

            // Form modal controls
            document.getElementById('btnTambah').addEventListener('click', function() {
                openFormModal('Tambah Inventaris');
            });

            document.getElementById('btnBatal').addEventListener('click', function() {
                closeFormModal();
            });

            document.getElementById('btnBatalAdjust').addEventListener('click', function() {
                closeStokModal();
            });

            // Form submission
            document.getElementById('formInventaris').addEventListener('submit', function(e) {
                e.preventDefault();
                submitInventaris();
            });

            document.getElementById('formAdjustStok').addEventListener('submit', function(e) {
                e.preventDefault();
                submitStokAdjustment();
            });

            // Function to load kategori options
            function loadKategoriOptions() {
                fetch(`${apiBaseUrl}/kategori-pengeluaran`)
                    .then(response => response.json())
                    .then(response => {
                        // Handle the response structure correctly
                        let kategoriData = response;
                        
                        // Check if the response has a 'data' property (API might return {status: 'success', data: [...]}
                        if (response.data) {
                            kategoriData = response.data;
                        }
                        
                        const select = document.getElementById('kategori_pengeluaran_id');
                        
                        // Clear existing options except the first one
                        while (select.options.length > 1) {
                            select.remove(1);
                        }
                        
                        // Add new options - handle both name fields (nama or nama_kategori)
                        kategoriData.forEach(kategori => {
                            const namaValue = kategori.nama_kategori || kategori.nama;
                            const option = new Option(namaValue, kategori.id);
                            select.add(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading kategori options:', error);
                        Swal.fire('Error', 'Gagal memuat data kategori', 'error');
                    });
            }

            // Function to load inventaris data
            function loadInventarisData() {
                const tbody = document.querySelector('#tableInventaris tbody');
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading data...</td></tr>';

                const params = new URLSearchParams();
                if (filterData.nama_barang) params.append('nama_barang', filterData.nama_barang);
                if (filterData.status) params.append('status', filterData.status);
                params.append('page', filterData.page);

                fetch(`${apiBaseUrl}/inventaris?${params.toString()}`)
                    .then(response => response.json())
                    .then(response => {
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
                            
                            renderInventarisTable(data.data, data.from);
                        } else {
                            // For non-paginated data
                            const items = Array.isArray(data) ? data : [data];
                            renderInventarisTable(items, 1);
                            
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
                        console.error('Error loading inventaris data:', error);
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                        Swal.fire('Error', 'Gagal memuat data inventaris', 'error');
                    });
            }

            function renderInventarisTable(items, startIndex) {
                const tbody = document.querySelector('#tableInventaris tbody');
                tbody.innerHTML = '';
                
                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>';
                    return;
                }
                
                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    
                    // Determine status and class
                    let statusText, statusClass;
                    if (item.jumlah_stok <= 0) {
                        statusText = 'Habis';
                        statusClass = 'bg-red-100 text-red-800';
                    } else if (item.jumlah_stok < item.minimal_stok) {
                        statusText = 'Rendah';
                        statusClass = 'bg-yellow-100 text-yellow-800';
                    } else {
                        statusText = 'Tersedia';
                        statusClass = 'bg-green-100 text-green-800';
                    }
                    
                    row.innerHTML = `
                        <td class="py-3 px-4">${(startIndex || 1) + index}</td>
                        <td class="py-3 px-4">${item.nama_barang}</td>
                        <td class="py-3 px-4">${item.kategori_pengeluaran?.nama_kategori || '-'}</td>
                        <td class="py-3 px-4 text-center">${item.satuan}</td>
                        <td class="py-3 px-4 text-center">${item.jumlah_stok}</td>
                        <td class="py-3 px-4 text-center">${item.minimal_stok}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button class="text-blue-500 hover:text-blue-700 mr-2 btnAdjust" data-id="${item.id}" title="Adjust Stok">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="text-yellow-500 hover:text-yellow-700 mr-2 btnEdit" data-id="${item.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-700 btnHapus" data-id="${item.id}" title="Hapus">
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
                // Adjust buttons
                document.querySelectorAll('.btnAdjust').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        openStokModal(id);
                    });
                });
                
                // Edit buttons
                document.querySelectorAll('.btnEdit').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        editInventaris(id);
                    });
                });
                
                // Delete buttons
                document.querySelectorAll('.btnHapus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        hapusInventaris(id);
                    });
                });
            }

            // Function to open stok adjustment modal
            function openStokModal(id) {
                fetch(`${apiBaseUrl}/inventaris/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('adjustInventarisId').value = data.id;
                        document.getElementById('adjustNamaItem').textContent = data.nama_barang;
                        document.getElementById('adjustStokSaatIni').textContent = `${data.jumlah_stok} ${data.satuan}`;
                        document.getElementById('adjustmentType').value = 'add';
                        document.getElementById('adjustmentValue').value = '';
                        document.getElementById('adjustmentNotes').value = '';
                        
                        document.getElementById('stokModal').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching inventaris data for adjustment:', error);
                        Swal.fire('Error', 'Gagal memuat data inventaris untuk adjustment', 'error');
                    });
            }

            // Function to close stok modal
            function closeStokModal() {
                document.getElementById('stokModal').classList.add('hidden');
            }

            // Function to edit inventaris
            function editInventaris(id) {
                fetch(`${apiBaseUrl}/inventaris/${id}`)
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            const data = response.data;
                            openFormModal('Edit Inventaris');
                            
                            document.getElementById('inventarisId').value = data.id;
                            document.getElementById('nama').value = data.nama_barang;
                            document.getElementById('kategori_pengeluaran_id').value = data.kategori_id;
                            document.getElementById('satuan').value = data.satuan;
                            document.getElementById('stok').value = data.jumlah_stok;
                            document.getElementById('stok_minimum').value = data.minimal_stok;
                            document.getElementById('deskripsi').value = data.deskripsi || '';
                        } else {
                            throw new Error('Invalid response format');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching inventaris data for edit:', error);
                        Swal.fire('Error', 'Gagal memuat data inventaris untuk diedit', 'error');
                    });
            }

            // Function to delete inventaris
            function hapusInventaris(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Anda yakin ingin menghapus item inventaris ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${apiBaseUrl}/inventaris/${id}`, {
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
                            Swal.fire('Berhasil!', 'Item inventaris berhasil dihapus', 'success');
                            loadInventarisData();
                        })
                        .catch(error => {
                            console.error('Error deleting inventaris:', error);
                            Swal.fire('Error', 'Gagal menghapus item inventaris', 'error');
                        });
                    }
                });
            }

            // Function to submit inventaris form
            function submitInventaris() {
                const inventarisId = document.getElementById('inventarisId').value;
                const isEdit = inventarisId !== '';
                
                // Prepare form data
                const formData = {
                    nama_barang: document.getElementById('nama').value,
                    kategori_id: document.getElementById('kategori_pengeluaran_id').value,
                    satuan: document.getElementById('satuan').value,
                    jumlah_stok: document.getElementById('stok').value,
                    minimal_stok: document.getElementById('stok_minimum').value,
                    deskripsi: document.getElementById('deskripsi').value
                };
                
                // Submit form
                const url = isEdit ? `${apiBaseUrl}/inventaris/${inventarisId}` : `${apiBaseUrl}/inventaris`;
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
                    Swal.fire('Berhasil!', `Item inventaris berhasil ${isEdit ? 'diperbarui' : 'disimpan'}`, 'success');
                    closeFormModal();
                    loadInventarisData();
                })
                .catch(error => {
                    console.error('Error saving inventaris:', error);
                    Swal.fire('Error', error.message || 'Gagal menyimpan item inventaris', 'error');
                });
            }

            // Function to submit stok adjustment
            function submitStokAdjustment() {
                const inventarisId = document.getElementById('adjustInventarisId').value;
                const adjustmentType = document.getElementById('adjustmentType').value;
                const adjustmentValue = parseInt(document.getElementById('adjustmentValue').value);
                const notes = document.getElementById('adjustmentNotes').value;
                
                if (!adjustmentValue || adjustmentValue <= 0) {
                    Swal.fire('Error', 'Jumlah adjustment harus lebih dari 0', 'error');
                    return;
                }
                
                // Prepare form data
                const formData = {
                    adjustment_type: adjustmentType,
                    adjustment_value: adjustmentValue,
                    notes: notes
                };
                
                // Submit form
                fetch(`${apiBaseUrl}/inventaris/${inventarisId}/adjust-stock`, {
                    method: 'POST',
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
                    Swal.fire('Berhasil!', 'Stok berhasil disesuaikan', 'success');
                    closeStokModal();
                    loadInventarisData();
                })
                .catch(error => {
                    console.error('Error adjusting stock:', error);
                    Swal.fire('Error', error.message || 'Gagal menyesuaikan stok', 'error');
                });
            }

            // Function to open form modal
            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('inventarisId').value = '';
                document.getElementById('nama').value = '';
                document.getElementById('kategori_pengeluaran_id').value = '';
                document.getElementById('satuan').value = '';
                document.getElementById('stok').value = '';
                document.getElementById('stok_minimum').value = '';
                document.getElementById('deskripsi').value = '';
                
                const modal = document.getElementById('formModal');
                modal.classList.remove('hidden');
                modal.style.display = 'block';
                console.log('Opening modal:', title);
            }

            // Function to close form modal
            function closeFormModal() {
                const modal = document.getElementById('formModal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
                console.log('Closing modal');
            }
        });
    </script>
    @endpush
</x-app-layout>
