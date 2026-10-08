<x-app-layout>
    @php
        $groups = [
            ['Drivers', [
                ['Available', $drivers->get('available', 0), route('drivers.index', ['status' => 'available'])],
                ['On trip', $drivers->get('on_trip', 0), route('drivers.index', ['status' => 'on_trip'])],
                ['Off duty', $drivers->get('off_duty', 0), route('drivers.index', ['status' => 'off_duty'])],
            ]],
            ['Trucks', [
                ['Available', $trucks->get('available', 0), route('trucks.index', ['status' => 'available'])],
                ['On trip', $trucks->get('on_trip', 0), route('trucks.index', ['status' => 'on_trip'])],
                ['Maintenance', $trucks->get('maintenance', 0), route('trucks.index', ['status' => 'maintenance'])],
            ]],
            ['Trips', [
                ['Scheduled', $tripCounts['Scheduled'], route('schedules.index')],
                ['Ongoing', $tripCounts['Ongoing'], route('schedules.index', ['status' => 'en_route'])],
                ['Completed', $tripCounts['Completed'], route('transactions.index', ['status' => 'delivered'])],
            ]],
        ];

        $alertItems = collect([
            [$alerts['reassign'], 'trip(s) need a new driver', route('schedules.index', ['status' => 'needs_reassignment']), 'border-red-200 bg-red-50 text-red-800'],
            [$alerts['urgent'], 'trip(s) urgent or overdue', route('schedules.index'), 'border-amber-200 bg-amber-50 text-amber-800'],
            [$alerts['licenses'], 'license(s) expiring or expired', route('drivers.index'), 'border-amber-200 bg-amber-50 text-amber-800'],
        ])->filter(fn ($a) => $a[0] > 0);
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <x-page-header crumb="Dashboard" title="Dashboard" :subtitle="now()->format('l, F j, Y')" />

            @if ($alertItems->isEmpty())
                <div class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    All clear. Nothing needs your attention right now.
                </div>
            @else
                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach ($alertItems as [$n, $label, $href, $cls])
                        <a href="{{ $href }}" class="block rounded-2xl border p-4 text-sm font-medium hover:shadow-sm {{ $cls }}">
                            <span class="text-2xl font-bold">{{ $n }}</span> {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($groups as [$title, $items])
                    <x-card class="p-6">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">{{ $title }}</h2>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            @foreach ($items as [$label, $count, $href])
                                <a href="{{ $href }}" class="rounded-lg p-2 hover:bg-gray-50">
                                    <div class="text-3xl font-bold text-[#12263F]">{{ $count }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $label }}</div>
                                </a>
                            @endforeach
                        </div>
                    </x-card>
                @endforeach
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <x-card class="lg:col-span-2">
                    <div class="flex items-center justify-between px-6 pt-6">
                        <h2 class="text-lg font-semibold text-[#12263F]">Today's trips</h2>
                        <a href="{{ route('schedules.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="mt-3 divide-y divide-gray-100 text-sm">
                        @forelse ($today as $trip)
                            <a href="{{ route('schedules.show', $trip) }}" class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50/60">
                                <div>
                                    <div class="font-medium text-[#12263F]">
                                        {{ $trip->departure_at->format('g:i A') }} &middot; {{ $trip->reference }} &middot; {{ $trip->driver->user->name }}
                                    </div>
                                    <div class="text-gray-500">{{ $trip->origin_address }} &rarr; {{ $trip->destination_address }}</div>
                                </div>
                                <x-status-pill :status="$trip->status" />
                            </a>
                        @empty
                            <x-empty-state title="No trips today" message="Assign a trip to get the day started."
                                action-label="Assign trip" :action-href="route('schedules.create')" />
                        @endforelse
                    </div>
                </x-card>

                <x-card>
                    <h2 class="px-6 pt-6 text-lg font-semibold text-[#12263F]">Licenses to renew</h2>
                    <div class="mt-3 divide-y divide-gray-100 text-sm">
                        @forelse ($expiring as $d)
                            <a href="{{ route('drivers.show', $d) }}" class="block px-6 py-4 hover:bg-gray-50/60">
                                <div class="font-medium text-[#12263F]">{{ $d->user->name }}</div>
                                <div class="text-gray-500">
                                    Expires {{ $d->license_expiry->format('M d, Y') }}
                                    <span class="ml-1 rounded px-1.5 py-0.5 text-[10px] font-bold uppercase text-white {{ $d->license_alert === 'expired' ? 'bg-red-600' : 'bg-amber-500' }}">{{ $d->license_alert === 'expired' ? 'Expired' : 'Expiring' }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="px-6 py-6 text-gray-500">No licenses expire in the next 30 days.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>