<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Edit Transaksi</h2>
                    <a href="{{ route('transaksi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>

                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: "{{ session('success') }}",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "{{ session('error') }}",
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                @if ($errors->any())
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal!',
                            html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                            confirmButtonText: 'OK'
                        });
                    </script>
                @endif

                <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST" id="transaksi-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                        <div>
                            <label for="pelanggan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Pelanggan</label>
                            <select id="pelanggan_id" name="pelanggan_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" required>
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->id }}" {{ (old('pelanggan_id', $transaksi->pelanggan_id) == $p->id) ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->telepon }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status Pembayaran</label>
                            <select id="status_pembayaran" name="status_pembayaran" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" required>
                                <option value="belum_lunas" {{ $transaksi->status_pembayaran == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="lunas" {{ $transaksi->status_pembayaran == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-3">
                        <div>
                            <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Masuk</label>
                            <input type="date" id="tanggal_masuk" name="tanggal_masuk" 
                                value="{{ old('tanggal_masuk', $transaksi->tanggal_masuk?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" required>
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" 
                                value="{{ old('tanggal_selesai', $transaksi->tanggal_selesai?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                        </div>

                        <div>
                            <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Pembayaran</label>
                            <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" 
                                value="{{ old('tanggal_pembayaran', $transaksi->tanggal_pembayaran?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-3">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status Transaksi</label>
                            <select id="status" name="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" required>
                                <option value="pending" {{ old('status', $transaksi->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ old('status', $transaksi->status) == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ old('status', $transaksi->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="diambil" {{ old('status', $transaksi->status) == 'diambil' ? 'selected' : '' }}>Diambil</option>
                            </select>
                        </div>

                        <div>
                            <label for="total_harga" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Total Harga</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400">Rp</span>
                                </div>
                                <input type="text" id="total_harga" name="total_display" 
                                    value="{{ number_format($transaksi->total_harga, 0, ',', '.') }}"
                                    readonly class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 bg-gray-100">
                            </div>
                        </div>
                        
                        <div>
                            <label for="jumlah_dibayar" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Jumlah Dibayar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400">Rp</span>
                                </div>
                                <input type="number" step="0.01" id="jumlah_dibayar" name="jumlah_dibayar" 
                                    value="{{ $transaksi->jumlah_dibayar }}"
                                    class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="catatan" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="3" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">{{ old('catatan', $transaksi->catatan) }}</textarea>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Detail Layanan</h3>
                        </div>

                        <div id="layanan-container">
                            @foreach($transaksi->detailTransaksi as $index => $detail)
                                <div class="layanan-item border rounded-lg p-4 mb-4" data-index="{{ $index }}">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-md font-medium text-gray-700">Item Layanan #{{ $index + 1 }}</h4>
                                        <button type="button" onclick="removeLayanan(this)" 
                                            class="text-red-600 hover:text-red-900 remove-btn">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Layanan</label>
                                            <select name="layanan[{{ $index }}][layanan_id]" 
                                                class="layanan-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                required onchange="updateHarga(this)">
                                                <option value="">Pilih Layanan</option>
                                                @foreach($layanan as $l)
                                                    <option value="{{ $l->id }}" 
                                                        data-harga="{{ $l->harga }}" 
                                                        data-satuan="{{ $l->satuan }}"
                                                        {{ $detail->layanan_id == $l->id ? 'selected' : '' }}>
                                                        {{ $l->nama_layanan }} ({{ $l->harga ? 'Rp '.number_format($l->harga, 0, ',', '.') : '0' }} / {{ $l->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                            <input type="number" name="layanan[{{ $index }}][jumlah]" step="0.01" min="0.01" 
                                                value="{{ $detail->jumlah }}"
                                                data-harga="{{ $detail->layanan->harga }}"
                                                data-satuan="{{ $detail->layanan->satuan }}"
                                                class="jumlah-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                required oninput="calculateSubtotal(this)"
                                                placeholder="Jumlah ({{ $detail->layanan->satuan }})">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                                            <input type="text" readonly 
                                                class="harga-display mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" 
                                                value="Rp {{ number_format($detail->layanan->harga, 0, ',', '.') }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                            <input type="text" readonly 
                                                class="subtotal-display mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" 
                                                value="Rp {{ number_format($detail->subtotal, 0, ',', '.') }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex justify-start">
                            <button type="button" id="add-layanan" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center shadow-sm transition-transform transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Detail Layanan
                            </button>
                        </div>

                        <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-medium text-gray-900">Total Keseluruhan:</span>
                                <span id="total-keseluruhan" class="text-xl font-bold text-blue-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                                <input type="hidden" name="total_harga" id="total_harga" value="{{ $transaksi->total_harga }}">
                            </div>
                            
                            <div class="border-t pt-4">
                                <h3 class="text-md font-medium text-gray-900 mb-3">Pembulatan</h3>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <button type="button" id="btn-pembulatan-100" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded text-sm">
                                        Bulatkan ke 100
                                    </button>
                                    <button type="button" id="btn-pembulatan-500" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded text-sm">
                                        Bulatkan ke 500
                                    </button>
                                    <button type="button" id="btn-pembulatan-1000" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded text-sm">
                                        Bulatkan ke 1000
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nilai Pembulatan</label>
                                        <input type="number" name="pembulatan" id="pembulatan" value="{{ $transaksi->pembulatan ?? 0 }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            oninput="hitungTotalSetelahPembulatan()">
                                        <div class="mt-1 text-xs text-gray-500">Positif: pembulatan ke atas, Negatif: pembulatan ke bawah</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Setelah Pembulatan</label>
                                        <div class="mt-1 flex items-center">
                                            <span id="total-setelah-pembulatan" class="text-lg font-semibold text-green-600">
                                                Rp {{ number_format($transaksi->total_setelah_pembulatan ?? $transaksi->total_harga, 0, ',', '.') }}
                                            </span>
                                            <input type="hidden" name="total_setelah_pembulatan" id="total_setelah_pembulatan" 
                                                value="{{ $transaksi->total_setelah_pembulatan ?? $transaksi->total_harga }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let layananIndex = 100; // Start with high number to avoid conflicts

document.getElementById('add-layanan').addEventListener('click', function() {
    const container = document.getElementById('layanan-container');
    const newLayanan = document.createElement('div');
    newLayanan.className = 'layanan-item border rounded-lg p-4 mb-4';
    newLayanan.setAttribute('data-index', layananIndex);
    newLayanan.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-medium text-gray-700">Item Layanan #${layananIndex + 1}</h4>
            <button type="button" onclick="removeLayanan(this)" 
                class="text-red-600 hover:text-red-900 remove-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Layanan</label>
                <select name="layanan[${layananIndex}][layanan_id]" 
                    class="layanan-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    required onchange="updateHarga(this)">
                    <option value="">Pilih Layanan</option>
                    @foreach($layanan as $l)
                        <option value="{{ $l->id }}" data-harga="{{ $l->harga }}" data-satuan="{{ $l->satuan }}">
                            {{ $l->nama_layanan }} ({{ $l->harga ? 'Rp '.number_format($l->harga, 0, ',', '.') : '0' }} / {{ $l->satuan }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="layanan[${layananIndex}][jumlah]" step="0.01" min="0.01" 
                    class="jumlah-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    required oninput="calculateSubtotal(this)" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                <input type="text" readonly 
                    class="harga-display mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" 
                    value="Rp 0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                <input type="text" readonly 
                    class="subtotal-display mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" 
                    value="Rp 0">
            </div>
        </div>
    `;
    container.appendChild(newLayanan);
    layananIndex++;
    updateRemoveButtons();
    
    Swal.fire({
        icon: 'success',
        title: 'Item Ditambahkan!',
        text: 'Item layanan baru berhasil ditambahkan',
        timer: 1500,
        showConfirmButton: false
    });
});

function removeLayanan(button) {
    const layananItems = document.querySelectorAll('.layanan-item');
    if (layananItems.length > 1) {
        Swal.fire({
            title: 'Hapus Item?',
            text: "Item layanan ini akan dihapus dari transaksi",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('.layanan-item').remove();
                updateLayananNumbers();
                updateRemoveButtons();
                calculateTotal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Dihapus!',
                    text: 'Item layanan berhasil dihapus',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Bisa Dihapus!',
            text: 'Minimal harus ada satu item layanan',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

function updateRemoveButtons() {
    const layananItems = document.querySelectorAll('.layanan-item');
    const removeButtons = document.querySelectorAll('.remove-btn');
    
    if (layananItems.length <= 1) {
        removeButtons.forEach(btn => btn.classList.add('hidden'));
    } else {
        removeButtons.forEach(btn => btn.classList.remove('hidden'));
    }
}

function updateLayananNumbers() {
    const layananItems = document.querySelectorAll('.layanan-item');
    layananItems.forEach((item, index) => {
        const title = item.querySelector('h4');
        if (title) {
            title.textContent = `Item Layanan #${index + 1}`;
        }
    });
}

function updateHarga(select) {
    const option = select.selectedOptions[0];
    const harga = parseFloat(option.getAttribute('data-harga') || 0);
    const satuan = option.getAttribute('data-satuan') || '';
    
    const layananItem = select.closest('.layanan-item');
    const jumlahInput = layananItem.querySelector('.jumlah-input');
    const hargaDisplay = layananItem.querySelector('.harga-display');
    
    jumlahInput.setAttribute('data-harga', harga);
    jumlahInput.setAttribute('data-satuan', satuan);
    hargaDisplay.value = 'Rp ' + harga.toLocaleString('id-ID');
    
    // Update placeholder jumlah dengan satuan
    jumlahInput.placeholder = `Jumlah (${satuan})`;
    
    calculateSubtotal(jumlahInput);
}

function calculateSubtotal(input) {
    const harga = parseFloat(input.getAttribute('data-harga') || 0);
    const jumlah = parseFloat(input.value || 0);
    const subtotal = harga * jumlah;
    
    const subtotalDisplay = input.closest('.layanan-item').querySelector('.subtotal-display');
    subtotalDisplay.value = 'Rp ' + subtotal.toLocaleString('id-ID');
    
    calculateTotal();
}

function calculateTotal() {
    const layananItems = document.querySelectorAll('.layanan-item');
    let total = 0;
    
    layananItems.forEach(item => {
        const jumlahInput = item.querySelector('.jumlah-input');
        const harga = parseFloat(jumlahInput.getAttribute('data-harga') || 0);
        const jumlah = parseFloat(jumlahInput.value || 0);
        total += harga * jumlah;
    });
    
    document.getElementById('total-keseluruhan').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_harga').value = total;
    
    // Update juga total setelah pembulatan
    hitungTotalSetelahPembulatan();
}

// Form submission with validation
document.getElementById('transaksi-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    const pelangganId = document.getElementById('pelanggan_id').value;
    const tanggalMasuk = document.getElementById('tanggal_masuk').value;
    const layananItems = document.querySelectorAll('.layanan-item');
    
    let hasError = false;
    let errorMessage = '';
    
    if (!pelangganId) {
        hasError = true;
        errorMessage += '• Pilih pelanggan\n';
    }
    
    if (!tanggalMasuk) {
        hasError = true;
        errorMessage += '• Isi tanggal masuk\n';
    }
    
    // Validate layanan items
    layananItems.forEach((item, index) => {
        const select = item.querySelector('.layanan-select');
        const jumlah = item.querySelector('.jumlah-input');
        
        if (!select.value) {
            hasError = true;
            errorMessage += `• Pilih layanan untuk item #${index + 1}\n`;
        }
        
        if (!jumlah.value || parseFloat(jumlah.value) <= 0) {
            hasError = true;
            errorMessage += `• Isi jumlah untuk item #${index + 1}\n`;
        }
    });
    
    if (hasError) {
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Valid!',
            text: errorMessage,
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan Transaksi...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form
    this.submit();
});

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
    updateLayananNumbers();
    calculateTotal();
    
    // Event listener untuk tombol pembulatan
    document.getElementById('btn-pembulatan-100').addEventListener('click', () => hitungPembulatan(100));
    document.getElementById('btn-pembulatan-500').addEventListener('click', () => hitungPembulatan(500));
    document.getElementById('btn-pembulatan-1000').addEventListener('click', () => hitungPembulatan(1000));
    
    // Initial calculation
    hitungTotalSetelahPembulatan();
});

function hitungPembulatan(nominal) {
    const totalHarga = parseFloat(document.getElementById('total_harga').value) || 0;
    const modulo = totalHarga % nominal;
    
    let pembulatan = 0;
    if(modulo > 0) {
        // Pembulatan ke atas
        pembulatan = nominal - modulo;
    }
    
    document.getElementById('pembulatan').value = pembulatan;
    hitungTotalSetelahPembulatan();
}

function hitungTotalSetelahPembulatan() {
    const totalHarga = parseFloat(document.getElementById('total_harga').value) || 0;
    const pembulatan = parseFloat(document.getElementById('pembulatan').value) || 0;
    const totalSetelahPembulatan = totalHarga + pembulatan;
    
    document.getElementById('total-setelah-pembulatan').textContent = 'Rp ' + totalSetelahPembulatan.toLocaleString('id-ID');
    document.getElementById('total_setelah_pembulatan').value = totalSetelahPembulatan;
}

// Mengatur jumlah pembayaran dan status pembayaran
document.addEventListener('DOMContentLoaded', function() {
    const statusPembayaran = document.getElementById('status_pembayaran');
    const jumlahDibayar = document.getElementById('jumlah_dibayar');
    const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
    const totalHarga = parseFloat('{{ $transaksi->total_harga }}');
    
    statusPembayaran.addEventListener('change', function() {
        if (this.value === 'lunas') {
            // Jika status diubah ke lunas, tandai tanggal pembayaran hari ini jika belum diisi
            if (!tanggalPembayaran.value) {
                tanggalPembayaran.value = new Date().toISOString().substr(0, 10);
            }
            
            // Isi jumlah pembayaran dengan total harga
            jumlahDibayar.value = totalHarga;
        }
    });
    
    // Cek status pembayaran saat halaman dimuat
    if (statusPembayaran.value === 'lunas' && !tanggalPembayaran.value) {
        tanggalPembayaran.value = new Date().toISOString().substr(0, 10);
    }
});
</script>
</x-app-layout>
