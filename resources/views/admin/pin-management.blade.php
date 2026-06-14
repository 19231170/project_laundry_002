<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-key mr-2"></i> Manajemen PIN POS
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="pinManagement()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800">Tentang PIN POS</h3>
                        <p class="text-blue-700 text-sm mt-1">
                            PIN digunakan untuk login ke sistem POS (Point of Sale) yang terpisah dari sistem utama.
                            Hanya user yang memiliki PIN yang dapat mengakses halaman POS di <code class="bg-blue-100 px-1 rounded">/pos</code>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar User</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PIN Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                @if($user->id === auth()->id())
                                                    <span class="text-xs text-blue-600">(Anda)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button type="button" 
                                            @click="toggleAdmin({{ $user->id }}, {{ $user->is_admin ? 'true' : 'false' }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-colors
                                                {{ $user->is_admin ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            id="admin-badge-{{ $user->id }}">
                                            <i class="fas {{ $user->is_admin ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>
                                            <span id="admin-text-{{ $user->id }}">{{ $user->is_admin ? 'Admin' : 'User' }}</span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span id="pin-status-{{ $user->id }}" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                {{ $user->hasPosPin() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            <i class="fas {{ $user->hasPosPin() ? 'fa-lock' : 'fa-lock-open' }} mr-1"></i>
                                            {{ $user->hasPosPin() ? 'PIN Aktif' : 'Belum Ada PIN' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="openPinModal({{ $user->id }}, '{{ $user->name }}')"
                                                class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm transition-colors">
                                                <i class="fas fa-key mr-1"></i>
                                                {{ $user->hasPosPin() ? 'Ubah PIN' : 'Set PIN' }}
                                            </button>
                                            @if($user->hasPosPin())
                                                <button type="button" @click="removePin({{ $user->id }}, '{{ $user->name }}')"
                                                    class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Link to POS -->
            <div class="mt-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Akses POS</h3>
                        <p class="text-blue-100 text-sm mt-1">Buka halaman Point of Sale untuk kasir</p>
                    </div>
                    <a href="{{ route('pos.login') }}" target="_blank"
                        class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Buka POS
                    </a>
                </div>
            </div>
        </div>

        <!-- PIN Modal -->
        <div x-show="showPinModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4" @click.away="showPinModal = false">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-key mr-2 text-blue-600"></i>
                        Set PIN untuk <span x-text="selectedUserName"></span>
                    </h3>
                    <button type="button" @click="showPinModal = false" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4">
                        Masukkan PIN 6 digit untuk user ini. PIN akan digunakan untuk login ke sistem POS.
                    </p>

                    <!-- PIN Input Display -->
                    <div class="flex justify-center gap-2 mb-6">
                        <template x-for="i in 6" :key="i">
                            <div class="w-12 h-14 border-2 rounded-lg flex items-center justify-center text-2xl font-bold"
                                :class="newPin.length >= i ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <span x-text="newPin[i-1] || ''"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Numpad -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num">
                            <button type="button" @click="addPinDigit(num)"
                                class="h-14 text-xl font-semibold bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <span x-text="num"></span>
                            </button>
                        </template>
                        <button type="button" @click="newPin = ''"
                            class="h-14 text-sm font-semibold bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                            Clear
                        </button>
                        <button type="button" @click="addPinDigit(0)"
                            class="h-14 text-xl font-semibold bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            0
                        </button>
                        <button type="button" @click="newPin = newPin.slice(0, -1)"
                            class="h-14 text-sm font-semibold bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition-colors">
                            <i class="fas fa-backspace"></i>
                        </button>
                    </div>

                    <button type="button" @click="savePin()"
                        :disabled="newPin.length !== 6"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan PIN
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pinManagement() {
            return {
                showPinModal: false,
                selectedUserId: null,
                selectedUserName: '',
                newPin: '',

                openPinModal(userId, userName) {
                    this.selectedUserId = userId;
                    this.selectedUserName = userName;
                    this.newPin = '';
                    this.showPinModal = true;
                },

                addPinDigit(num) {
                    if (this.newPin.length < 6) {
                        this.newPin += num.toString();
                    }
                },

                async savePin() {
                    if (this.newPin.length !== 6) return;

                    try {
                        const response = await fetch(`/admin/users/${this.selectedUserId}/pin`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ pin: this.newPin })
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil', data.message, 'success');
                            this.showPinModal = false;
                            // Update UI
                            const statusEl = document.getElementById(`pin-status-${this.selectedUserId}`);
                            if (statusEl) {
                                statusEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                                statusEl.innerHTML = '<i class="fas fa-lock mr-1"></i> PIN Aktif';
                            }
                            // Reload to update buttons
                            location.reload();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Gagal menyimpan PIN', 'error');
                    }
                },

                async removePin(userId, userName) {
                    const result = await Swal.fire({
                        title: 'Hapus PIN?',
                        text: `Hapus PIN untuk ${userName}? User tidak akan bisa login ke POS.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    });

                    if (!result.isConfirmed) return;

                    try {
                        const response = await fetch(`/admin/users/${userId}/pin`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil', data.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Gagal menghapus PIN', 'error');
                    }
                },

                async toggleAdmin(userId, currentStatus) {
                    const action = currentStatus ? 'menghapus status admin' : 'menjadikan admin';
                    const result = await Swal.fire({
                        title: 'Konfirmasi',
                        text: `Apakah Anda yakin ingin ${action} user ini?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    });

                    if (!result.isConfirmed) return;

                    try {
                        const response = await fetch(`/admin/users/${userId}/toggle-admin`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil', data.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Gagal mengubah status admin', 'error');
                    }
                }
            }
        }
    </script>
</x-app-layout>
