@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => true])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}@if ($required)<span class="text-red-500"> *</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required)
        {{ $attributes->except('class') }}
        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error($name) border-red-400 @enderror">
    @if ($hint)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>