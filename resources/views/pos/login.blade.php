<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>POS Login - {{ config('app.name', 'Laundry') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-600 to-indigo-800 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4" x-data="posLogin()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cash-register text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">POS Login</h1>
                <p class="text-gray-500 mt-2">Masukkan PIN untuk melanjutkan</p>
            </div>

            <!-- Alert Messages -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('pos.verify-pin') }}" id="pinForm">
                @csrf

                <!-- User Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kasir</label>
                    <select name="user_id" x-model="selectedUser" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                        <option value="">-- Pilih Kasir --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @if($users->isEmpty())
                        <p class="text-sm text-orange-600 mt-2">
                            <i class="fas fa-info-circle"></i> Belum ada kasir yang memiliki PIN. Hubungi admin.
                        </p>
                    @endif
                </div>

                <!-- PIN Display -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PIN (6 digit)</label>
                    <div class="flex justify-center gap-2 mb-4">
                        <template x-for="i in 6" :key="i">
                            <div class="w-12 h-14 border-2 rounded-lg flex items-center justify-center text-2xl font-bold"
                                :class="pin.length >= i ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <span x-show="pin.length >= i">•</span>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="pin" :value="pin">
                </div>

                <!-- Numpad -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num">
                        <button type="button" @click="addDigit(num)"
                            class="h-16 text-2xl font-semibold bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <span x-text="num"></span>
                        </button>
                    </template>
                    <button type="button" @click="clearPin()"
                        class="h-16 text-lg font-semibold bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="button" @click="addDigit(0)"
                        class="h-16 text-2xl font-semibold bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        0
                    </button>
                    <button type="button" @click="removeDigit()"
                        class="h-16 text-lg font-semibold bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition-colors">
                        <i class="fas fa-backspace"></i>
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="pin.length !== 6 || !selectedUser"
                    class="w-full py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Back to main site link -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Kembali ke Login Utama
                </a>
            </div>
        </div>
    </div>

    <script>
        function posLogin() {
            return {
                pin: '',
                selectedUser: '',

                addDigit(num) {
                    if (this.pin.length < 6) {
                        this.pin += num.toString();
                    }
                },

                removeDigit() {
                    this.pin = this.pin.slice(0, -1);
                },

                clearPin() {
                    this.pin = '';
                }
            }
        }
    </script>
</body>
</html>
