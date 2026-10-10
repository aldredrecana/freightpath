<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                Workspace <span class="mx-2">/</span>
                <a href="{{ route('trucks.index') }}" class="hover:underline">Trucks</a>
                <span class="mx-2">/</span> <span class="text-gray-500">{{ $truck->plate_no }}</span>
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg bg-red-100 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-[#12263F]">{{ $truck->plate_no }}</h1>
                    <div class="mt-2"><x-status-pill :status="$truck->status" /></div>
                </div>
                <div class="flex items-center gap-4">
                    <x-btn variant="secondary" :href="route('trucks.index')">Back</x-btn>
                    @if (auth()->user()->hasRole('admin'))
                        <x-btn :href="route('trucks.edit', $truck)">Edit truck</x-btn>
                        <x-confirm-delete :action="route('trucks.destroy', $truck)" title="Delete this truck?" :message="'Truck ' . $truck->plate_no . ' will be removed. This cannot be undone.'" />
                    @endif
                </div>
            </div>

            <x-card class="mt-8 grid grid-cols-1 gap-4 p-6 text-sm md:grid-cols-2">
                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-400">Model</div>
                    <div class="mt-1 text-gray-800">{{ $truck->model }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-400">Capacity</div>
                    <div class="mt-1 text-gray-800">{{ number_format($truck->capacity_kg) }} kg</div>
                </div>
            </x-card>

            <x-card class="mt-6">
                <h2 class="px-6 pt-6 text-lg font-semibold text-[#12263F]">Recent trips</h2>
                <div class="mt-3 divide-y divide-gray-100 text-sm">
                    @forelse ($trips as $trip)
                        <a href="{{ route('schedules.show', $trip) }}"
                            class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50/60">
                            <div>
                                <div class="font-medium text-[#12263F]">{{ $trip->reference }} &middot;
                                    {{ $trip->driver->user->name }}</div>
                                <div class="text-gray-500">{{ $trip->origin_address }} &rarr;
                                    {{ $trip->destination_address }}</div>
                            </div>
                            <x-status-pill :status="$trip->status" />
                        </a>
                    @empty
                        <p class="px-6 py-6 text-gray-500">No trips yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
