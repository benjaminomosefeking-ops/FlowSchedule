<x-public-layout>
    <x-slot name="title">{{ $board->name }} — FlowSchedule</x-slot>

    @php
        $canEditBoard = Auth::id() === $board->user_id;
    @endphp

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/boards/show.css') }}">
    @endpush

    <div class="board-page">
        <!-- Header del tablero -->
        <div class="board-header">
        <div class="board-title">
            <a href="{{ route('boards.index') }}" class="board-btn secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
            <h1>{{ $board->name }}</h1>
        </div>

        <div class="board-actions">
            <div class="board-status">
                Pizarra personal
            </div>
        </div>
        </div>

        <!-- Contenedor de las pizarras -->
        <div class="board-container">
            <div
                id="excalidraw-root"
                data-board-id="{{ $board->id }}"
                data-initial-data='@json($board->content ?? [])'
                data-csrf-token="{{ csrf_token() }}"
                data-save-url="{{ route('boards.update', $board->id) }}"
                data-content-url="{{ route('boards.content', $board->id) }}"
                data-can-edit="{{ $canEditBoard ? 'true' : 'false' }}"
                data-can-manage-permissions="false"
            ></div>
        </div>
    </div>

</x-public-layout>