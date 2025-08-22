<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Data Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Daftar Pelanggan</h2>
                        <a href="{{ route('pelanggan.create') }}" class="inline-flex items-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white py-1.5 px-3 rounded text-sm">
                            <i class="fas fa-plus"></i><span class="ml-1">Tambah Pelanggan</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto bg-white shadow-md rounded-lg" style="max-width: 100%; overflow-x: auto;">
                        <table class="w-full divide-y divide-gray-200 table-auto text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                        Telepon
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                        Total Transaksi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-60 min-w-[240px]">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pelanggan as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->nama }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($item->alamat, 50) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->telepon ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->email ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->transaksi()->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium min-w-[240px]">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('pelanggan.show', $item) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    <span class="flex items-center"><i class="fas fa-eye"></i><span class="ml-1">Detail</span></span>
                                                </a>
                                                <a href="{{ route('pelanggan.edit', $item) }}" class="inline-block bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    <span class="flex items-center"><i class="fas fa-edit"></i><span class="ml-1">Edit</span></span>
                                                </a>
                                                <form action="{{ route('pelanggan.destroy', $item->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs delete-btn"
                                                        data-pelanggan="{{ $item->nama }}">
                                                        <span class="flex items-center"><i class="fas fa-trash"></i><span class="ml-1">Hapus</span></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada data pelanggan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 flex justify-center">
                        {{ $pelanggan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notifikasi Sukses
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Notifikasi Error
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    showConfirmButton: true
                });
            @endif

            // Konfirmasi Hapus
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const namaPelanggan = this.getAttribute('data-pelanggan');
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Hapus Pelanggan?',
                        text: `Pelanggan "${namaPelanggan}" akan dihapus permanen!`,
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
        });
    </script>
    @endpush
</x-app-layout>
