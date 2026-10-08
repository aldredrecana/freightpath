<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                Workspace <span class="mx-2">/</span> <span class="text-gray-500">Drivers</span>
            </p>

            <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-[#12263F]">Driver management</h1>
                    <p class="mt-2 text-gray-500">Your people behind the wheel. Keep every driver connected.</p>
                </div>
                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('drivers.create') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        Add Driver
                    </a>
                @endif
            </div>

            @php
                $stats = [
                    ['Total drivers', $counts->sum(), null],
                    ['Available', $counts->get('available', 0), 'bg-emerald-500'],
                    ['On trip', $counts->get('on_trip', 0), 'bg-blue-500'],
                    ['Off duty', $counts->get('off_duty', 0), 'bg-gray-400'],
                ];
            @endphp
            <div class="mt-8 flex flex-wrap items-center gap-x-10 gap-y-3">
                @foreach ($stats as [$label, $value, $dot])
                    <div class="flex items-center gap-2">
                        @if ($dot)<span class="h-2 w-2 rounded-full {{ $dot }}"></span>@endif
                        <span class="text-3xl font-bold text-[#12263F]">{{ $value }}</span>
                        <span class="text-sm text-gray-500">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            
            @if ($errors->any())
                <div class="mt-6 rounded-lg bg-red-100 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
                <form method="GET" action="{{ route('drivers.index') }}" class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="relative w-full max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search by name, license, or phone..."
                               class="w-full rounded-lg border border-gray-200 py-3 pl-11 pr-4 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-4">
                        @if ($q !== '' || $status)
                            <a href="{{ route('drivers.index') }}" class="text-sm text-gray-500 underline">Clear</a>
                        @endif
                        <select name="status" onchange="this.form.submit()"
                                class="rounded-lg border border-gray-200 py-3 pl-4 pr-10 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All statuses</option>
                            @foreach (['available' => 'Available', 'on_trip' => 'On trip', 'off_duty' => 'Off duty'] as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

               <div class="overflow-x-auto border-t border-gray-100">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4">Driver</th>
                            <th class="px-6 py-4">License number</th>
                            <th class="px-6 py-4">Phone number</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4"><span class="sr-only">Open</span></th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($drivers as $driver)
                        <tr class="cursor-pointer hover:bg-gray-50/60" onclick="window.location='{{ route('drivers.show', $driver) }}'">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$driver->user->name" :seed="$driver->id" />
                                        <span class="font-medium text-[#12263F]">{{ $driver->user->name }}</span>
                                    </div>
                            </td>
                            
                            <td class="px-6 py-5 text-gray-700">
                                {{ $driver->license_no }}
                                  <div class="text-xs text-gray-400">
                                    Expires {{ $driver->license_expiry->format('M d, Y') }}
                                    @if ($driver->license_alert)
                                    <span class="ml-1 rounded px-1.5 py-0.5 text-[10px] font-bold uppercase text-white {{ $driver->license_alert === 'expired' ? 'bg-red-600' : 'bg-amber-500' }}">{{ $driver->license_alert === 'expired' ? 'Expired' : 'Expiring' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-gray-700">{{ $driver->contact_no }}</td>
                            <td class="px-6 py-5"><x-status-pill :status="$driver->status" /></td>
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('drivers.show', $driver) }}" class="inline-flex text-gray-400 hover:text-gray-700" aria-label="Open {{ $driver->user->name }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                            
                            @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No drivers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($drivers->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">{{ $drivers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>