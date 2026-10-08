<x-form-page crumb="Trucks" title="Edit truck" method="PUT"
    :action="route('trucks.update', $truck)" :cancel="route('trucks.show', $truck)" submit="Update truck">
    @include('trucks._form')
</x-form-page>