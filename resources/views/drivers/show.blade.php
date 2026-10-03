<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                Workspace <span class="mx-2">/</span>
                <a href="{{ route('drivers.index') }}" class="hover:underline">Drivers</a>
                <span class="mx-2">/</span> <span class="text-gray-500">{{ $driver->user->name }}</span>
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg bg-red-100 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <x-avatar :name="$driver->user->name" :seed="$driver->id" size="h-16 w-16 text-lg" />
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#12263F]">{{ $driver->user->name }}</h1>
                        <div class="mt-2"><x-status-pill :status="$driver->status" /></div>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('drivers.index') }}" class="text-gray-600 underline">Back</a>
                    @if (auth()->user()->hasRole('admin'))
                        <a href="{{ route('drivers.edit', $driver) }}" class="rounded-lg bg-blue-600 px-4 py-2.5 font-semibold text-white hover:bg-blue-700">Edit driver</a>
                        <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Delete this driver?')">
                            @csrf
                            @method('DELETE')
                            <button class="font-medium text-red-600 hover:underline">Delete</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 rounded-2xl border border-gray-200 bg-white p-6 text-sm shadow-sm md:grid-cols-2">
                <div><div class="text-xs uppercase tracking-wider text-gray-400">Email (login)</div><div class="mt-1 text-gray-800">{{ $driver->user->email }}</div></div>
                <div><div class="text-xs uppercase tracking-wider text-gray-400">Phone number</div><div class="mt-1 text-gray-800">{{ $driver->contact_no }}</div></div>
                <div><div class="text-xs uppercase tracking-wider text-gray-400">License number</div><div class="mt-1 text-gray-800">{{ $driver->license_no }}</div></div>
                <div><div class="text-xs uppercase tracking-wider text-gray-400">License expiry</div><div class="mt-1 text-gray-800">{{ $driver->license_expiry->format('M d, Y') }}</div></div>
                <div class="md:col-span-2"><div class="text-xs uppercase tracking-wider text-gray-400">Address</div><div class="mt-1 text-gray-800">{{ $driver->address }}</div></div>
            </div>

            <div class="mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
                <h2 class="px-6 pt-6 text-lg font-semibold text-[#12263F]">Recent trips</h2>
                <div class="mt-3 divide-y divide-gray-100 text-sm">
                    @forelse ($trips as $trip)
                        <a href="{{ route('schedules.show', $trip) }}" class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50/60">
                            <div>
                                <div class="font-medium text-[#12263F]">{{ $trip->reference }} &middot; {{ $trip->truck->plate_no }}</div>
                                <div class="text-gray-500">{{ $trip->origin_address }} &rarr; {{ $trip->destination_address }}</div>
                            </div>
                            <x-status-pill :status="$trip->status" />
                        </a>
                    @empty
                        <p class="px-6 py-6 text-gray-500">No trips yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>