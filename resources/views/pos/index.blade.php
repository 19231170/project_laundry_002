<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>POS - {{ config('app.name', 'Laundry') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        .main-full {
            margin-left: 0 !important;
        }
        
        /* Service button interactive effects */
        .service-btn {
            transition: all 0.2s ease-in-out;
            transform: scale(1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .service-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
            border-color: #3b82f6 !important;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%) !important;
        }
        .service-btn:active {
            transform: scale(0.97);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
        }
        .service-btn:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
        }
        .service-btn .service-icon {
            transition: all 0.2s ease-in-out;
        }
        .service-btn:hover .service-icon {
            transform: scale(1.1);
        }
        .service-btn:active .service-icon {
            transform: scale(0.95);
        }
        
        /* Ripple effect */
        .service-btn {
            position: relative;
            overflow: hidden;
        }
        .service-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }
        .service-btn:active::after {
            width: 200px;
            height: 200px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="posApp()" class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg fixed h-full z-40 transition-transform duration-300"
            :class="{ 'sidebar-hidden': !showSidebar }">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-blue-600">
                        <i class="fas fa-cash-register mr-2"></i>POS
                    </h1>
                    <button @click="showSidebar = false" class="text-gray-500 hover:text-gray-700 lg:hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b bg-blue-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ session('pos_user_name') }}</p>
                        <p class="text-xs text-gray-500">Kasir</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-100 text-blue-700 rounded-lg">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Transaksi Baru</span>
                </a>
            </nav>

            <!-- Session Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-gray-50">
                <div class="text-xs text-gray-500 mb-3">
                    <i class="fas fa-clock mr-1"></i>
                    Sesi aktif: <span x-text="sessionTime"></span>
                </div>
                <form action="{{ route('pos.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 transition-all duration-300" :class="{ 'ml-64': showSidebar, 'main-full': !showSidebar }">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="showSidebar = !showSidebar" 
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h2 class="text-lg font-semibold text-gray-800">Transaksi Baru</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </header>

            <div class="flex flex-col lg:flex-row h-[calc(100vh-60px)]">
                <!-- Services Grid -->
                <div class="flex-1 p-4 overflow-y-auto">
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" x-model="searchLayanan" placeholder="Cari layanan..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Services by Category (Satuan) -->
                    @foreach($layananGrouped as $satuan => $items)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-white
                                    @if($satuan === 'KG') bg-green-500
                                    @elseif($satuan === 'PCS') bg-blue-500
                                    @else bg-purple-500
                                    @endif">
                                    @if($satuan === 'KG')
                                        <i class="fas fa-weight text-xs"></i>
                                    @elseif($satuan === 'PCS')
                                        <i class="fas fa-tshirt text-xs"></i>
                                    @else
                                        <i class="fas fa-ruler text-xs"></i>
                                    @endif
                                </span>
                                Per {{ $satuan }}
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($items as $item)
                                    <button type="button" 
                                        x-show="!searchLayanan || '{{ strtolower($item->nama_layanan) }}'.includes(searchLayanan.toLowerCase())"
                                        @click="addToCart({{ $item->id }}, '{{ $item->nama_layanan }}', {{ $item->harga }}, '{{ $item->satuan }}')"
                                        class="service-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-left group cursor-pointer">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="service-icon w-10 h-10 rounded-lg flex items-center justify-center
                                                @if($item->satuan === 'KG') bg-green-100 text-green-600
                                                @elseif($item->satuan === 'PCS') bg-blue-100 text-blue-600
                                                @else bg-purple-100 text-purple-600
                                                @endif">
                                                @if($item->satuan === 'KG')
                                                    <i class="fas fa-weight"></i>
                                                @elseif($item->satuan === 'PCS')
                                                    <i class="fas fa-tshirt"></i>
                                                @else
                                                    <i class="fas fa-ruler"></i>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                                {{ $item->satuan }}
                                            </span>
                                        </div>
                                        <div class="font-semibold text-gray-800 group-hover:text-blue-600 mb-1 truncate transition-colors">
                                            {{ $item->nama_layanan }}
                                        </div>
                                        <div class="text-lg font-bold text-blue-600">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Panel -->
                <div class="w-full lg:w-96 bg-white border-l flex flex-col">
                    <!-- Customer Selection -->
                    <div class="p-4 border-b">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pelanggan</label>
                        <div class="relative">
                            <input type="text" x-model="customerSearch" @input="searchCustomer()"
                                placeholder="Cari atau pilih pelanggan..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Customer Results -->
                        <div x-show="customerResults.length > 0" x-cloak
                            class="absolute z-20 w-80 bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                            <template x-for="customer in customerResults" :key="customer.id">
                                <button type="button" @click="selectCustomer(customer)"
                                    class="w-full px-4 py-2 text-left hover:bg-blue-50 border-b last:border-b-0">
                                    <div class="font-medium" x-text="customer.nama"></div>
                                    <div class="text-xs text-gray-500" x-text="customer.telepon || '-'"></div>
                                </button>
                            </template>
                        </div>

                        <!-- Selected Customer -->
                        <div x-show="selectedCustomer" x-cloak class="mt-2 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                            <div>
                                <span class="font-medium text-blue-800" x-text="selectedCustomer?.nama"></span>
                                <span class="text-xs text-blue-600 ml-2" x-text="selectedCustomer?.telepon"></span>
                            </div>
                            <button type="button" @click="selectedCustomer = null; customerSearch = ''" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Quick Add Customer -->
                        <button type="button" @click="showAddCustomerModal = true"
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus mr-1"></i> Tambah Pelanggan Baru
                        </button>
                    </div>

                    <!-- Cart Items -->
                    <div class="flex-1 p-4 overflow-y-auto">
                        <template x-if="cart.length === 0">
                            <div class="text-center text-gray-400 py-8">
                                <i class="fas fa-shopping-basket text-4xl mb-3"></i>
                                <p>Keranjang kosong</p>
                                <p class="text-sm">Pilih layanan untuk memulai</p>
                            </div>
                        </template>

                        <template x-for="(item, index) in cart" :key="index">
                            <div class="bg-gray-50 rounded-lg p-3 mb-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800" x-text="item.nama"></div>
                                        <div class="text-sm text-gray-500">
                                            Rp <span x-text="formatNumber(item.harga)"></span> / <span x-text="item.satuan"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="updateQty(index, -0.5)" 
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" x-model.number="item.jumlah" @change="updateSubtotal(index)"
                                        step="0.1" min="0.1"
                                        class="w-20 text-center border border-gray-300 rounded-lg py-1">
                                    <button type="button" @click="updateQty(index, 0.5)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    <div class="flex-1 text-right font-semibold text-blue-600">
                                        Rp <span x-text="formatNumber(item.subtotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Cart Summary & Checkout -->
                    <div class="border-t p-4 bg-gray-50">
                        <!-- Subtotal -->
                        <div class="flex justify-between text-gray-600 mb-2">
                            <span>Subtotal</span>
                            <span>Rp <span x-text="formatNumber(subtotal)"></span></span>
                        </div>

                        <!-- Pembulatan -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Pembulatan</span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="pembulatan -= 100" class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded text-xs">-</button>
                                <input type="number" x-model.number="pembulatan" class="w-20 text-center text-sm border rounded py-1">
                                <button type="button" @click="pembulatan += 100" class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded text-xs">+</button>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between text-xl font-bold text-gray-800 py-3 border-t border-b mb-3">
                            <span>TOTAL</span>
                            <span class="text-blue-600">Rp <span x-text="formatNumber(total)"></span></span>
                        </div>

                        <!-- Payment Input -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" x-model.number="jumlahBayar" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-lg font-semibold focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Change -->
                        <div class="flex justify-between mb-3" x-show="jumlahBayar >= total">
                            <span class="text-gray-600">Kembalian</span>
                            <span class="font-bold text-green-600">Rp <span x-text="formatNumber(kembalian)"></span></span>
                        </div>

                        <!-- Quick Cash Buttons -->
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <button type="button" @click="jumlahBayar = total" class="py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-sm font-medium">
                                Pas
                            </button>
                            <button type="button" @click="jumlahBayar = Math.ceil(total / 10000) * 10000" class="py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                10rb
                            </button>
                            <button type="button" @click="jumlahBayar = Math.ceil(total / 50000) * 50000" class="py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                50rb
                            </button>
                            <button type="button" @click="jumlahBayar = Math.ceil(total / 100000) * 100000" class="py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                100rb
                            </button>
                        </div>

                        <!-- Payment Status -->
                        <div class="flex gap-2 mb-3">
                            <button type="button" @click="statusPembayaran = 'lunas'"
                                :class="statusPembayaran === 'lunas' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="flex-1 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-check mr-1"></i> Lunas
                            </button>
                            <button type="button" @click="statusPembayaran = 'belum_lunas'"
                                :class="statusPembayaran === 'belum_lunas' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="flex-1 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-clock mr-1"></i> Belum Lunas
                            </button>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-3">
                            <textarea x-model="catatan" placeholder="Catatan (opsional)..." rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Checkout Button -->
                        <button type="button" @click="processCheckout()"
                            :disabled="cart.length === 0 || !selectedCustomer || isProcessing"
                            class="w-full py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg text-lg transition-colors">
                            <template x-if="!isProcessing">
                                <span><i class="fas fa-check-circle mr-2"></i> Proses Transaksi</span>
                            </template>
                            <template x-if="isProcessing">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Add Customer Modal -->
        <div x-show="showAddCustomerModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4" @click.away="showAddCustomerModal = false">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Tambah Pelanggan Baru</h3>
                    <button type="button" @click="showAddCustomerModal = false" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                        <input type="text" x-model="newCustomer.nama" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" x-model="newCustomer.telepon"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea x-model="newCustomer.alamat" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="button" @click="saveCustomer()"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 text-center p-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Transaksi Berhasil!</h3>
                <p class="text-gray-600 mb-4">Kode: <span class="font-mono font-bold" x-text="lastTransaction?.kode_transaksi"></span></p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Total</span>
                        <span class="font-semibold">Rp <span x-text="formatNumber(lastTransaction?.total)"></span></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Dibayar</span>
                        <span class="font-semibold">Rp <span x-text="formatNumber(lastTransaction?.dibayar)"></span></span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span class="font-semibold text-green-600">Kembalian</span>
                        <span class="font-bold text-green-600">Rp <span x-text="formatNumber(lastTransaction?.kembalian)"></span></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="printReceipt()"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg">
                        <i class="fas fa-print mr-2"></i> Cetak Struk
                    </button>
                    <button type="button" @click="resetAndClose()"
                        class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posApp() {
            return {
                // UI State
                showSidebar: localStorage.getItem('pos_sidebar') !== 'hidden',
                showAddCustomerModal: false,
                showSuccessModal: false,
                isProcessing: false,
                sessionTime: '00:00',

                // Search
                searchLayanan: '',
                customerSearch: '',
                customerResults: [],

                // Customer
                selectedCustomer: null,
                newCustomer: { nama: '', telepon: '', alamat: '' },

                // Cart
                cart: [],
                pembulatan: 0,
                jumlahBayar: 0,
                statusPembayaran: 'lunas',
                catatan: '',

                // Last Transaction
                lastTransaction: null,

                // Computed
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
                },
                get total() {
                    return Math.max(0, this.subtotal + this.pembulatan);
                },
                get kembalian() {
                    return Math.max(0, this.jumlahBayar - this.total);
                },

                // Init
                init() {
                    this.$watch('showSidebar', (value) => {
                        localStorage.setItem('pos_sidebar', value ? 'visible' : 'hidden');
                    });

                    // Update session timer
                    this.updateSessionTime();
                    setInterval(() => this.updateSessionTime(), 1000);
                },

                updateSessionTime() {
                    const start = {{ session('pos_last_activity', time()) }};
                    const now = Math.floor(Date.now() / 1000);
                    const elapsed = now - start;
                    const remaining = Math.max(0, 600 - elapsed); // 10 minutes = 600 seconds
                    const mins = Math.floor(remaining / 60);
                    const secs = remaining % 60;
                    this.sessionTime = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                    if (remaining <= 0) {
                        window.location.href = '{{ route("pos.login") }}';
                    }
                },

                // Cart Methods
                addToCart(id, nama, harga, satuan) {
                    const existing = this.cart.find(item => item.layanan_id === id);
                    if (existing) {
                        existing.jumlah += 1;
                        existing.subtotal = existing.jumlah * existing.harga;
                    } else {
                        this.cart.push({
                            layanan_id: id,
                            nama: nama,
                            harga: harga,
                            satuan: satuan,
                            jumlah: 1,
                            subtotal: harga
                        });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateQty(index, delta) {
                    const newQty = this.cart[index].jumlah + delta;
                    if (newQty > 0) {
                        this.cart[index].jumlah = newQty;
                        this.updateSubtotal(index);
                    }
                },

                updateSubtotal(index) {
                    this.cart[index].subtotal = this.cart[index].jumlah * this.cart[index].harga;
                },

                // Customer Methods
                async searchCustomer() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const response = await fetch(`{{ route('pos.search-pelanggan') }}?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await response.json();
                    } catch (error) {
                        console.error('Search error:', error);
                    }
                },

                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerSearch = customer.nama;
                    this.customerResults = [];
                },

                async saveCustomer() {
                    if (!this.newCustomer.nama) {
                        Swal.fire('Error', 'Nama pelanggan wajib diisi', 'error');
                        return;
                    }
                    try {
                        const response = await fetch('{{ route('pos.store-pelanggan') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.newCustomer)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.selectCustomer(data.pelanggan);
                            this.showAddCustomerModal = false;
                            this.newCustomer = { nama: '', telepon: '', alamat: '' };
                            Swal.fire('Berhasil', 'Pelanggan baru berhasil ditambahkan', 'success');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Gagal menyimpan pelanggan', 'error');
                    }
                },

                // Checkout
                async processCheckout() {
                    if (!this.selectedCustomer) {
                        Swal.fire('Error', 'Pilih pelanggan terlebih dahulu', 'error');
                        return;
                    }
                    if (this.cart.length === 0) {
                        Swal.fire('Error', 'Keranjang masih kosong', 'error');
                        return;
                    }

                    this.isProcessing = true;

                    // Sanitize jumlahBayar: treat empty/NaN as 0
                    const jumlahBayarFinal = (isNaN(this.jumlahBayar) || !this.jumlahBayar) ? 0 : this.jumlahBayar;

                    try {
                        const payload = {
                            pelanggan_id: this.selectedCustomer.id,
                            items: this.cart.map(item => ({
                                layanan_id: item.layanan_id,
                                jumlah: item.jumlah,
                                harga_satuan: item.harga,
                                subtotal: item.subtotal
                            })),
                            total_harga: this.subtotal,
                            pembulatan: this.pembulatan,
                            total_setelah_pembulatan: this.total,
                            jumlah_dibayar: this.statusPembayaran === 'lunas' ? this.total : jumlahBayarFinal,
                            status_pembayaran: this.statusPembayaran,
                            catatan: this.catatan
                        };

                        const response = await fetch('{{ route('pos.checkout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.lastTransaction = data.transaksi;
                            this.showSuccessModal = true;
                        } else {
                            Swal.fire('Error', data.message || 'Gagal memproses transaksi', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses transaksi', 'error');
                    } finally {
                        this.isProcessing = false;
                    }
                },

                printReceipt() {
                    if (this.lastTransaction) {
                        window.open(`/transaksi/${this.lastTransaction.id}/struk`, '_blank');
                    }
                },

                resetAndClose() {
                    this.cart = [];
                    this.selectedCustomer = null;
                    this.customerSearch = '';
                    this.pembulatan = 0;
                    this.jumlahBayar = 0;
                    this.statusPembayaran = 'lunas';
                    this.catatan = '';
                    this.showSuccessModal = false;
                },

                // Helpers
                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num || 0);
                }
            }
        }
    </script>
</body>
</html>
