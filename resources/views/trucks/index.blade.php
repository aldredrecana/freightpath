<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <x-page-header crumb="Trucks" title="Truck management" subtitle="Know which trucks are ready, on the road, or in the shop.">
                <x-slot name="actions">
                    @if (auth()->user()->hasRole('admin'))
                        <x-btn :href="route('trucks.create')">+ Add truck</x-btn>
                    @endif
                </x-slot>
            </x-page-header>

            @php
                $stats = [
                    ['Total trucks', $counts->sum(), null],
                    ['Available', $counts->get('available', 0), 'bg-emerald-500'],
                    ['On trip', $counts->get('on_trip', 0), 'bg-blue-500'],
                    ['Maintenance', $counts->get('maintenance', 0), 'bg-amber-500'],
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

            <x-card class="mt-6">
                @if ($counts->sum() === 0)
                    <x-empty-state title="No trucks yet" message="Add your first truck so you can assign it to trips."
                        :action-label="auth()->user()->hasRole('admin') ? 'Add truck' : null"
                        :action-href="route('trucks.create')" />
                @else
                    <form method="GET" action="{{ route('trucks.index') }}" class="flex flex-wrap items-center justify-between gap-4 p-5">
                        <div class="relative w-full max-w-md">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Search by plate number or model..."
                                   class="w-full rounded-lg border border-gray-200 py-3 pl-11 pr-4 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center gap-4">
                            @if ($q !== '' || $status)
                                <a href="{{ route('trucks.index') }}" class="text-sm text-gray-500 underline">Clear</a>
                            @endif
                            <select name="status" onchange="this.form.submit()"
                                    class="rounded-lg border border-gray-200 py-3 pl-4 pr-10 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All statuses</option>
                                @foreach (['available' => 'Available', 'on_trip' => 'On trip', 'maintenance' => 'Maintenance'] as $value => $label)
                                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="overflow-x-auto border-t border-gray-100">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="px-6 py-4">Plate number</th>
                                    <th class="px-6 py-4">Model</th>
                                    <th class="px-6 py-4">Capacity</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4"><span class="sr-only">Open</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse ($trucks as $truck)
                                    <tr class="cursor-pointer hover:bg-gray-50/60" onclick="window.location='{{ route('trucks.show', $truck) }}'">
                                        <td class="px-6 py-5 font-medium text-[#12263F]">{{ $truck->plate_no }}</td>
                                        <td class="px-6 py-5 text-gray-700">{{ $truck->model }}</td>
                                        <td class="px-6 py-5 text-gray-700">{{ number_format($truck->capacity_kg) }} kg</td>
                                        <td class="px-6 py-5"><x-status-pill :status="$truck->status" /></td>
                                        <td class="px-6 py-5 text-right">
                                            <a href="{{ route('trucks.show', $truck) }}" class="inline-flex text-gray-400 hover:text-gray-700" aria-label="Open truck {{ $truck->plate_no }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No trucks match your search.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($trucks->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">{{ $trucks->links() }}</div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>