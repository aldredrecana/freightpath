<x-app-layout>
    <div class="pb-28 pt-6">
        <div class="mx-auto max-w-2xl space-y-4 px-4">
            <h1 class="text-3xl font-bold text-gray-900">My trips</h1>

            @forelse ($schedules as $schedule)
                @php
                    $label = [
                        'pending' => ['Waiting for you to accept', 'bg-amber-100 text-amber-900'],
                        'accepted' => ['Accepted', 'bg-blue-100 text-blue-900'],
                        'en_route' => ['On the way', 'bg-blue-100 text-blue-900'],
                        'delivered' => ['Delivered', 'bg-emerald-100 text-emerald-900'],
                        'needs_reassignment' => ['You declined', 'bg-gray-200 text-gray-800'],
                        'cancelled' => ['Cancelled', 'bg-red-100 text-red-900'],
                    ][$schedule->status] ?? [ucfirst($schedule->status), 'bg-gray-200 text-gray-800'];
                @endphp
                <a href="{{ route('schedules.show', $schedule) }}" class="block rounded-2xl bg-white p-5 shadow-sm">
                    <span
                        class="inline-block rounded-full px-3 py-1 text-base font-semibold {{ $label[1] }}">{{ $label[0] }}</span>
                    <p class="mt-3 text-xl font-semibold text-gray-900">{{ $schedule->origin_address }} &rarr;
                        {{ $schedule->destination_address }}</p>
                    <p class="mt-1 text-lg text-gray-700">{{ $schedule->departure_at->calendar() }} &middot;
                        {{ $schedule->truck->plate_no }}</p>
                </a>
            @empty
                <div class="rounded-2xl bg-white p-6 text-xl text-gray-700 shadow-sm">No trips assigned to you yet.</div>
            @endforelse

            <div>{{ $schedules->links() }}</div>
        </div>
    </div>
</x-app-layout>
