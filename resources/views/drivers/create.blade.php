<x-form-page crumb="Drivers" title="Add driver" subtitle="Creates the driver's login and profile in one step."
    :action="route('drivers.store')" :cancel="route('drivers.index')" submit="Save driver">
    @include('drivers._form')
</x-form-page>
