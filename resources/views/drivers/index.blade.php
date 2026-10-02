<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Drivers</h2>
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('drivers.create') }}" class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md">Add Driver</a>
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
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">License No.</th>
                            <th class="px-4 py-3">Expiry</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Status</th>
                            @if (auth()->user()->hasRole('admin'))
                                <th class="px-4 py-3">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $driver->user->name }}</td>
                                <td class="px-4 py-3">{{ $driver->license_no }}</td>
                                <td class="px-4 py-3">{{ $driver->license_expiry->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $driver->contact_no }}</td>
                                <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $driver->status)) }}</td>
                                @if (auth()->user()->hasRole('admin'))
                                    <td class="px-4 py-3 flex gap-3">
                                        <a href="{{ route('drivers.edit', $driver) }}" class="text-indigo-600 underline">Edit</a>
                                        <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Delete this driver?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 underline">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No drivers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $drivers->links() }}</div>
        </div>
    </div>
</x-app-layout>