<!-- Mobile Navigation Menu - This is now handled in the sidebar component -->
<nav x-data="{ open: false }" class="md:hidden bg-white border-b border-gray-100">
    <!-- Mobile Hamburger Menu -->
    <div class="px-4 py-2 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
            <span class="ml-2 font-semibold text-gray-800">{{ config('app.name', 'Laravel') }}</span>
        </div>
        
        <!-- Hamburger -->
        <div>
            <button x-on:click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden">
        <div class="pt-2 pb-3 px-4 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Pemasukan -->
        <div class="pt-2 pb-1">
            <div class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('Pemasukan') }}
            </div>
            <x-responsive-nav-link :href="route('transaksi.index')" :active="request()->routeIs('transaksi.*')">
                {{ __('Transaksi') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pelanggan.index')" :active="request()->routeIs('pelanggan.*')">
                {{ __('Pelanggan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('layanan.index')" :active="request()->routeIs('layanan.*')">
                {{ __('Layanan') }}
            </x-responsive-nav-link>
        </div>

        <!-- Pengeluaran -->
        <div class="pt-2 pb-1">
            <div class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('Pengeluaran') }}
            </div>
            <x-responsive-nav-link :href="route('pengeluaran')" :active="request()->is('pengeluaran') && !request()->is('pengeluaran/*')">
                {{ __('Pengeluaran') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pengeluaran.kategori')" :active="request()->routeIs('pengeluaran.kategori')">
                {{ __('Kategori') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pengeluaran.supplier')" :active="request()->routeIs('pengeluaran.supplier')">
                {{ __('Supplier') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pengeluaran.inventaris')" :active="request()->routeIs('pengeluaran.inventaris')">
                {{ __('Inventaris') }}
            </x-responsive-nav-link>
        </div>

        <!-- Laporan -->
        <div class="pt-2 pb-1">
            <div class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('Laporan') }}
            </div>
            <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.index')">
                {{ __('Laporan Pemasukan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('laporan.pengeluaran-kategori')" :active="request()->routeIs('laporan.pengeluaran-kategori')">
                {{ __('Laporan Pengeluaran') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('laporan.laba-rugi')" :active="request()->routeIs('laporan.laba-rugi')">
                {{ __('Laba Rugi') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('laporan.pembulatan')" :active="request()->routeIs('laporan.pembulatan')">
                {{ __('Pembulatan') }}
            </x-responsive-nav-link>
        </div>

        <!-- Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
