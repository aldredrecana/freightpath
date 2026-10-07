<x-app-layout>
    <div class="pb-28 pt-6">
        <div class="mx-auto max-w-2xl space-y-5 px-4">

            <p class="text-3xl font-bold text-gray-900">Hello, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}</p>

            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-base font-semibold uppercase tracking-wide text-gray-600">Your current trip</p>
                @if ($current)
                    <p class="mt-3 text-xl font-semibold text-gray-900">{{ $current->origin_address }} &rarr; {{ $current->destination_address }}</p>
                    <p class="mt-1 text-lg text-gray-700">{{ $current->departure_at->calendar() }} &middot; {{ $current->truck->plate_no }}</p>
                    <p class="mt-1 text-lg font-semibold text-blue-800">{{ $current->status === 'en_route' ? 'On the way' : 'Accepted' }}</p>
                    <a href="{{ route('schedules.show', $current) }}" class="mt-5 block w-full rounded-2xl bg-blue-700 py-5 text-center text-xl font-bold text-white">Open trip</a>
                @else
                    <p class="mt-3 text-xl text-gray-700">No active trip right now.</p>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-xl font-bold text-gray-900">New trips for you</p>
                @forelse ($upcoming as $trip)
                    <a href="{{ route('schedules.show', $trip) }}" class="mt-4 block rounded-2xl border-2 border-gray-200 p-4">
                        <p class="text-lg font-semibold text-gray-900">{{ $trip->origin_address }} &rarr; {{ $trip->destination_address }}</p>
                        <p class="text-base text-gray-700">{{ $trip->departure_at->calendar() }}</p>
                        <p class="mt-1 text-base font-semibold text-blue-800">Waiting for you to accept</p>
                    </a>
                @empty
                    <p class="mt-3 text-lg text-gray-600">No new trips.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>