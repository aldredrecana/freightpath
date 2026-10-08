<x-form-page crumb="Drivers" title="Edit driver" method="PUT"
    :action="route('drivers.update', $driver)" :cancel="route('drivers.show', $driver)" submit="Update driver">
    @include('drivers._form')
</x-form-page>