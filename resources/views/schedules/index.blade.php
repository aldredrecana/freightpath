<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Schedules</h2>
            <a href="{{ route('schedules.create') }}" class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md">New Schedule</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Ref</th>
                            <th class="px-4 py-3">Driver</th>
                            <th class="px-4 py-3">Truck</th>
                            <th class="px-4 py-3">Route</th>
                            <th class="px-4 py-3">Departure</th>
                            <th class="px-4 py-3">Expected arrival</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr class="border-t">
                                <td class="px-4 py-3">#{{ $schedule->id }}</td>
                                <td class="px-4 py-3">{{ $schedule->driver->user->name }}</td>
                                <td class="px-4 py-3">{{ $schedule->truck->plate_no }}</td>
                                <td class="px-4 py-3">{{ $schedule->origin_address }} &rarr; {{ $schedule->destination_address }}</td>
                                <td class="px-4 py-3">{{ $schedule->departure_at->format('M d, Y g:i A') }}</td>
                                <td class="px-4 py-3">{{ $schedule->expected_arrival_at->format('M d, Y g:i A') }}</td>
                                <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $schedule->status)) }}</td>
                                <td class="px-4 py-3 flex gap-3">
                                    <a href="{{ route('schedules.show', $schedule) }}" class="text-gray-700 underline">View</a>
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="text-indigo-600 underline">Edit</a>
                                    <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete this schedule?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">No schedules yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $schedules->links() }}</div>
        </div>
    </div>
</x-app-layout>