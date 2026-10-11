@php
    $isEdit = isset($schedule);
    $driverOptions = $drivers->mapWithKeys(fn($d) => [$d->id => $d->user->name . ' (' . $d->license_no . ')'])->all();
    $truckOptions = $trucks->mapWithKeys(fn($t) => [$t->id => $t->plate_no . ' - ' . $t->model])->all();
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <x-form-select name="driver_id" label="Driver" placeholder="Select a driver" :options="$driverOptions" :value="$isEdit ? $schedule->driver_id : null"
        hint="Off-duty drivers are hidden. Overlapping trips are blocked when you save." />
    <x-form-select name="truck_id" label="Truck" placeholder="Select a truck" :options="$truckOptions" :value="$isEdit ? $schedule->truck_id : null"
        hint="Trucks in maintenance are hidden. Overlapping trips are blocked when you save." />
    <x-form-field name="departure_at" label="Departure" type="datetime-local" :value="$isEdit ? $schedule->departure_at->format('Y-m-d\TH:i') : null" />
    <x-form-field name="expected_arrival_at" label="Expected arrival" type="datetime-local" :value="$isEdit ? $schedule->expected_arrival_at->format('Y-m-d\TH:i') : null" />
    <x-form-textarea name="origin_address" label="Origin address" class="md:col-span-2" rows="2"
        :value="$isEdit ? $schedule->origin_address : null" />
    <x-form-textarea name="destination_address" label="Destination address" class="md:col-span-2" rows="2"
        :value="$isEdit ? $schedule->destination_address : null" />
</div>