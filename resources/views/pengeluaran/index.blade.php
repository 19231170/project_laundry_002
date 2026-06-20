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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" id="tanggalMulai" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" id="tanggalAkhir" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-right">Jumlah</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600">
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
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="formPengeluaran">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Pengeluaran</h3>
                        <input type="hidden" id="pengeluaranId">

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengeluaran *</label>
                            <input type="text" name="name" id="name" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Contoh: Beli deterjen" required>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) *</label>
                            <input type="number" name="amount" id="amount" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0" min="0" required>
                        </div>

                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                            <input type="date" name="date" id="date" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
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
            const apiBaseUrl = '/web-api';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let currentPage = 1;
            let lastPage = 1;
            let filterData = { page: 1, tanggalMulai: '', tanggalAkhir: '' };

            const fetchConfig = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            };

            // Initialize dates
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            document.getElementById('tanggalMulai').valueAsDate = firstDay;
            document.getElementById('tanggalAkhir').valueAsDate = lastDay;
            document.getElementById('date').valueAsDate = today;

            loadPengeluaranData();

            document.getElementById('btnFilter').addEventListener('click', function() {
                filterData.tanggalMulai = document.getElementById('tanggalMulai').value;
                filterData.tanggalAkhir = document.getElementById('tanggalAkhir').value;
                filterData.page = 1;
                loadPengeluaranData();
            });

            document.getElementById('btnReset').addEventListener('click', function() {
                document.getElementById('tanggalMulai').valueAsDate = firstDay;
                document.getElementById('tanggalAkhir').valueAsDate = lastDay;
                filterData = { page: 1, tanggalMulai: '', tanggalAkhir: '' };
                loadPengeluaranData();
            });

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

            document.getElementById('btnTambah').addEventListener('click', function() {
                openFormModal('Tambah Pengeluaran');
            });

            document.getElementById('btnBatal').addEventListener('click', closeFormModal);

            document.getElementById('formPengeluaran').addEventListener('submit', function(e) {
                e.preventDefault();
                submitPengeluaran();
            });

            function loadPengeluaranData() {
                const tbody = document.querySelector('#tablePengeluaran tbody');
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>';

                const params = new URLSearchParams();
                if (filterData.tanggalMulai) params.append('dari_tanggal', filterData.tanggalMulai);
                if (filterData.tanggalAkhir) params.append('sampai_tanggal', filterData.tanggalAkhir);
                params.append('page', filterData.page);

                fetch(`${apiBaseUrl}/pengeluaran?${params.toString()}`, fetchConfig)
                    .then(r => r.json())
                    .then(response => {
                        if (response.status !== 'success') throw new Error('Gagal memuat data');

                        const data = response.data;
                        if (data.current_page) {
                            currentPage = data.current_page;
                            lastPage = data.last_page;
                            document.getElementById('fromData').textContent = data.from || 0;
                            document.getElementById('toData').textContent = data.to || 0;
                            document.getElementById('totalData').textContent = data.total;
                            document.getElementById('prevPage').disabled = currentPage === 1;
                            document.getElementById('nextPage').disabled = currentPage === lastPage;
                            renderTable(data.data, data.from);
                        } else {
                            const items = Array.isArray(data) ? data : [data];
                            document.getElementById('fromData').textContent = items.length > 0 ? 1 : 0;
                            document.getElementById('toData').textContent = items.length;
                            document.getElementById('totalData').textContent = items.length;
                            renderTable(items, 1);
                        }
                    })
                    .catch(() => {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data</td></tr>';
                    });
            }

            function renderTable(items, startIndex) {
                const tbody = document.querySelector('#tablePengeluaran tbody');
                tbody.innerHTML = '';

                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Tidak ada data</td></tr>';
                    return;
                }

                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    row.innerHTML = `
                        <td class="py-3 px-4">${(startIndex || 1) + index}</td>
                        <td class="py-3 px-4">${formatDate(item.date)}</td>
                        <td class="py-3 px-4">${item.name}</td>
                        <td class="py-3 px-4 text-right">${formatRupiah(item.amount)}</td>
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

                document.querySelectorAll('.btnEdit').forEach(btn => btn.addEventListener('click', () => editPengeluaran(btn.dataset.id)));
                document.querySelectorAll('.btnHapus').forEach(btn => btn.addEventListener('click', () => hapusPengeluaran(btn.dataset.id)));
            }

            function editPengeluaran(id) {
                fetch(`${apiBaseUrl}/pengeluaran/${id}`, fetchConfig)
                    .then(r => r.json())
                    .then(response => {
                        if (response.status !== 'success') return;
                        const d = response.data;
                        openFormModal('Edit Pengeluaran');
                        document.getElementById('pengeluaranId').value = d.id;
                        document.getElementById('name').value = d.name;
                        document.getElementById('amount').value = d.amount;
                        document.getElementById('date').value = d.date;
                    });
            }

            function hapusPengeluaran(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Hapus pengeluaran ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    fetch(`${apiBaseUrl}/pengeluaran/${id}`, {
                        method: 'DELETE',
                        headers: fetchConfig.headers,
                        credentials: fetchConfig.credentials
                    }).then(r => r.json()).then(() => {
                        Swal.fire('Berhasil', 'Pengeluaran dihapus', 'success');
                        loadPengeluaranData();
                    });
                });
            }

            function submitPengeluaran() {
                const id = document.getElementById('pengeluaranId').value;
                const isEdit = id !== '';
                const formData = {
                    name: document.getElementById('name').value,
                    amount: parseFloat(document.getElementById('amount').value),
                    date: document.getElementById('date').value,
                };

                const url = isEdit ? `${apiBaseUrl}/pengeluaran/${id}` : `${apiBaseUrl}/pengeluaran`;
                const method = isEdit ? 'PUT' : 'POST';

                fetch(url, { method, headers: fetchConfig.headers, credentials: fetchConfig.credentials, body: JSON.stringify(formData) })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Berhasil', `Pengeluaran berhasil ${isEdit ? 'diperbarui' : 'disimpan'}`, 'success');
                            closeFormModal();
                            loadPengeluaranData();
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menyimpan', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Gagal menyimpan data', 'error'));
            }

            function openFormModal(title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('pengeluaranId').value = '';
                document.getElementById('name').value = '';
                document.getElementById('amount').value = '';
                document.getElementById('date').valueAsDate = today;
                document.getElementById('formModal').classList.remove('hidden');
            }

            function closeFormModal() {
                document.getElementById('formModal').classList.add('hidden');
            }

            function formatDate(dateStr) {
                return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }

            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
            }
        });
    </script>
    @endpush
</x-app-layout>
