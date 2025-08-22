<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Edit Layanan</h2>
                    <a href="{{ route('layanan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal!',
                                html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                <form action="{{ route('layanan.update', $layanan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_layanan" class="block text-sm font-medium text-gray-700">Nama Layanan</label>
                            <input type="text" name="nama_layanan" id="nama_layanan" 
                                value="{{ old('nama_layanan', $layanan->nama_layanan) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                placeholder="Contoh: Cuci Kering" required>
                        </div>

                        <div>
                            <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan</label>
                            <select name="satuan" id="satuan" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Satuan</option>
                                <option value="KG" {{ old('satuan', $layanan->satuan) == 'KG' ? 'selected' : '' }}>Kilogram (KG)</option>
                                <option value="PCS" {{ old('satuan', $layanan->satuan) == 'PCS' ? 'selected' : '' }}>Pieces (PCS)</option>
                                <option value="M" {{ old('satuan', $layanan->satuan) == 'M' ? 'selected' : '' }}>Meter (M)</option>
                            </select>
                        </div>

                        <div>
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga per Satuan</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="harga" id="harga" 
                                    value="{{ old('harga', $layanan->harga) }}"
                                    class="block w-full pl-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="10000" min="0" step="100" required>
                            </div>
                        </div>

                        <div>
                            <label for="estimasi_waktu" class="block text-sm font-medium text-gray-700">Estimasi Waktu (Hari)</label>
                            <input type="number" name="estimasi_waktu" id="estimasi_waktu" 
                                value="{{ old('estimasi_waktu', $layanan->estimasi_waktu) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                placeholder="1" min="1" max="30">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Deskripsi layanan (opsional)">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Layanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Form validation dan submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nama = document.getElementById('nama_layanan').value.trim();
            const satuan = document.getElementById('satuan').value;
            const harga = document.getElementById('harga').value;
            
            let hasError = false;
            let errorMessage = '';
            
            if (!nama) {
                hasError = true;
                errorMessage += '• Nama layanan harus diisi\n';
            }
            
            if (!satuan) {
                hasError = true;
                errorMessage += '• Satuan harus dipilih\n';
            }
            
            if (!harga || parseFloat(harga) <= 0) {
                hasError = true;
                errorMessage += '• Harga harus diisi dan lebih dari 0\n';
            }
            
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
                title: 'Memperbarui Layanan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            form.submit();
        });
    });
</script>
