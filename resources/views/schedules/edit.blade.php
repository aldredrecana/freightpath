<x-form-page crumb="Schedules" title="Edit trip" method="PUT" :action="route('schedules.update', $schedule)" :cancel="route('schedules.show', $schedule)" submit="Update trip">
    @include('schedules._form')
</x-form-page>
