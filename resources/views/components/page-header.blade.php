@props(['crumb', 'title', 'subtitle' => null])
<div>
    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
        Workspace <span class="mx-2">/</span> <span class="text-gray-500">{{ $crumb }}</span>
    </p>
    <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold tracking-tight text-[#12263F]">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($actions)
            <div class="flex items-center gap-3">{{ $actions }}</div>
        @endisset
    </div>
</div>