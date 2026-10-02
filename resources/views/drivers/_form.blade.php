@php $isEdit = isset($driver); @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="name" value="Full name" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $isEdit ? $driver->user->name : '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="email" value="Email (login)" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $isEdit ? $driver->user->email : '')" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="password" :value="$isEdit ? 'New password (leave blank to keep)' : 'Password'" />
        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" :required="! $isEdit" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="password_confirmation" value="Confirm password" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" />
    </div>
    <div>
        <x-input-label for="license_no" value="License number" />
        <x-text-input id="license_no" name="license_no" class="block mt-1 w-full" :value="old('license_no', $isEdit ? $driver->license_no : '')" required />
        <x-input-error :messages="$errors->get('license_no')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="license_expiry" value="License expiry" />
        <x-text-input id="license_expiry" name="license_expiry" type="date" class="block mt-1 w-full" :value="old('license_expiry', $isEdit ? $driver->license_expiry->format('Y-m-d') : '')" required />
        <x-input-error :messages="$errors->get('license_expiry')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="contact_no" value="Contact number" />
        <x-text-input id="contact_no" name="contact_no" class="block mt-1 w-full" :value="old('contact_no', $isEdit ? $driver->contact_no : '')" required />
        <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['available' => 'Available', 'on_trip' => 'On trip', 'off_duty' => 'Off duty'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $isEdit ? $driver->status : 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="address" value="Address" />
        <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('address', $isEdit ? $driver->address : '') }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>
</div>