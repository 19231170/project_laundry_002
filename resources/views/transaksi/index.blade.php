<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Transaksi</h2>
                    <a href="{{ route('transaksi.create') }}" class="inline-flex items-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white py-1.5 px-3 rounded text-sm">
                        <i class="fas fa-plus"></i><span class="ml-1">Tambah Transaksi</span>
                    </a>
                </div>
                
                <!-- Filter Controls -->
                <div class="flex mb-4 space-x-2">
                    <a href="{{ route('transaksi.index') }}" class="px-3 py-1.5 text-xs font-medium border rounded {{ !request('filter') ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        Semua
                    </a>
                    <a href="{{ route('transaksi.index', ['filter' => 'lunas']) }}" class="px-3 py-1.5 text-xs font-medium border rounded {{ request('filter') == 'lunas' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        Lunas
                    </a>
                    <a href="{{ route('transaksi.index', ['filter' => 'belum_lunas']) }}" class="px-3 py-1.5 text-xs font-medium border rounded {{ request('filter') == 'belum_lunas' ? 'bg-red-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        Belum Lunas
                    </a>
                </div>

                <!-- Alert notifications -->
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
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
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
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

                <div class="overflow-x-auto bg-white shadow-md rounded-lg" style="max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="w-full divide-y divide-gray-200 table-auto text-sm border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Kode Transaksi
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                    Pelanggan
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                    Tanggal Masuk
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                    Tanggal Selesai
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    Total
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">
                                    Status
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    Pembayaran
                                </th>
                                <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[150px] min-w-[150px]">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transaksi as $t)
                                <tr>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs font-medium text-gray-900">
                                        {{ $t->kode_transaksi }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">
                                        {{ $t->pelanggan->nama }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">
                                        {{ $t->tanggal_masuk ? $t->tanggal_masuk->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">
                                        {{ $t->tanggal_selesai ? $t->tanggal_selesai->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">
                                        Rp {{ number_format($t->total_setelah_pembulatan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <span class="px-1 inline-flex text-xs leading-4 font-medium rounded-full 
                                            {{ $t->status == 'selesai' ? 'bg-green-100 text-green-800' : 
                                               ($t->status == 'proses' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <span class="px-1 inline-flex text-xs leading-4 font-medium rounded-full 
                                            {{ $t->status_pembayaran == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $t->status_pembayaran == 'lunas' ? 'Lunas' : 'Belum' }}
                                        </span>
                                        @if ($t->status_pembayaran == 'belum_lunas' && $t->jumlah_dibayar > 0)
                                            <div class="text-xs text-gray-500 mt-0.5 text-center">
                                                Rp{{ number_format($t->jumlah_dibayar, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-sm min-w-[200px]">
                                        <div class="grid grid-cols-3 gap-1 justify-items-center">
                                            <a href="{{ route('transaksi.show', $t->id) }}" class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-xs w-full">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                            <a href="{{ route('transaksi.edit', $t->id) }}" class="inline-flex items-center justify-center bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-xs w-full">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            
                                            @if($t->status_pembayaran == 'belum_lunas')
                                            <form action="{{ route('transaksi.mark-as-paid', $t->id) }}" method="POST" class="col-span-3 w-full">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded text-xs w-full mark-paid-btn" 
                                                    data-transaksi="{{ $t->kode_transaksi }}">
                                                    <i class="fas fa-check-circle mr-1"></i>Lunasi
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST" class="col-span-{{ $t->status_pembayaran == 'belum_lunas' ? '3' : '1' }} w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="inline-flex items-center justify-center bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-xs w-full delete-btn" 
                                                    data-transaksi="{{ $t->kode_transaksi }}">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada data transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-center">
                    {{ $transaksi->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notifications will be moved to the main layout
            // Konfirmasi Hapus dan Lunasi Pembayaran
            // Delete transaction confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const kodeTransaksi = this.getAttribute('data-transaksi');
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Hapus Transaksi?',
                        text: `Transaksi ${kodeTransaksi} akan dihapus permanen!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Mark as paid confirmation
            document.querySelectorAll('.mark-paid-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const kodeTransaksi = this.getAttribute('data-transaksi');
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Ubah Status Pembayaran?',
                        text: `Transaksi ${kodeTransaksi} akan ditandai sebagai LUNAS!`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Tandai Lunas!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
