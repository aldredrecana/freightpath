@props(['status'])
@php
    $map = [
        'available'   => ['bg-emerald-50 text-emerald-700', 'bg-emerald-500', 'Available'],
        'on_trip'     => ['bg-blue-50 text-blue-700', 'bg-blue-500', 'On trip'],
        'off_duty'    => ['bg-gray-100 text-gray-600', 'bg-gray-400', 'Off duty'],
        'maintenance' => ['bg-amber-50 text-amber-700', 'bg-amber-500', 'Maintenance'],
        'pending'     => ['bg-gray-100 text-gray-600', 'bg-gray-400', 'Pending'],
        'accepted'    => ['bg-amber-50 text-amber-700', 'bg-amber-500', 'Accepted'],
        'en_route'    => ['bg-blue-50 text-blue-700', 'bg-blue-500', 'En route'],
        'delivered'   => ['bg-emerald-50 text-emerald-700', 'bg-emerald-500', 'Delivered'],
        'cancelled'   => ['bg-red-50 text-red-700', 'bg-red-500', 'Cancelled'],
    ];
    [$cls, $dot, $label] = $map[$status] ?? ['bg-gray-100 text-gray-600', 'bg-gray-400', ucfirst(str_replace('_', ' ', $status))];
@endphp
<span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-medium {{ $cls }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}
</span>