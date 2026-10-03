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

            @if (session('status'))
                <div class="mt-6 rounded-lg bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif
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
                                @if (auth()->user()->hasRole('admin'))
                                    <th class="px-6 py-4 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($drivers as $driver)
                                @php
                                    $initials = collect(preg_split('/\s+/', trim($driver->user->name)))
                                        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                    $avatars = ['bg-blue-100 text-blue-700', 'bg-violet-100 text-violet-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700'];
                                    $pill = [
                                        'available' => ['bg-emerald-50 text-emerald-700', 'bg-emerald-500', 'Available'],
                                        'on_trip'   => ['bg-blue-50 text-blue-700', 'bg-blue-500', 'On trip'],
                                        'off_duty'  => ['bg-gray-100 text-gray-600', 'bg-gray-400', 'Off duty'],
                                    ][$driver->status] ?? ['bg-gray-100 text-gray-600', 'bg-gray-400', ucfirst($driver->status)];
                                @endphp
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-11 w-11 items-center justify-center rounded-full text-sm font-semibold {{ $avatars[$driver->id % 4] }}">{{ $initials }}</span>
                                            <span class="font-medium text-[#12263F]">{{ $driver->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700">
                                        {{ $driver->license_no }}
                                        <div class="text-xs text-gray-400">Expires {{ $driver->license_expiry->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700">{{ $driver->contact_no }}</td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-medium {{ $pill[0] }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $pill[1] }}"></span>{{ $pill[2] }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->hasRole('admin'))
                                        <td class="px-6 py-5">
                                            <div class="flex items-center justify-end gap-4">
                                                <a href="{{ route('drivers.edit', $driver) }}" class="text-blue-600 hover:underline">Edit</a>
                                                <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Delete this driver?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No drivers found.</td>
                                </tr>
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