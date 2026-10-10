@props(['name', 'seed' => 0, 'size' => 'h-11 w-11 text-sm'])
@php
    $initials = collect(preg_split('/\s+/', trim($name)))
        ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
    $palette = [
        'bg-blue-100 text-blue-700',
        'bg-violet-100 text-violet-700',
        'bg-emerald-100 text-emerald-700',
        'bg-amber-100 text-amber-700',
    ];
@endphp
<span
    class="flex {{ $size }} shrink-0 items-center justify-center rounded-full font-semibold {{ $palette[$seed % 4] }}">{{ $initials }}</span>
