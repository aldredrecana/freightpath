<x-form-page crumb="Trucks" title="Add truck" subtitle="Register a truck so it can be assigned to trips." :action="route('trucks.store')"
    :cancel="route('trucks.index')" submit="Save truck">
    @include('trucks._form')
</x-form-page>
