@props(['href', 'active' => false, 'icon' => 'grid'])

<a href="{{ $href }}"
   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-white/10 text-white' : 'text-blue-100/80 hover:bg-white/5 hover:text-white' }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        @switch($icon)
            @case('users')
                <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
                @break
            @case('truck')
                <path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                @break
            @case('calendar')
                <rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M8 3v4M16 3v4"/>
                @break
            @case('package')
                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5"/>
                @break
            @default
                <rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>
        @endswitch
    </svg>
    <span>{{ $slot }}</span>
</a>