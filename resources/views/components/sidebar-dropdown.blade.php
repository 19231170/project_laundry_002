@props(['title', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mb-2">
    <!-- Dropdown Header -->
    <div 
        x-on:click="open = !open"
        class="sidebar-dropdown-title"
    >
        <div>
            {{ $title }}
        </div>
        <div>
            <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 text-gray-500 transform transition-transform duration-200"
                :class="{'rotate-180': open}"
                viewBox="0 0 20 20" 
                fill="currentColor"
            >
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Dropdown Content -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95"
        class="pl-4 mt-1 space-y-1"
    >
        {{ $slot }}
    </div>
</div>
