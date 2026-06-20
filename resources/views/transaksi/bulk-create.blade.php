<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bulk Create Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Bulk Create Transaksi</h2>
                    <a href="{{ route('transaksi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>

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

                <form action="{{ route('transaksi.bulk-store') }}" method="POST" id="bulk-form">
                    @csrf

                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Jumlah Transaksi:</span>
                            <span id="transaksi-count" class="ml-2 text-lg font-bold text-blue-600">1</span>
                        </div>
                        <button type="button" id="add-transaksi"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center shadow-sm transition-transform transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Transaksi
                        </button>
                    </div>

                    <div id="transaksi-container">
                        {{-- Transaction block #0 will be rendered by JS on load --}}
                    </div>

                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Grand Total:</span>
                            <span id="grand-total" class="text-xl font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan Semua Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let transaksiIndex = 0;
let layananIndices = {}; // keyed by transaksiIndex

// ==================== LAYANAN TEMPLATE ====================
let newLayananOptions = [];

function getLayananOptionsHtml() {
    let html = '<option value="">Pilih Layanan</option>';
    @foreach($layanan as $l)
    html += `<option value="{{ $l->id }}" data-harga="{{ $l->harga }}" data-satuan="{{ $l->satuan }}">{{ $l->nama_layanan }} ({{ $l->harga ? 'Rp '.number_format($l->harga, 0, ',', '.') : '0' }} / {{ $l->satuan }})</option>`;
    @endforeach
    newLayananOptions.forEach(l => {
        const hargaFormatted = l.harga ? 'Rp ' + parseInt(l.harga).toLocaleString('id-ID') : 'Rp 0';
        html += `<option value="${l.id}" data-harga="${l.harga}" data-satuan="${l.satuan}">${l.nama_layanan} (${hargaFormatted} / ${l.satuan})</option>`;
    });
    return html;
}

// ==================== PELANGGAN OPTIONS ====================
function getPelangganOptionsHtml() {
    let html = '<option value="">Pilih Pelanggan</option>';
    @foreach($pelanggan as $p)
    html += `<option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->telepon }}</option>`;
    @endforeach
    return html;
}

// ==================== LAYANAN ITEM HTML ====================
function buildLayananItem(tIdx, lIdx) {
    const div = document.createElement('div');
    div.className = 'layanan-item border rounded-lg p-4 mb-3';
    div.setAttribute('data-t-index', tIdx);
    div.setAttribute('data-l-index', lIdx);
    div.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h5 class="text-sm font-medium text-gray-600 layanan-title">Layanan #${lIdx + 1}</h5>
            <button type="button" onclick="removeLayanan(this)" class="text-red-600 hover:text-red-900 remove-btn hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Layanan</label>
                <select name="transaksis[${tIdx}][layanan][${lIdx}][layanan_id]"
                    class="layanan-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required onchange="updateHarga(this)">
                    ${getLayananOptionsHtml()}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="transaksis[${tIdx}][layanan][${lIdx}][jumlah]" step="0.01" min="0.01"
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
    return div;
}

// ==================== TRANSACTION BLOCK HTML ====================
function buildTransaksiBlock(index) {
    const section = document.createElement('div');
    section.className = 'transaksi-block border-2 border-indigo-200 rounded-lg p-5 mb-6 bg-white';
    section.setAttribute('data-index', index);
    section.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-indigo-700">Transaksi #${index + 1}</h3>
            <button type="button" onclick="removeTransaksiBlock(this)" class="remove-transaksi-btn text-red-600 hover:text-red-900 hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Pelanggan</label>
                <select name="transaksis[${index}][pelanggan_id]" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    ${getPelangganOptionsHtml()}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                <input type="date" name="transaksis[${index}][tanggal_masuk]"
                    value="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="transaksis[${index}][tanggal_selesai]"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                <input type="text" name="transaksis[${index}][catatan]"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Catatan transaksi (opsional)">
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-medium text-gray-800">Detail Layanan</h4>
                <button type="button" onclick="addLayanan(this)"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded-md transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Layanan
                </button>
            </div>

            <div class="layanan-container" id="layanan-container-${index}">
                ${buildLayananItem(index, 0).outerHTML}
            </div>

            <div class="mt-3 flex justify-end">
                <span class="text-sm text-gray-500">Subtotal:</span>
                <span class="ml-2 text-sm font-semibold text-gray-800 transaksi-subtotal" id="subtotal-${index}">Rp 0</span>
            </div>
        </div>
    `;
    return section;
}

// ==================== ADD TRANSACTION BLOCK ====================
document.getElementById('add-transaksi').addEventListener('click', function() {
    transaksiIndex++;
    layananIndices[transaksiIndex] = 1;

    const container = document.getElementById('transaksi-container');
    const block = buildTransaksiBlock(transaksiIndex);
    container.appendChild(block);

    updateTransaksiNumbers();
    updateRemoveTransaksiButtons();
    updateRemoveLayananButtons();

    document.getElementById('transaksi-count').textContent = document.querySelectorAll('.transaksi-block').length;
});

// ==================== REMOVE TRANSACTION BLOCK ====================
function removeTransaksiBlock(btn) {
    const blocks = document.querySelectorAll('.transaksi-block');
    if (blocks.length <= 1) {
        Swal.fire({ icon: 'error', title: 'Tidak Bisa Dihapus!', text: 'Minimal harus ada satu transaksi', timer: 2000, showConfirmButton: false });
        return;
    }
    Swal.fire({
        title: 'Hapus Transaksi?',
        text: 'Seluruh transaksi ini akan dihapus',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            btn.closest('.transaksi-block').remove();
            updateTransaksiNumbers();
            updateRemoveTransaksiButtons();
            document.getElementById('transaksi-count').textContent = document.querySelectorAll('.transaksi-block').length;
            calculateGrandTotal();
        }
    });
}

function updateRemoveTransaksiButtons() {
    const blocks = document.querySelectorAll('.transaksi-block');
    document.querySelectorAll('.remove-transaksi-btn').forEach(btn => {
        btn.classList.toggle('hidden', blocks.length <= 1);
    });
}

// ==================== ADD LAYANAN TO A BLOCK ====================
function addLayanan(btn) {
    const container = btn.closest('.transaksi-block').querySelector('.layanan-container');
    const tIdx = parseInt(container.id.replace('layanan-container-', ''));
    const lIdx = layananIndices[tIdx] ?? 0;
    layananIndices[tIdx] = lIdx + 1;

    const wrapper = document.createElement('div');
    wrapper.appendChild(buildLayananItem(tIdx, lIdx));
    container.appendChild(wrapper.lastElementChild);

    updateLayananNumbers(container);
    updateRemoveLayananButtonsIn(container);
}

function removeLayanan(btn) {
    const container = btn.closest('.layanan-container');
    const items = container.querySelectorAll('.layanan-item');
    if (items.length <= 1) {
        Swal.fire({ icon: 'error', title: 'Tidak Bisa Dihapus!', text: 'Minimal harus ada satu layanan', timer: 2000, showConfirmButton: false });
        return;
    }
    btn.closest('.layanan-item').remove();
    updateLayananNumbers(container);
    updateRemoveLayananButtonsIn(container);
    calculateSubtotalForTransaksi(container);
    calculateGrandTotal();
}

function updateRemoveLayananButtonsIn(container) {
    const items = container.querySelectorAll('.layanan-item');
    container.querySelectorAll('.remove-btn').forEach(btn => {
        btn.classList.toggle('hidden', items.length <= 1);
    });
}

function updateRemoveLayananButtons() {
    document.querySelectorAll('.layanan-container').forEach(c => updateRemoveLayananButtonsIn(c));
}

function updateLayananNumbers(container) {
    container.querySelectorAll('.layanan-item').forEach((item, idx) => {
        const title = item.querySelector('.layanan-title');
        if (title) title.textContent = `Layanan #${idx + 1}`;
    });
}

