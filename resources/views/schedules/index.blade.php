<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                Workspace <span class="mx-2">/</span> <span class="text-gray-500">Schedules</span>
            </p>

            <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-[#12263F]">Trip schedules</h1>
                    <p class="mt-2 text-gray-500">Active trips only. Finished trips move to Trip history.</p>
                </div>
                <a href="{{ route('schedules.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Assign Trip
                </a>
            </div>

           
            @if ($errors->any())
                <div class="mt-6 rounded-lg bg-red-100 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm">
                <form method="GET" action="{{ route('schedules.index') }}" class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="relative w-full max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search trips, drivers, or destinations..."
                               class="w-full rounded-lg border border-gray-200 py-3 pl-11 pr-4 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-4">
                        @if ($q !== '' || $status)
                            <a href="{{ route('schedules.index') }}" class="text-sm text-gray-500 underline">Clear</a>
                        @endif
                        <select name="status" onchange="this.form.submit()"
                                class="rounded-lg border border-gray-200 py-3 pl-4 pr-10 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All active trips</option>
                              @foreach (['needs_reassignment' => 'Needs reassignment', 'pending' => 'Pending', 'accepted' => 'Accepted', 'en_route' => 'En route'] as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="overflow-x-auto border-t border-gray-100">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-6 py-4">Trip ID</th>
                                <th class="px-6 py-4">Driver</th>
                                <th class="px-6 py-4">Origin</th>
                                <th class="px-6 py-4">Destination</th>
                                <th class="px-6 py-4">Scheduled time</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4"><span class="sr-only">Open</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($schedules as $schedule)
                                <tr class="cursor-pointer hover:bg-gray-50/60" onclick="window.location='{{ route('schedules.show', $schedule) }}'">
                                    <td class="px-6 py-5 font-medium text-[#12263F]">{{ $schedule->reference }}</td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <x-avatar :name="$schedule->driver->user->name" :seed="$schedule->driver_id" size="h-10 w-10 text-xs" />
                                            <div>
                                                <div class="font-medium text-[#12263F]">{{ $schedule->driver->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $schedule->truck->plate_no }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700">{{ $schedule->origin_address }}</td>
                                    <td class="px-6 py-5 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6-5.2-6-10a6 6 0 1 1 12 0c0 4.8-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
                                            {{ $schedule->destination_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700">
                                        {{ $schedule->departure_at->format('g:i A') }}
                                        <div class="text-xs text-gray-400">{{ $schedule->departure_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        
                                        <x-status-pill :status="$schedule->status" />
                                            @if ($schedule->urgency)
                                            <span class="ml-2 rounded px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white {{ $schedule->urgency === 'overdue' ? 'bg-red-600' : 'bg-amber-500' }}">{{ $schedule->urgency }}</span>
                                            @endif
                                            
                                            @if ($schedule->awaiting_since)
                                            <div class="mt-1 text-xs {{ $schedule->awaiting_since->lt(now()->subMinutes((int) config('freightpath.response_window_minutes', 30))) ? 'text-red-600' : 'text-gray-400' }}">
                                                Awaiting driver &middot; {{ $schedule->awaiting_since->diffForHumans(null, true) }}
                                            </div>
                                            @endif
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('schedules.show', $schedule) }}" class="inline-flex text-gray-400 hover:text-gray-700" aria-label="Open trip {{ $schedule->reference }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">No trips found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($schedules->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">{{ $schedules->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>