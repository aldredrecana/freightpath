@props(['action', 'title' => 'Are you sure?', 'message' => null, 'label' => 'Delete'])
<div x-data="{ open: false }" class="inline">
    <button type="button" @click="open = true"
        class="text-sm font-semibold text-red-600 hover:underline">{{ $label }}</button>

    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="open = false">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="open = false">
            <h3 class="text-lg font-semibold text-[#12263F]">{{ $title }}</h3>
            @if ($message)
                <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>
            @endif
            <form method="POST" action="{{ $action }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <x-btn variant="secondary" type="button" @click="open = false">Cancel</x-btn>
                <x-btn variant="danger">Delete</x-btn>
            </form>
        </div>
    </div>
</div>
