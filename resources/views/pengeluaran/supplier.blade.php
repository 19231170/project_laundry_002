<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">Daftar Supplier</h3>
                        <button id="btnTambah" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Tambah Supplier
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="tableSupplier" class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="py-3 px-4 text-left">No</th>
                                    <th class="py-3 px-4 text-left">Nama Supplier</th>
                                    <th class="py-3 px-4 text-left">Alamat</th>
                                    <th class="py-3 px-4 text-left">No. Telepon</th>
                                    <th class="py-3 px-4 text-left">Email</th>
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
                <form id="formSupplier">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Supplier</h3>

                                <input type="hidden" id="supplierId">

                                <div class="mb-4">
                                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier*</label>
                                    <input type="text" name="nama" id="nama" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                    <textarea name="alamat" id="alamat" rows="2" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                    <input type="text" name="telepon" id="telepon" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="email" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
            loadSupplierData();

            // Event listeners for pagination
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    filterData.page = currentPage - 1;
                    loadSupplierData();
                }
            });

            document.getElementById('nextPage').addEventListener('click', function() {
                if (currentPage < lastPage) {
                    filterData.page = currentPage + 1;
                    loadSupplierData();
                }
            });

            // Form modal controls
            document.getElementById('btnTambah').addEventListener('click', function() {
                openFormModal('Tambah Supplier');
            });

            document.getElementById('btnBatal').addEventListener('click', function() {
                closeFormModal();
            });

            // Form submission
            document.getElementById('formSupplier').addEventListener('submit', function(e) {
                e.preventDefault();
                submitSupplier();
            });

            // Function to load supplier data
            function loadSupplierData() {
                const tbody = document.querySelector('#tableSupplier tbody');
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading data...</td></tr>';

                const params = new URLSearchParams();
                params.append('page', filterData.page);

                fetch(`${apiBaseUrl}/supplier?${params.toString()}`)
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
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Tidak ada data</td></tr>';
                            return;
                        }
                        
                        data.data.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                            
                            row.innerHTML = `
                                <td class="py-3 px-4">${data.from + index}</td>
                                <td class="py-3 px-4">${item.nama}</td>
                                <td class="py-3 px-4">${item.alamat || '-'}</td>
                                <td class="py-3 px-4">${item.telepon || '-'}</td>
                                <td class="py-3 px-4">${item.email || '-'}</td>
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
                        console.error('Error loading supplier data:', error);
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                        Swal.fire('Error', 'Gagal memuat data supplier', 'error');
                    });
            }

            // Add event listeners to action buttons
            function addActionButtonListeners() {
                // Edit buttons
                document.querySelectorAll('.btnEdit').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        editSupplier(id);
                    });
                });
                
                // Delete buttons
                document.querySelectorAll('.btnHapus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        hapusSupplier(id);
                    });
                });
            }

            // Function to edit supplier
            function editSupplier(id) {
                fetch(`${apiBaseUrl}/supplier/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        openFormModal('Edit Supplier');
                        
                        document.getElementById('supplierId').value = data.id;
                        document.getElementById('nama').value = data.nama;
                        document.getElementById('alamat').value = data.alamat || '';
                        document.getElementById('telepon').value = data.telepon || '';
                        document.getElementById('email').value = data.email || '';
                    })
                    .catch(error => {
                        console.error('Error fetching supplier data for edit:', error);
                        Swal.fire('Error', 'Gagal memuat data supplier untuk diedit', 'error');
                    });
            }

            // Function to delete supplier
            function hapusSupplier(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Anda yakin ingin menghapus supplier ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${apiBaseUrl}/supplier/${id}`, {
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
                            Swal.fire('Berhasil!', 'Supplier berhasil dihapus', 'success');
                            loadSupplierData();
                        })
                        .catch(error => {
                            console.error('Error deleting supplier:', error);
                            Swal.fire('Error', 'Gagal menghapus supplier', 'error');
                        });
                    }
                });
            }

            // Function to submit supplier form
            function submitSupplier() {
                const supplierId = document.getElementById('supplierId').value;
                const isEdit = supplierId !== '';
                
                // Prepare form data
                const formData = {
                    nama: document.getElementById('nama').value,
                    alamat: document.getElementById('alamat').value,
                    telepon: document.getElementById('telepon').value,
                    email: document.getElementById('email').value
                };
                
                // Submit form
                const url = isEdit ? `${apiBaseUrl}/supplier/${supplierId}` : `${apiBaseUrl}/supplier`;
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
                    Swal.fire('Berhasil!', `Supplier berhasil ${isEdit ? 'diperbarui' : 'disimpan'}`, 'success');
                    closeFormModal();
                    loadSupplierData();
                })
                .catch(error => {
                    console.error('Error saving supplier:', error);
                    Swal.fire('Error', error.message || 'Gagal menyimpan supplier', 'error');
                });
            }

            // Function to open form modal
            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('supplierId').value = '';
                document.getElementById('nama').value = '';
                document.getElementById('alamat').value = '';
                document.getElementById('telepon').value = '';
                document.getElementById('email').value = '';
                
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
