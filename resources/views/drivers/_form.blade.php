@php $isEdit = isset($driver); @endphp

<div class="space-y-10">
    <section>
        <h3 class="text-base font-semibold text-[#12263F]">Account</h3>
        <p class="mt-1 text-sm text-gray-500">The driver signs in with this email and password.</p>
        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-form-field name="name" label="Full name" :value="$isEdit ? $driver->user->name : null" />
            <x-form-field name="email" label="Email (login)" type="email" :value="$isEdit ? $driver->user->email : null" />
            <x-form-field name="password" :label="$isEdit ? 'New password' : 'Password'" type="password" :required="!$isEdit" autocomplete="new-password"
                :hint="$isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.'" />
            <x-form-field name="password_confirmation" label="Confirm password" type="password" :required="!$isEdit"
                autocomplete="new-password" />
        </div>
    </section>

    <section>
        <h3 class="text-base font-semibold text-[#12263F]">License</h3>
        <p class="mt-1 text-sm text-gray-500">As printed on the driver's license.</p>
        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-form-field name="license_no" label="License number" :value="$isEdit ? $driver->license_no : null" />
            <x-form-field name="license_expiry" label="License expiry" type="date" :value="$isEdit ? $driver->license_expiry->format('Y-m-d') : null"
                hint="Licenses expiring within 30 days are flagged." />
        </div>
    </section>

    <section>
        <h3 class="text-base font-semibold text-[#12263F]">Contact and status</h3>
        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-form-field name="contact_no" label="Phone number" :value="$isEdit ? $driver->contact_no : null" hint="Example: 09171234567" />
            <x-form-select name="status" label="Status" :options="['available' => 'Available', 'on_trip' => 'On trip', 'off_duty' => 'Off duty']" :value="$isEdit ? $driver->status : 'available'" />
            <x-form-textarea name="address" label="Address" class="md:col-span-2" :value="$isEdit ? $driver->address : null" />
        </div>
    </section>
</div>
