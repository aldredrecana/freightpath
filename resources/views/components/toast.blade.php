@if (session('status') &&
        !auth()->user()?->hasRole('driver') &&
        !in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent'], true))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4500)" x-show="show" x-transition.opacity.duration.300ms
        style="display: none;" role="status" class="fixed right-4 top-24 z-50 w-full max-w-sm sm:right-6">
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-white p-4 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path d="M8 12l3 3 5-6" />
            </svg>
            <p class="flex-1 text-sm font-medium text-gray-800">{{ session('status') }}</p>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600"
                aria-label="Close">&times;</button>
        </div>
    </div>
@endif
