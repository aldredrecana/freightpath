<x-form-page crumb="Schedules" title="Assign trip" subtitle="Choose a driver and a truck, then set the route and times."
    :action="route('schedules.store')" :cancel="route('schedules.index')" submit="Assign trip">
    @include('schedules._form')
</x-form-page>