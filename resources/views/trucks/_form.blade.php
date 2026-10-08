@php $isEdit = isset($truck); @endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <x-form-field name="plate_no" label="Plate number" :value="$isEdit ? $truck->plate_no : null" hint="Example: ABC 1234" />
    <x-form-field name="model" label="Model" :value="$isEdit ? $truck->model : null" />
    <x-form-field name="capacity_kg" label="Capacity (kg)" type="number" min="1" :value="$isEdit ? $truck->capacity_kg : null" />
    <x-form-select name="status" label="Status"
        :options="['available' => 'Available', 'on_trip' => 'On trip', 'maintenance' => 'Maintenance']"
        :value="$isEdit ? $truck->status : 'available'" />
</div>