<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Layanan') }}
        </h2>
    </x-slot>

    <div class="py-6">
    <div class="max-w-full mx-auto sm:px-4 lg:px-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 text-gray-900">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Layanan</h2>
                    <a href="{{ route('layanan.create') }}" class="inline-flex items-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white py-1.5 px-3 rounded text-sm">
                        <i class="fas fa-plus"></i><span class="ml-1">Tambah Layanan</span>
                    </a>
                </div>

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: @json(session('success')),
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: @json(session('error')),
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                <div class="overflow-x-auto bg-white shadow-md rounded-lg" style="max-width: 100%; overflow-x: auto;">
                    <table class="w-full divide-y divide-gray-200 table-auto text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Nama Layanan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    Satuan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Harga
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Deskripsi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-60 min-w-[240px]">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($layanan as $l)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $l->nama_layanan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $l->satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($l->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $l->deskripsi ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium min-w-[240px]">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('layanan.show', $l->id) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                <span class="flex items-center"><i class="fas fa-eye"></i><span class="ml-1">Detail</span></span>
                                            </a>
                                            <a href="{{ route('layanan.edit', $l->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                <span class="flex items-center"><i class="fas fa-edit"></i><span class="ml-1">Edit</span></span>
                                            </a>
                                            <form action="{{ route('layanan.destroy', $l->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs delete-btn" 
                                                    data-layanan="{{ $l->nama_layanan }}">
                                                    <span class="flex items-center"><i class="fas fa-trash"></i><span class="ml-1">Hapus</span></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada data layanan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-center">
                    {{ $layanan->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const namaLayanan = this.getAttribute('data-layanan');
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Hapus Layanan?',
                        text: `Layanan "${namaLayanan}" akan dihapus permanen!`,
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
</x-app-layout>
