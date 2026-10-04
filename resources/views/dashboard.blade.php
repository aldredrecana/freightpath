<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-gray-600">Welcome, {{ auth()->user()->name }}.</p>

            @if ($mode === 'driver')

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Current trip</h3>
                    @if ($current)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><span class="text-gray-500">Ref:</span> #{{ $current->id }}</div>
                            <div><span class="text-gray-500">Status:</span> <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $current->status)) }}</span></div>
                            <div><span class="text-gray-500">Truck:</span> {{ $current->truck->plate_no }}</div>
                            <div><span class="text-gray-500">Departure:</span> {{ $current->departure_at->format('M d, Y g:i A') }}</div>
                            <div class="md:col-span-2"><span class="text-gray-500">Route:</span> {{ $current->origin_address }} &rarr; {{ $current->destination_address }}</div>
                        </div>
                        <a href="{{ route('schedules.show', $current) }}" class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md">Open trip</a>
                    @else
                        <p class="text-sm text-gray-500">No active trip.</p>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Next trips</h3>
                        <a href="{{ route('schedules.mine') }}" class="text-sm text-indigo-600 underline">All my deliveries</a>
                    </div>
                    @forelse ($upcoming as $trip)
                        <div class="flex justify-between items-center border-t py-3 text-sm">
                            <div>
                                <div class="font-semibold">#{{ $trip->id }} &middot; {{ $trip->departure_at->format('M d, Y g:i A') }}</div>
                                <div class="text-gray-500">{{ $trip->origin_address }} &rarr; {{ $trip->destination_address }}</div>
                            </div>
                            <a href="{{ route('schedules.show', $trip) }}" class="text-indigo-600 underline">Open</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No upcoming trips.</p>
                    @endforelse
                </div>

            @else

            @if (($alerts['reassign'] ?? 0) > 0 || ($alerts['urgent'] ?? 0) > 0)
            <a href="{{ route('schedules.index') }}" class="block rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 hover:bg-red-100">
                @if ($alerts['reassign'] > 0)
                <strong>{{ $alerts['reassign'] }}</strong> {{ \Illuminate\Support\Str::plural('trip', $alerts['reassign']) }} need a new driver.
                @endif
                @if ($alerts['urgent'] > 0)
                <strong>{{ $alerts['urgent'] }}</strong> urgent or overdue.
                @endif
                View schedules &rarr;
            </a>
            @endif

                @php
                    $groups = [
                        'Drivers' => [
                            'Available' => $drivers->get('available', 0),
                            'On trip'   => $drivers->get('on_trip', 0),
                            'Off duty'  => $drivers->get('off_duty', 0),
                        ],
                        'Trucks' => [
                            'Available'   => $trucks->get('available', 0),
                            'On trip'     => $trucks->get('on_trip', 0),
                            'Maintenance' => $trucks->get('maintenance', 0),
                        ],
                        'Trips' => $tripCounts,
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($groups as $title => $items)
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-semibold text-gray-800 mb-4">{{ $title }}</h3>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                @foreach ($items as $label => $count)
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                                        <div class="text-xs text-gray-500">{{ $label }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                    <div class="flex justify-between items-center p-6 pb-3">
                        <h3 class="font-semibold text-gray-800">Latest trips</h3>
                        <a href="{{ route('schedules.index') }}" class="text-sm text-indigo-600 underline">View all</a>
                    </div>
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Ref</th>
                                <th class="px-4 py-3">Driver</th>
                                <th class="px-4 py-3">Truck</th>
                                <th class="px-4 py-3">Route</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recent as $trip)
                                <tr class="border-t">
                                    <td class="px-4 py-3">#{{ $trip->id }}</td>
                                    <td class="px-4 py-3">{{ $trip->driver->user->name }}</td>
                                    <td class="px-4 py-3">{{ $trip->truck->plate_no }}</td>
                                    <td class="px-4 py-3">{{ $trip->origin_address }} &rarr; {{ $trip->destination_address }}</td>
                                    <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</td>
                                    <td class="px-4 py-3"><a href="{{ route('schedules.show', $trip) }}" class="text-indigo-600 underline">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No trips yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
</x-app-layout>