@props(['light' => false])

<div class="flex items-center gap-3">
    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="6" cy="6" r="2"/>
            <circle cx="18" cy="18" r="2"/>
            <path d="M8 6h6a3 3 0 0 1 0 6H9a3 3 0 0 0 0 6h7"/>
        </svg>
    </span>
    <span class="text-2xl font-bold tracking-tight {{ $light ? 'text-white' : 'text-[#12263F]' }}">FreightPath<span class="text-amber-400">.</span></span>
</div>