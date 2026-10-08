@props(['title', 'message' => null, 'actionLabel' => null, 'actionHref' => null])
<div class="px-6 py-14 text-center">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 13l2-7h12l2 7v6H4z"/><path d="M4 13h5l1 2h4l1-2h5"/></svg>
    </div>
    <p class="mt-4 text-lg font-semibold text-[#12263F]">{{ $title }}</p>
    @if ($message)
        <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
    @endif
    @if ($actionLabel && $actionHref)
        <div class="mt-6"><x-btn :href="$actionHref">{{ $actionLabel }}</x-btn></div>
    @endif
</div>