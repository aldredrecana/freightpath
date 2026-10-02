@php $isEdit = isset($truck); @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="plate_no" value="Plate number" />
        <x-text-input id="plate_no" name="plate_no" class="block mt-1 w-full" :value="old('plate_no', $isEdit ? $truck->plate_no : '')" required />
        <x-input-error :messages="$errors->get('plate_no')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="model" value="Model" />
        <x-text-input id="model" name="model" class="block mt-1 w-full" :value="old('model', $isEdit ? $truck->model : '')" required />
        <x-input-error :messages="$errors->get('model')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="capacity_kg" value="Capacity (kg)" />
        <x-text-input id="capacity_kg" name="capacity_kg" type="number" min="1" class="block mt-1 w-full" :value="old('capacity_kg', $isEdit ? $truck->capacity_kg : '')" required />
        <x-input-error :messages="$errors->get('capacity_kg')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['available' => 'Available', 'on_trip' => 'On trip', 'maintenance' => 'Maintenance'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $isEdit ? $truck->status : 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>