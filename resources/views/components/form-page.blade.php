@props(['crumb', 'title', 'subtitle' => null, 'action', 'method' => 'POST', 'cancel', 'submit' => 'Save'])
<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-page-header :crumb="$crumb" :title="$title" :subtitle="$subtitle" />

            <x-card class="mt-8 p-8">
                <form method="POST" action="{{ $action }}">
                    @csrf
                    @if (strtoupper($method) !== 'POST')
                        @method($method)
                    @endif

                    {{ $slot }}

                    <div class="mt-10 flex items-center gap-3 border-t border-gray-100 pt-6">
                        <x-btn>{{ $submit }}</x-btn>
                        <x-btn variant="secondary" :href="$cancel">Cancel</x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>