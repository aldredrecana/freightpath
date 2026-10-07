<x-app-layout>
    @php
        $steps = ['Assigned', 'Accepted', 'On the way', 'Delivered'];
        $stepIndex = ['pending' => 0, 'accepted' => 1, 'en_route' => 2, 'delivered' => 3][$schedule->status] ?? null;
        $allDone = $schedule->status === 'delivered';

        $messages = [
            'pending'            => 'New trip. Please accept it.',
            'accepted'           => 'You accepted this trip. Start it when you leave.',
            'en_route'           => 'You are on the way.',
            'delivered'          => 'Delivered. Thank you!',
            'needs_reassignment' => 'You declined this trip. The dispatcher will assign another driver.',
            'cancelled'          => 'This trip was cancelled.',
        ];

        $actions = [
            'accepted'  => ['Accept trip', 'Accept this trip?', 'Yes, accept', 'bg-blue-700 hover:bg-blue-800'],
            'en_route'  => ['Start trip', 'Start this trip now?', 'Yes, start trip', 'bg-blue-700 hover:bg-blue-800'],
            'delivered' => ['Delivered', 'Did you deliver everything?', 'Yes, delivered', 'bg-emerald-700 hover:bg-emerald-800'],
        ];

        $next = $nextStatuses[0] ?? null;
        $dispatcherPhone = config('freightpath.dispatcher_phone');
    @endphp

    <div class="pb-28 pt-6">
        <div class="mx-auto max-w-2xl space-y-5 px-4">

            <a href="{{ route('schedules.mine') }}" class="inline-block text-lg font-semibold text-blue-700 underline">&larr; My trips</a>

            @if (session('status'))
                <div class="rounded-2xl bg-emerald-100 p-5 text-xl font-semibold text-emerald-900">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl bg-red-100 p-5 text-lg text-red-900">{{ $errors->first() }}</div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-base font-semibold uppercase tracking-wide text-gray-600">Trip {{ $schedule->reference }}</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $messages[$schedule->status] ?? '' }}</p>

                @if ($stepIndex !== null)
                    <ol class="mt-6 grid grid-cols-4 gap-2 text-center">
                        @foreach ($steps as $i => $label)
                            @php
                                $done = $allDone || $i < $stepIndex;
                                $current = ! $allDone && $i === $stepIndex;
                            @endphp
                            <li>
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold {{ $done ? 'bg-emerald-600 text-white' : ($current ? 'bg-blue-700 text-white ring-4 ring-blue-200' : 'bg-gray-200 text-gray-600') }}">
                                    {{ $done ? '✓' : $i + 1 }}
                                </span>
                                <span class="mt-2 block text-base {{ $current ? 'font-bold text-gray-900' : 'text-gray-600' }}">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            @if ($next && isset($actions[$next]))
                @php [$btn, $ask, $yes, $color] = $actions[$next]; @endphp
                <form method="POST" action="{{ route('schedules.status', $schedule) }}" x-data="{ open: false }">
                    @csrf
                    <button type="button" @click="open = true"
                            class="w-full rounded-2xl py-6 text-2xl font-bold text-white shadow {{ $color }}">{{ $btn }}</button>

                    <div x-show="open" style="display: none;"
                         class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 p-4 sm:items-center"
                         @keydown.escape.window="open = false">
                        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                            <p class="text-2xl font-bold text-gray-900">{{ $ask }}</p>
                            <div class="mt-6 space-y-3">
                                <button type="submit" name="status" value="{{ $next }}"
                                        class="w-full rounded-2xl py-5 text-xl font-bold text-white {{ $color }}">{{ $yes }}</button>
                                <button type="button" @click="open = false"
                                        class="w-full rounded-2xl border-2 border-gray-300 py-5 text-xl font-semibold text-gray-800">Not yet</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

            <div class="space-y-4 rounded-2xl bg-white p-6 shadow-sm">
                <div>
                    <p class="text-base font-semibold text-gray-600">From</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $schedule->origin_address }}</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-600">To</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $schedule->destination_address }}</p>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($schedule->destination_address) }}"
                       target="_blank" rel="noopener" class="mt-2 inline-block text-lg font-semibold text-blue-700 underline">Open in Maps</a>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-600">Leave</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $schedule->departure_at->calendar() }}</p>
                    <p class="text-base text-gray-600">{{ $schedule->departure_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-600">Truck</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $schedule->truck->plate_no }}</p>
                </div>
            </div>

            @if ($dispatcherPhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $dispatcherPhone) }}"
                   class="flex w-full items-center justify-center rounded-2xl border-2 border-blue-700 py-5 text-xl font-bold text-blue-800">Call dispatcher</a>
            @endif

            @if (in_array($schedule->status, ['pending', 'accepted'], true))
                <form method="POST" action="{{ route('schedules.decline', $schedule) }}"
                      x-data="{ open: {{ $errors->has('reason') || $errors->has('details') ? 'true' : 'false' }} }">
                    @csrf
                    <button type="button" @click="open = true"
                            class="mx-auto block text-lg font-semibold text-gray-600 underline">I can't take this trip</button>

                    <div x-show="open" style="display: none;"
                         class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 p-4 sm:items-center"
                         @keydown.escape.window="open = false">
                        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                            <p class="text-2xl font-bold text-gray-900">Why can't you take this trip?</p>
                            <p class="mt-1 text-base text-gray-600">The dispatcher will be told right away and will find another driver.</p>
                            <div class="mt-5 space-y-4">
                                <select name="reason" required class="block w-full rounded-xl border-2 border-gray-300 py-4 text-xl">
                                    <option value="">Choose a reason</option>
                                    @foreach (\App\Models\Schedule::DECLINE_REASONS as $value => $label)
                                        <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="details" value="{{ old('details') }}" placeholder="Details (needed if you choose Other)"
                                       class="block w-full rounded-xl border-2 border-gray-300 py-4 text-xl">
                            </div>
                            <div class="mt-6 space-y-3">
                                <button type="submit" class="w-full rounded-2xl bg-red-700 py-5 text-xl font-bold text-white">Yes, I can't take it</button>
                                <button type="button" @click="open = false"
                                        class="w-full rounded-2xl border-2 border-gray-300 py-5 text-xl font-semibold text-gray-800">Go back</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-xl font-bold text-gray-900">What happened</p>
                <ul class="mt-4 space-y-4">
                    @foreach ($schedule->statusLogs as $log)
                        <li>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ ['pending' => 'Trip assigned', 'accepted' => 'Trip accepted', 'en_route' => 'Trip started', 'delivered' => 'Delivered', 'needs_reassignment' => 'Driver declined', 'cancelled' => 'Trip cancelled'][$log->status] ?? ucfirst(str_replace('_', ' ', $log->status)) }}
                            </p>
                            <p class="text-base text-gray-600">{{ $log->logged_at->calendar() }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>