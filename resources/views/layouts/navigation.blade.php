@php
    $user = auth()->user();
    $items = $user->hasRole('driver')
        ? [
            ['Dashboard', 'dashboard', 'dashboard', 'grid'],
            ['My Deliveries', 'schedules.mine', 'schedules.*', 'package'],
        ]
        : [
            ['Dashboard', 'dashboard', 'dashboard', 'grid'],
            ['Drivers', 'drivers.index', 'drivers.*', 'users'],
            ['Trucks', 'trucks.index', 'trucks.*', 'truck'],
            ['Schedules', 'schedules.index', 'schedules.*', 'calendar'],
            ['Transactions', 'transactions.index', 'transactions.*', 'history'],
        ];
    $initials = collect(preg_split('/\s+/', trim($user->name)))
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<nav x-data="{ open: false }" class="bg-[#12263F] text-white">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-8">
            <a href="{{ route('dashboard') }}"><x-brand-logo :light="true" /></a>
            <span class="hidden h-8 w-px bg-white/15 lg:block"></span>
            <div class="hidden items-center gap-1 lg:flex">
                @foreach ($items as [$label, $route, $pattern, $icon])
                    <x-nav-item :href="route($route)" :active="request()->routeIs($pattern)" :icon="$icon">{{ $label }}</x-nav-item>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Account menu">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/10 text-sm font-semibold">{{ $initials }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 text-xs text-gray-500">
                        <div class="font-semibold text-gray-700">{{ $user->name }}</div>
                        <div>{{ ucfirst($user->role?->name ?? '') }}</div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>

            <button @click="open = ! open" class="rounded-lg p-2 text-blue-100 hover:bg-white/10 lg:hidden" aria-label="Toggle menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/10 px-4 py-3 lg:hidden">
        <div class="space-y-1">
            @foreach ($items as [$label, $route, $pattern, $icon])
                <x-nav-item :href="route($route)" :active="request()->routeIs($pattern)" :icon="$icon">{{ $label }}</x-nav-item>
            @endforeach
        </div>
    </div>
</nav>

@if (auth()->user()->hasRole('driver'))
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white lg:hidden">
        <div class="grid grid-cols-2">
            @foreach ([
                ['Home', 'dashboard', 'dashboard', 'M3 12l9-9 9 9M5 10v10h14V10'],
                ['My Trips', 'schedules.mine', 'schedules.*', 'M3 6h11v10H3zM14 10h4l3 3v3h-7z'],
            ] as [$label, $route, $pattern, $icon])
                <a href="{{ route($route) }}"
                   class="flex flex-col items-center gap-1 py-3 text-base font-semibold {{ request()->routeIs($pattern) ? 'text-blue-700' : 'text-gray-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
@endif