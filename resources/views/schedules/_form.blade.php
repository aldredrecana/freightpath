@php $isEdit = isset($schedule); @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="driver_id" value="Driver" />
        <select id="driver_id" name="driver_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
            <option value="">Select a driver</option>
            @foreach ($drivers as $driver)
                <option value="{{ $driver->id }}" @selected((int) old('driver_id', $isEdit ? $schedule->driver_id : null) === $driver->id)>
                    {{ $driver->user->name }} ({{ $driver->license_no }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="truck_id" value="Truck" />
        <select id="truck_id" name="truck_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
            <option value="">Select a truck</option>
            @foreach ($trucks as $truck)
                <option value="{{ $truck->id }}" @selected((int) old('truck_id', $isEdit ? $schedule->truck_id : null) === $truck->id)>
                    {{ $truck->plate_no }} - {{ $truck->model }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('truck_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="departure_at" value="Departure" />
        <x-text-input id="departure_at" name="departure_at" type="datetime-local" class="block mt-1 w-full" :value="old('departure_at', $isEdit ? $schedule->departure_at->format('Y-m-d\TH:i') : '')" required />
        <x-input-error :messages="$errors->get('departure_at')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="expected_arrival_at" value="Expected arrival" />
        <x-text-input id="expected_arrival_at" name="expected_arrival_at" type="datetime-local" class="block mt-1 w-full" :value="old('expected_arrival_at', $isEdit ? $schedule->expected_arrival_at->format('Y-m-d\TH:i') : '')" required />
        <x-input-error :messages="$errors->get('expected_arrival_at')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="origin_address" value="Origin address" />
        <textarea id="origin_address" name="origin_address" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('origin_address', $isEdit ? $schedule->origin_address : '') }}</textarea>
        <x-input-error :messages="$errors->get('origin_address')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="destination_address" value="Destination address" />
        <textarea id="destination_address" name="destination_address" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('destination_address', $isEdit ? $schedule->destination_address : '') }}</textarea>
        <x-input-error :messages="$errors->get('destination_address')" class="mt-2" />
    </div>
</div>