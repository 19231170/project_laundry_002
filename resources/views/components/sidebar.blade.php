<div class="h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" x-data="{ isOpen: true }">
    <!-- Sidebar Toggle Button (For Mobile) -->
    <div class="sticky top-0 flex lg:hidden justify-end px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <button x-on:click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-600 focus:outline-none">
            <svg class="h-6 w-6" x-bind:class="{'hidden': !isOpen, 'block': isOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg class="h-6 w-6" x-bind:class="{'block': !isOpen, 'hidden': isOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
    
    <!-- Sidebar Content -->
    <div class="h-full overflow-y-auto" x-bind:class="{'hidden': !isOpen, 'block': isOpen}">
        <!-- Logo -->
        <div class="flex items-center justify-center p-6 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                <span class="ml-2 text-lg font-semibold text-gray-800 dark:text-gray-200">{{ config('app.name', 'Laravel') }}</span>
            </a>
        </div>
        
        <!-- Navigation Links -->
        <nav class="mt-5 px-2">
            <!-- Dashboard -->
            <div class="mb-4">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'></path></svg>'">
                    {{ __('Dashboard') }}
                </x-sidebar-link>
            </div>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
            
            <!-- Pemasukan Section -->
            <x-sidebar-dropdown title="PEMASUKAN" :active="request()->routeIs('transaksi.*') || request()->routeIs('pelanggan.*') || request()->routeIs('layanan.*')">
                <x-sidebar-link :href="route('transaksi.index')" :active="request()->routeIs('transaksi.*')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'">
                    {{ __('Transaksi') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('pelanggan.index')" :active="request()->routeIs('pelanggan.*')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'">
                    {{ __('Pelanggan') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('layanan.index')" :active="request()->routeIs('layanan.*')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'></path></svg>'">
                    {{ __('Layanan') }}
                </x-sidebar-link>
            </x-sidebar-dropdown>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
            
            <!-- Pengeluaran Section -->
            <x-sidebar-dropdown title="PENGELUARAN" :active="request()->is('pengeluaran*')">
                <x-sidebar-link :href="route('pengeluaran')" :active="request()->is('pengeluaran') && !request()->is('pengeluaran/*')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z\'></path></svg>'">
                    {{ __('Pengeluaran') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('pengeluaran.kategori')" :active="request()->routeIs('pengeluaran.kategori')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z\'></path></svg>'">
                    {{ __('Kategori') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('pengeluaran.supplier')" :active="request()->routeIs('pengeluaran.supplier')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'">
                    {{ __('Supplier') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('pengeluaran.inventaris')" :active="request()->routeIs('pengeluaran.inventaris')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>'">
                    {{ __('Inventaris') }}
                </x-sidebar-link>
            </x-sidebar-dropdown>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
            
            <!-- Laporan Section -->
            <x-sidebar-dropdown title="LAPORAN" :active="request()->routeIs('laporan.*')">
                <x-sidebar-link :href="route('laporan.index')" :active="request()->routeIs('laporan.index')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'">
                    {{ __('Laporan Pemasukan') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('laporan.pengeluaran-kategori')" :active="request()->routeIs('laporan.pengeluaran-kategori')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'">
                    {{ __('Laporan Pengeluaran') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('laporan.laba-rugi')" :active="request()->routeIs('laporan.laba-rugi')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path></svg>'">
                    {{ __('Laba Rugi') }}
                </x-sidebar-link>
                
                <x-sidebar-link :href="route('laporan.pembulatan')" :active="request()->routeIs('laporan.pembulatan')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'">
                    {{ __('Pembulatan') }}
                </x-sidebar-link>
            </x-sidebar-dropdown>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
            
            <!-- Pengaturan Section -->
            <div class="mb-4">
                <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\'></path><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 12a3 3 0 11-6 0 3 3 0 016 0z\'></path></svg>'">
                    {{ __('PENGATURAN') }}
                </x-sidebar-link>
            </div>
        </nav>
    </div>
</div>
