<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Deliveries</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Ref</th>
                            <th class="px-4 py-3">Truck</th>
                            <th class="px-4 py-3">Route</th>
                            <th class="px-4 py-3">Departure</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr class="border-t">
                                <td class="px-4 py-3">#{{ $schedule->id }}</td>
                                <td class="px-4 py-3">{{ $schedule->truck->plate_no }}</td>
                                <td class="px-4 py-3">{{ $schedule->origin_address }} &rarr; {{ $schedule->destination_address }}</td>
                                <td class="px-4 py-3">{{ $schedule->departure_at->format('M d, Y g:i A') }}</td>
                                <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $schedule->status)) }}</td>
                                <td class="px-4 py-3"><a href="{{ route('schedules.show', $schedule) }}" class="text-indigo-600 underline">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No deliveries assigned to you.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $schedules->links() }}</div>
        </div>
    </div>
</x-app-layout>