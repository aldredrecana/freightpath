<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Driver</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('drivers.update', $driver) }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                @csrf
                @method('PUT')
                @include('drivers._form')
                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>Update</x-primary-button>
                    <a href="{{ route('drivers.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>