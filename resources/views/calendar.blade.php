<x-public-layout>
    <x-slot name="title">Calendario | FlowSchedule</x-slot>

    @include('layouts.navigation')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    <main class="calendar-container">
        <div id="calendar-root" data-shifts="{{ json_encode($upcomingShifts ?? []) }}"></div>
    </main>

    @include('layouts.footer')
</x-public-layout>