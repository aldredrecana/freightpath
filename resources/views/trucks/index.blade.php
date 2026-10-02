<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trucks</h2>
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('trucks.create') }}" class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md">Add Truck</a>
            @endif
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
                            <th class="px-4 py-3">Plate No.</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">Capacity (kg)</th>
                            <th class="px-4 py-3">Status</th>
                            @if (auth()->user()->hasRole('admin'))
                                <th class="px-4 py-3">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trucks as $truck)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $truck->plate_no }}</td>
                                <td class="px-4 py-3">{{ $truck->model }}</td>
                                <td class="px-4 py-3">{{ number_format($truck->capacity_kg) }}</td>
                                <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $truck->status)) }}</td>
                                @if (auth()->user()->hasRole('admin'))
                                    <td class="px-4 py-3 flex gap-3">
                                        <a href="{{ route('trucks.edit', $truck) }}" class="text-indigo-600 underline">Edit</a>
                                        <form method="POST" action="{{ route('trucks.destroy', $truck) }}" onsubmit="return confirm('Delete this truck?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 underline">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No trucks yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $trucks->links() }}</div>
        </div>
    </div>
</x-app-layout>