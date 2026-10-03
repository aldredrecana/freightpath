<x-slot name="header">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Delivery {{ $schedule->reference }}</h2>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ auth()->user()->hasRole('driver') ? route('schedules.mine') : route('schedules.index') }}" class="text-gray-600 underline">Back</a>
            @if (! auth()->user()->hasRole('driver'))
                <a href="{{ route('schedules.edit', $schedule) }}" class="text-blue-600 underline">Edit</a>
                <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete this schedule?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 underline">Delete</button>
                </form>
            @endif
        </div>
    </div>
</x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            @if ($errors->has('delete'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ $errors->first('delete') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Driver:</span> {{ $schedule->driver->user->name }}</div>
                <div><span class="text-gray-500">Truck:</span> {{ $schedule->truck->plate_no }} - {{ $schedule->truck->model }}</div>
                <div><span class="text-gray-500">Origin:</span> {{ $schedule->origin_address }}</div>
                <div><span class="text-gray-500">Destination:</span> {{ $schedule->destination_address }}</div>
                <div><span class="text-gray-500">Departure:</span> {{ $schedule->departure_at->format('M d, Y g:i A') }}</div>
                <div><span class="text-gray-500">Expected arrival:</span> {{ $schedule->expected_arrival_at->format('M d, Y g:i A') }}</div>
                <div class="md:col-span-2"><span class="text-gray-500">Current status:</span>
                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $schedule->status)) }}</span>
                </div>
            </div>

            @if (count($nextStatuses))
            <form method="POST" action="{{ route('schedules.status', $schedule) }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                @csrf
                <h3 class="font-semibold text-gray-800 mb-4">Update delivery status</h3>
                <x-input-label for="remarks" value="Remarks (optional)" />
                <x-text-input id="remarks" name="remarks" class="block mt-1 w-full" :value="old('remarks')" />
                <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                
                <div class="mt-4 flex gap-3">
                    @foreach ($nextStatuses as $s)
                     <button type="submit" name="status" value="{{ $s }}"
                       class="px-4 py-2 rounded-md text-xs uppercase text-white {{ $s === 'cancelled' ? 'bg-red-600' : 'bg-gray-800' }}">
                       {{ ['accepted' => 'Accept Trip', 'en_route' => 'Depart / En Route', 'delivered' => 'Mark Delivered', 'cancelled' => 'Cancel Trip'][$s] }}
                </button>
            @endforeach
        </div>
    </form>
@endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Delivery timeline</h3>
                <ol class="relative border-l border-gray-300 ml-3">
                    @forelse ($schedule->statusLogs as $log)
                        <li class="mb-6 ml-6">
                            <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full {{ $loop->first ? 'bg-indigo-600' : 'bg-gray-400' }}"></span>
                            <p class="font-semibold text-sm {{ $loop->first ? 'text-indigo-700' : 'text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $log->logged_at->format('M d, Y g:i A') }} &middot; {{ $log->loggedBy->name }}</p>
                            @if ($log->remarks)
                                <p class="text-sm text-gray-600 mt-1">{{ $log->remarks }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="ml-6 text-sm text-gray-500">No history yet.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</x-app-layout>