function updateTransaksiNumbers() {
    document.querySelectorAll('.transaksi-block').forEach((block, idx) => {
        block.querySelector('h3').textContent = `Transaksi #${idx + 1}`;
    });
}

// ==================== PRICING ====================
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
    jumlahInput.placeholder = `Jumlah (${satuan})`;
    calculateSubtotal(jumlahInput);
}

function calculateSubtotal(input) {
    const harga = parseFloat(input.getAttribute('data-harga') || 0);
    const jumlah = parseFloat(input.value || 0);
    const subtotal = harga * jumlah;
    input.closest('.layanan-item').querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');
    calculateSubtotalForTransaksi(input.closest('.layanan-container'));
    calculateGrandTotal();
}

function calculateSubtotalForTransaksi(container) {
    const tIdx = parseInt(container.id.replace('layanan-container-', ''));
    let total = 0;
    container.querySelectorAll('.layanan-item').forEach(item => {
        const jumlahInput = item.querySelector('.jumlah-input');
        const harga = parseFloat(jumlahInput.getAttribute('data-harga') || 0);
        const jumlah = parseFloat(jumlahInput.value || 0);
        total += harga * jumlah;
    });
    const el = document.getElementById('subtotal-' + tIdx);
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function calculateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.layanan-item').forEach(item => {
        const jumlahInput = item.querySelector('.jumlah-input');
        const harga = parseFloat(jumlahInput.getAttribute('data-harga') || 0);
        const jumlah = parseFloat(jumlahInput.value || 0);
        grand += harga * jumlah;
    });
    document.getElementById('grand-total').textContent = 'Rp ' + grand.toLocaleString('id-ID');
}

// ==================== FORM VALIDATION ====================
document.getElementById('bulk-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let hasError = false;
    let errorMessage = '';

    document.querySelectorAll('.transaksi-block').forEach((block, tIdx) => {
        const pelanggan = block.querySelector('select[name^="transaksis"][name$="[pelanggan_id]"]');
        const tanggalMasuk = block.querySelector('input[name$="[tanggal_masuk]"]');

        if (!pelanggan.value) {
            hasError = true;
            errorMessage += `• Pilih pelanggan untuk Transaksi #${tIdx + 1}\n`;
        }
        if (!tanggalMasuk.value) {
            hasError = true;
            errorMessage += `• Isi tanggal masuk untuk Transaksi #${tIdx + 1}\n`;
        }

        block.querySelectorAll('.layanan-item').forEach((item, lIdx) => {
            const select = item.querySelector('.layanan-select');
            const jumlah = item.querySelector('.jumlah-input');
            if (!select.value) {
                hasError = true;
                errorMessage += `• Pilih layanan untuk Item #${lIdx + 1} di Transaksi #${tIdx + 1}\n`;
            }
            if (!jumlah.value || parseFloat(jumlah.value) <= 0) {
                hasError = true;
                errorMessage += `• Isi jumlah untuk Item #${lIdx + 1} di Transaksi #${tIdx + 1}\n`;
            }
        });
    });

    if (hasError) {
        Swal.fire({ icon: 'error', title: 'Form Tidak Valid!', text: errorMessage, confirmButtonText: 'OK' });
        return;
    }

    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    this.submit();
});

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', function() {
    // Render initial block
    layananIndices[0] = 1;
    const container = document.getElementById('transaksi-container');
    container.appendChild(buildTransaksiBlock(0));
    updateRemoveTransaksiButtons();
    updateRemoveLayananButtons();
});
</script>
</x-app-layout>
