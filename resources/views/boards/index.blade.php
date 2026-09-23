<x-public-layout>
<x-slot name="title">Mis Pizarras — FlowSchedule</x-slot>
<link rel="stylesheet" href="{{ asset('css/boards/index.css') }}">

@include('layouts.navigation')

<main class="wrap" style="padding: 64px 28px;">

  <section style="margin-bottom: 48px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
      <div>
        <span class="eyebrow blue">Mis Pizarras</span>
        <p style="color: var(--pencil); font-size: 0.96rem; margin: 0;">
          Crea y administra tus pizarras personales para diagramar ideas y organizar tu pensamiento
        </p>
      </div>
      <a href="{{ route('boards.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nueva Pizarra
      </a>
    </div>
  </section>

  @if(session('success'))
    <div style="margin-bottom: 32px; padding: 14px 18px; background: var(--green); color: var(--paper); font-family: var(--font-display); font-size: 0.85rem; border: 1.5px solid var(--ink); box-shadow: 3px 3px 0 var(--ink);">
      ✓ {{ session('success') }}
    </div>
  @endif

  @if($boards->count() > 0)
    <div class="boards-grid">
      @foreach($boards as $board)
        <div class="board-card">
          <span class="pin" aria-hidden="true"></span>

          <div class="board-card-header">
            <h3>{{ $board->name }}</h3>
            <span class="board-badge personal">Personal</span>
          </div>

          <div class="board-card-meta">
            <span>
              Creada por ti
            </span>
            <span>
              {{ $board->updated_at->locale('es')->diffForHumans() }}
            </span>
          </div>

          <div class="board-card-actions">
            <a href="{{ route('boards.show', $board->id) }}" class="board-action-btn primary">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
              Abrir
            </a>

            @if($board->user_id === Auth::id())
              <form method="POST" action="{{ route('boards.destroy', $board->id) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta pizarra?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="board-action-btn danger">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                  Eliminar
                </button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="empty-state" style="text-align: center; padding: 84px 24px; border: 2px dashed var(--rule-strong); border-radius: 4px;">
      <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 24px; opacity: 0.3;">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="9" y1="9" x2="15" y2="9"></line>
        <line x1="9" y1="15" x2="15" y2="15"></line>
      </svg>
      <h3 style="font-family: var(--font-display); font-size: 1.3rem; margin-bottom: 12px; color: var(--ink);">
        No tienes pizarras aún
      </h3>
      <p style="color: var(--pencil); font-size: 0.95rem; margin-bottom: 28px; max-width: 420px; margin-left: auto; margin-right: auto;">
        Crea tu primera pizarra personal con estilo hand-drawn para diagramar ideas y organizar tu pensamiento
      </p>
      <a href="{{ route('boards.create') }}" class="btn btn-primary">
        Crear mi primera pizarra
      </a>
    </div>
  @endif

</main>

@include('layouts.footer')

</x-public-layout>