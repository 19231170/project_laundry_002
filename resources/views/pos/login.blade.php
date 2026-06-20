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

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
            20%, 40%, 60%, 80% { transform: translateX(8px); }
        }
        @keyframes successPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes dotFill {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.7); }
        }
        .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-shake { animation: shake 0.5s ease-in-out; }
        .animate-success { animation: successPulse 0.5s ease-out forwards; }
        .animate-dot { animation: dotFill 0.15s ease-out forwards; }
        .animate-glow { animation: glow 2s ease-in-out infinite; }
        .animate-float { animation: float 3s ease-in-out infinite; }

        .bg-animated {
            background: linear-gradient(-45deg, #1e3a5f, #2563eb, #4338ca, #1e3a5f);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .numpad-btn {
            transition: all 0.15s ease;
        }
        .numpad-btn:hover { transform: scale(1.05); }
        .numpad-btn:active { transform: scale(0.95); }

        .pin-dot-filled {
            animation: dotFill 0.15s ease-out forwards;
        }

        .success-overlay {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
    </style>
</head>
<body class="font-sans antialiased bg-animated min-h-screen">
    <!-- Decorative floating elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-blue-400/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-40 right-20 w-48 h-48 bg-purple-400/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-1/4 w-40 h-40 bg-indigo-400/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4" x-data="posLogin()" @keydown.window="handleKeydown($event)">
        <div class="glass-card rounded-3xl shadow-2xl w-full max-w-md p-8 relative overflow-hidden animate-slide-up"
             :class="{ 'animate-shake': shaking }">

            <!-- Success Overlay -->
            <div x-show="loginSuccess" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="success-overlay absolute inset-0 z-50 flex flex-col items-center justify-center rounded-3xl">
                <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center animate-success shadow-lg">
                    <i class="fas fa-check text-white text-4xl"></i>
                </div>
                <p class="mt-4 text-gray-700 font-semibold text-lg">Login Berhasil!</p>
                <p class="text-gray-500 text-sm mt-1">Mengalihkan...</p>
            </div>

            <!-- Loading Overlay -->
            <div x-show="loading" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-white/80 backdrop-blur-sm z-40 flex items-center justify-center rounded-3xl">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="mt-3 text-gray-600 font-medium">Memproses...</p>
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="relative inline-block">
                    <div class="w-22 h-22 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg animate-glow">
                        <i class="fas fa-cash-register text-white text-3xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-400 rounded-full border-2 border-white">
                        <i class="fas fa-wifi text-white text-xs flex items-center justify-center h-full"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">POS Kasir</h1>
                <p class="text-gray-500 mt-1 text-sm">Masukkan PIN 6 digit untuk masuk</p>
            </div>

            <!-- Alert Messages -->
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <p class="text-sm flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-400 hover:text-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <form method="POST" action="{{ route('pos.verify-pin') }}" id="pinForm" @submit.prevent="submitForm()">
                @csrf

                <!-- User Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-user text-blue-500"></i>
                        Pilih Kasir
                    </label>
                    <div class="relative">
                        <select name="user_id" x-model="selectedUser" required
                            class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Kasir --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    @if($users->isEmpty())
                        <p class="text-sm text-orange-500 mt-2 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i> Belum ada kasir dengan PIN. Hubungi admin.
                        </p>
                    @endif
                </div>

                <!-- PIN Display -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-600 flex items-center gap-2">
                            <i class="fas fa-lock text-blue-500"></i>
                            PIN (6 digit)
                        </label>
                        <button type="button" @click="showPin = !showPin" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
                            <i :class="showPin ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <div class="flex justify-center gap-3 mb-4">
                        <template x-for="(dot, i) in 6" :key="i">
                            <div class="w-12 h-14 border-2 rounded-xl flex items-center justify-center transition-all duration-200"
                                :class="pin.length > i
                                    ? (pinError ? 'border-red-400 bg-red-50' : 'border-blue-500 bg-blue-50')
                                    : 'border-gray-200 hover:border-blue-300'">
                                <span x-show="pin.length > i && !showPin"
                                      class="text-2xl font-bold text-blue-600 animate-dot"
                                      :style="`animation-delay: ${i * 30}ms`">•</span>
                                <span x-show="pin.length > i && showPin"
                                      class="text-lg font-bold text-blue-600 animate-dot"
                                      :style="`animation-delay: ${i * 30}ms`"
                                      x-text="pin[i]"></span>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="pin" :value="pin">

                    <!-- PIN strength indicator -->
                    <div class="flex justify-center gap-1">
                        <template x-for="i in 6" :key="i">
                            <div class="h-1.5 w-8 rounded-full transition-all duration-200"
                                 :class="pin.length >= i
                                     ? (pinError ? 'bg-red-400' : 'bg-blue-500')
                                     : 'bg-gray-200'">
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Numpad -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num">
                        <button type="button" @click="addDigit(num)"
                            class="numpad-btn h-16 text-2xl font-bold bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl transition-all shadow-sm text-gray-700">
                            <span x-text="num"></span>
                        </button>
                    </template>
                    <button type="button" @click="clearPin()"
                        class="numpad-btn h-16 text-base font-semibold bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Clear</span>
                    </button>
                    <button type="button" @click="addDigit(0)"
                        class="numpad-btn h-16 text-2xl font-bold bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl transition-all shadow-sm text-gray-700">
                        0
                    </button>
                    <button type="button" @click="removeDigit()"
                        class="numpad-btn h-16 text-base font-semibold bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-500 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-backspace"></i>
                        <span>Hapus</span>
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    :disabled="pin.length !== 6 || !selectedUser || loading"
                    class="w-full py-4 rounded-xl font-bold text-lg transition-all flex items-center justify-center gap-3"
                    :class="pin.length === 6 && selectedUser
                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5'
                        : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <template x-if="loading">
                        <div class="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </template>
                    <template x-if="!loading">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk
                        </span>
                    </template>
                </button>

                <!-- Hint -->
                <p class="text-center text-gray-400 text-xs mt-4">
                    <i class="fas fa-keyboard mr-1"></i>
                    Gunakan keyboard: 0-9 untuk input, Backspace untuk hapus
                </p>
            </form>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
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
                showPin: false,
                loading: false,
                shaking: false,
                pinError: false,
                loginSuccess: false,

                addDigit(num) {
                    if (this.pin.length < 6) {
                        this.pin += num.toString();
                        // Haptic-style feedback - button ripple effect
                        this.createRipple();
                    }
                },

                removeDigit() {
                    this.pin = this.pin.slice(0, -1);
                },

                clearPin() {
                    this.pin = '';
                    this.pinError = false;
                },

                handleKeydown(event) {
                    // Ignore if form is loading or success shown
                    if (this.loading || this.loginSuccess) return;

                    // Ignore if user is typing in select
                    if (event.target.tagName === 'SELECT') return;

                    // Number keys
                    if (event.key >= '0' && event.key <= '9') {
                        event.preventDefault();
                        this.addDigit(parseInt(event.key));
                    }
                    // Backspace
                    else if (event.key === 'Backspace') {
                        event.preventDefault();
                        this.removeDigit();
                    }
                    // Escape - clear
                    else if (event.key === 'Escape') {
                        event.preventDefault();
                        this.clearPin();
                    }
                    // Enter - submit
                    else if (event.key === 'Enter' && this.pin.length === 6 && this.selectedUser) {
                        event.preventDefault();
                        this.submitForm();
                    }
                    // Delete key
                    else if (event.key === 'Delete') {
                        event.preventDefault();
                        this.clearPin();
                    }
                },

                async submitForm() {
                    if (this.pin.length !== 6 || !this.selectedUser) return;

                    this.loading = true;

                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('user_id', this.selectedUser);
                    formData.append('pin', this.pin);

                    try {
                        const response = await fetch('{{ route('pos.verify-pin') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.loginSuccess = true;
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route('pos.index') }}';
                            }, 1000);
                        } else {
                            this.loading = false;
                            this.pinError = true;
                            this.shaking = true;

                            // Show error message
                            this.showError(data.message || 'PIN salah!');

                            setTimeout(() => {
                                this.shaking = false;
                                this.pinError = false;
                                this.clearPin();
                            }, 500);
                        }
                    } catch (error) {
                        this.loading = false;
                        this.pinError = true;
                        this.shaking = true;

                        this.showError('Terjadi kesalahan. Silakan coba lagi.');

                        setTimeout(() => {
                            this.shaking = false;
                            this.pinError = false;
                            this.clearPin();
                        }, 500);
                    }
                },

                showError(message) {
                    // Create and show error toast
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3 animate-slide-up';
                    errorDiv.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i>
                        <span>${message}</span>
                    `;
                    document.body.appendChild(errorDiv);

                    setTimeout(() => {
                        errorDiv.style.opacity = '0';
                        errorDiv.style.transition = 'opacity 0.3s';
                        setTimeout(() => errorDiv.remove(), 300);
                    }, 3000);
                },

                createRipple() {
                    // Visual feedback for numpad press
                    const buttons = document.querySelectorAll('.numpad-btn');
                    buttons.forEach(btn => {
                        if (btn.textContent.trim() === this.pin.slice(-1).toString() ||
                            (this.pin.slice(-1) === '0' && btn.textContent.trim() === '0')) {
                            // Skip - handled by CSS
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
