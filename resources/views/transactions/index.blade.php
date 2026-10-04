<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                Workspace <span class="mx-2">/</span> <span class="text-gray-500">Transactions</span>
            </p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-[#12263F]">Trip history</h1>
            <p class="mt-2 text-gray-500">Every milestone, in one place. Follow the journey from start to finish.</p>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-12">

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:col-span-4">
                    <div class="p-6 pb-4">
                        <h2 class="text-lg font-semibold text-[#12263F]">Trip records</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $trips->total() }} {{ \Illuminate\Support\Str::plural('journey', $trips->total()) }}, fully documented.
                        </p>

                        <form method="GET" action="{{ route('transactions.index') }}" class="mt-4 space-y-3">
                            <input type="text" name="q" value="{{ $q }}" placeholder="Search trips, drivers, places..."
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                            <div class="flex items-center gap-3">
                                <select name="status" onchange="this.form.submit()"
                                        class="flex-1 rounded-lg border border-gray-200 py-2.5 pl-3 pr-8 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All finished</option>
                                    <option value="delivered" @selected($status === 'delivered')>Delivered</option>
                                    <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                                </select>
                                @if ($q !== '' || $status)
                                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 underline">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-100 border-t border-gray-100">
                        @forelse ($trips as $trip)
                            @php $active = $selected && $selected->id === $trip->id; @endphp
                            <a href="{{ route('transactions.index', array_merge(request()->query(), ['trip' => $trip->id])) }}"
                               class="block border-l-4 px-6 py-5 {{ $active ? 'border-blue-600 bg-blue-50/60' : 'border-transparent hover:bg-gray-50/60' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-semibold text-[#12263F]">{{ $trip->reference }}</span>
                                    <x-status-pill :status="$trip->status" />
                                </div>
                                <div class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                                    <span class="truncate">{{ $trip->origin_address }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    <span class="truncate">{{ $trip->destination_address }}</span>
                                </div>
                                <div class="mt-2 text-sm text-gray-400">{{ $trip->driver->user->name }}</div>
                            </a>
                        @empty
                            <p class="px-6 py-10 text-center text-sm text-gray-500">No finished trips yet.</p>
                        @endforelse
                    </div>

                    @if ($trips->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">{{ $trips->links() }}</div>
                    @endif
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:col-span-8">
                    @if ($selected)
                        @php
                            $name = $selected->driver->user->name;
                            $logs = $selected->statusLogs->reverse()->values();
                            $finishedAt = $logs->last()?->logged_at;
                            $labels = [
                                'pending'   => 'Trip assigned',
                                'accepted'  => 'Trip accepted',
                                'en_route'  => 'En route',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Trip cancelled',
                                'needs_reassignment' => 'Driver declined',
                            ];
                            $defaults = [
                                'pending'   => 'Assigned to ' . $name,
                                'accepted'  => $name . ' accepted the trip',
                                'en_route'  => 'Departed from ' . $selected->origin_address,
                                'delivered' => 'Delivered to ' . $selected->destination_address,
                                'cancelled' => 'The trip was cancelled',
                                'needs_reassignment' => 'The driver could not take the trip',
                            ];
                        @endphp

                        <div class="flex items-start justify-between gap-4 p-8">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Trip journey</p>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <h2 class="text-3xl font-bold tracking-tight text-[#12263F]">{{ $selected->reference }}</h2>
                                    <x-status-pill :status="$selected->status" />
                                </div>
                                <p class="mt-3 text-gray-500">{{ $selected->origin_address }} &rarr; {{ $selected->destination_address }}</p>
                            </div>
                            <x-avatar :name="$name" :seed="$selected->driver_id" size="h-14 w-14 text-base" />
                        </div>

                        <div class="grid grid-cols-2 gap-6 border-y border-gray-100 bg-gray-50/70 px-8 py-6 text-sm md:grid-cols-4">
                            <div><div class="text-xs uppercase tracking-wider text-gray-400">Driver</div><div class="mt-1 font-medium text-[#12263F]">{{ $name }}</div></div>
                            <div><div class="text-xs uppercase tracking-wider text-gray-400">Vehicle</div><div class="mt-1 font-medium text-[#12263F]">{{ $selected->truck->plate_no }}</div></div>
                            <div><div class="text-xs uppercase tracking-wider text-gray-400">Date</div><div class="mt-1 font-medium text-[#12263F]">{{ $selected->departure_at->format('F j, Y') }}</div></div>
                            <div><div class="text-xs uppercase tracking-wider text-gray-400">Finished</div><div class="mt-1 font-medium text-[#12263F]">{{ $finishedAt ? $finishedAt->format('M d, g:i A') : '-' }}</div></div>
                        </div>

                        <ol class="px-8 py-10">
                            @forelse ($logs as $log)
                                @php
                                    $isLast = $loop->last;
                                    $failed = $isLast && $log->status === 'cancelled';
                                    $done = $isLast && $log->status === 'delivered';
                                    $circle = $failed ? 'bg-red-500 text-white border-red-500'
                                            : ($done ? 'bg-emerald-500 text-white border-emerald-500'
                                            : 'bg-blue-50 text-blue-600 border-blue-200');
                                    $remark = ($log->remarks && $log->remarks !== 'Schedule created.') ? $log->remarks : null;
                                @endphp
                                <li class="relative pl-20 {{ $isLast ? '' : 'pb-10' }}">
                                    @unless ($isLast)
                                        <span class="absolute left-6 top-12 bottom-0 w-px bg-blue-200"></span>
                                    @endunless
                                    <span class="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-full border {{ $circle }}">
                                        @if ($failed)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>
                                        @endif
                                    </span>

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-lg font-semibold text-[#12263F]">{{ $labels[$log->status] ?? ucfirst(str_replace('_', ' ', $log->status)) }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ $defaults[$log->status] ?? '' }}</p>
                                            @if ($remark)
                                                <p class="mt-1 text-sm italic text-gray-600">"{{ $remark }}"</p>
                                            @endif
                                            <p class="mt-1 text-xs text-gray-400">Logged by {{ $log->loggedBy->name }}</p>
                                        </div>
                                        <span class="shrink-0 text-sm text-gray-400">{{ $log->logged_at->format('M d, g:i A') }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500">No history was recorded for this trip.</li>
                            @endforelse
                        </ol>
                    @else
                        <div class="p-16 text-center text-gray-500">
                            <p class="text-lg font-semibold text-[#12263F]">No finished trips yet</p>
                            <p class="mt-2 text-sm">Delivered and cancelled trips appear here with their full timeline.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>