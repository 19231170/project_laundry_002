<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategori Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">Daftar Kategori Pengeluaran</h3>
                        <button id="btnTambah" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Tambah Kategori
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="tableKategori" class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="py-3 px-4 text-left">No</th>
                                    <th class="py-3 px-4 text-left">Nama Kategori</th>
                                    <th class="py-3 px-4 text-left">Deskripsi</th>
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
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="formKategori">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Kategori Pengeluaran</h3>

                                <input type="hidden" id="kategoriId">

                                <div class="mb-4">
                                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori*</label>
                                    <input type="text" name="nama" id="nama" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiBaseUrl = '/api';
            let currentPage = 1;
            let lastPage = 1;
            let filterData = {
                page: 1
            };

            // Load initial data
            loadKategoriData();

            // Event listeners for pagination
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    filterData.page = currentPage - 1;
                    loadKategoriData();
                }
            });

            document.getElementById('nextPage').addEventListener('click', function() {
                if (currentPage < lastPage) {
                    filterData.page = currentPage + 1;
                    loadKategoriData();
                }
            });

            // Form modal controls
            document.getElementById('btnTambah').addEventListener('click', function() {
                openFormModal('Tambah Kategori Pengeluaran');
            });

            document.getElementById('btnBatal').addEventListener('click', function() {
                closeFormModal();
            });

            // Form submission
            document.getElementById('formKategori').addEventListener('submit', function(e) {
                e.preventDefault();
                submitKategori();
            });

            // Function to load kategori data
            function loadKategoriData() {
                const tbody = document.querySelector('#tableKategori tbody');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Loading data...</td></tr>';

                const params = new URLSearchParams();
                params.append('page', filterData.page);

                fetch(`${apiBaseUrl}/kategori-pengeluaran?${params.toString()}`)
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
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Tidak ada data</td></tr>';
                            return;
                        }
                        
                        data.data.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                            
                            row.innerHTML = `
                                <td class="py-3 px-4">${data.from + index}</td>
                                <td class="py-3 px-4">${item.nama}</td>
                                <td class="py-3 px-4">${item.deskripsi || '-'}</td>
                                <td class="py-3 px-4 text-center">
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
                        console.error('Error loading kategori data:', error);
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                        Swal.fire('Error', 'Gagal memuat data kategori pengeluaran', 'error');
                    });
            }

            // Add event listeners to action buttons
            function addActionButtonListeners() {
                // Edit buttons
                document.querySelectorAll('.btnEdit').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        editKategori(id);
                    });
                });
                
                // Delete buttons
                document.querySelectorAll('.btnHapus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        hapusKategori(id);
                    });
                });
            }

            // Function to edit kategori
            function editKategori(id) {
                fetch(`${apiBaseUrl}/kategori-pengeluaran/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        openFormModal('Edit Kategori Pengeluaran');
                        
                        document.getElementById('kategoriId').value = data.id;
                        document.getElementById('nama').value = data.nama;
                        document.getElementById('deskripsi').value = data.deskripsi || '';
                    })
                    .catch(error => {
                        console.error('Error fetching kategori data for edit:', error);
                        Swal.fire('Error', 'Gagal memuat data kategori untuk diedit', 'error');
                    });
            }

            // Function to delete kategori
            function hapusKategori(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Anda yakin ingin menghapus kategori pengeluaran ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${apiBaseUrl}/kategori-pengeluaran/${id}`, {
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
                            Swal.fire('Berhasil!', 'Kategori pengeluaran berhasil dihapus', 'success');
                            loadKategoriData();
                        })
                        .catch(error => {
                            console.error('Error deleting kategori:', error);
                            Swal.fire('Error', 'Gagal menghapus kategori pengeluaran', 'error');
                        });
                    }
                });
            }

            // Function to submit kategori form
            function submitKategori() {
                const kategoriId = document.getElementById('kategoriId').value;
                const isEdit = kategoriId !== '';
                
                // Prepare form data
                const formData = {
                    nama: document.getElementById('nama').value,
                    deskripsi: document.getElementById('deskripsi').value
                };
                
                // Submit form
                const url = isEdit ? `${apiBaseUrl}/kategori-pengeluaran/${kategoriId}` : `${apiBaseUrl}/kategori-pengeluaran`;
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
                    Swal.fire('Berhasil!', `Kategori pengeluaran berhasil ${isEdit ? 'diperbarui' : 'disimpan'}`, 'success');
                    closeFormModal();
                    loadKategoriData();
                })
                .catch(error => {
                    console.error('Error saving kategori:', error);
                    Swal.fire('Error', error.message || 'Gagal menyimpan kategori pengeluaran', 'error');
                });
            }

            // Function to open form modal
            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('kategoriId').value = '';
                document.getElementById('nama').value = '';
                document.getElementById('deskripsi').value = '';
                
                document.getElementById('formModal').classList.remove('hidden');
            }

            // Function to close form modal
            function closeFormModal() {
                document.getElementById('formModal').classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
