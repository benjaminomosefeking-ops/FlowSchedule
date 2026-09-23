<x-public-layout>
<x-slot name="title">Nueva Pizarra — FlowSchedule</x-slot>

@include('layouts.navigation')

<main class="wrap">
  <section class="auth-page" style="padding: 84px 0;">

    <div style="text-align: center; max-width: 640px; margin: 0 auto 54px;">
      <span class="eyebrow blue">Nueva Pizarra</span>
      <h1 style="margin: 18px 0 18px;">Crea tu espacio de pensamiento</h1>
      <p class="lead" style="margin: 0 auto;">
        Dale un nombre a tu pizarra personal para diagramar ideas y organizar tu pensamiento visual.
      </p>
    </div>

    <div style="max-width: 580px; margin: 0 auto;">
      <div class="auth-form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('boards.store') }}">
          @csrf

          <div style="display: grid; gap: 20px;">
            <div class="form-field">
              <label for="name">Nombre de la pizarra</label>
              <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                placeholder="Ej: Planificación Semana 34"
                maxlength="255"
              >
              @error('name')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div style="margin-top: 28px; padding-top: 20px; border-top: 1px dashed var(--rule-strong);">
            <div style="display: flex; gap: 12px;">
              <button class="btn btn-primary" type="submit" style="flex: 1; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Crear Pizarra
              </button>
              <a href="{{ route('boards.index') }}" class="btn btn-ghost" style="padding: 14px 20px;">
                Cancelar
              </a>
            </div>
          </div>
        </form>
      </div>

      <!-- Info adicional -->
      <div style="margin-top: 38px; padding: 20px 24px; background: rgba(31, 63, 168, 0.05); border: 1.5px solid var(--rule); border-radius: 2px;">
        <h3 style="font-family: var(--font-display); font-size: 0.82rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--ink); margin-bottom: 12px;">
           Características de tu pizarra
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 10px;">
          <li style="display: flex; align-items: start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--green); margin-top: 2px;">
              <path d="m5 12 5 5 9-11"/>
            </svg>
            <span style="font-size: 0.88rem; color: var(--pencil);">
              <strong style="color: var(--ink);">Estilo hand-drawn:</strong> Diseño orgánico y artesanal para diagramas más naturales.
            </span>
          </li>
          <li style="display: flex; align-items: start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--green); margin-top: 2px;">
              <path d="m5 12 5 5 9-11"/>
            </svg>
            <span style="font-size: 0.88rem; color: var(--pencil);">
              <strong style="color: var(--ink);">Lienzo infinito:</strong> Sin límites de tamaño, expande tu creatividad.
            </span>
          </li>
          <li style="display: flex; align-items: start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--green); margin-top: 2px;">
              <path d="m5 12 5 5 9-11"/>
            </svg>
            <span style="font-size: 0.88rem; color: var(--pencil);">
              <strong style="color: var(--ink);">Autoguardado:</strong> Tus cambios se guardan automáticamente cada 5 segundos.
            </span>
          </li>
          <li style="display: flex; align-items: start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--green); margin-top: 2px;">
              <path d="m5 12 5 5 9-11"/>
            </svg>
            <span style="font-size: 0.88rem; color: var(--pencil);">
              <strong style="color: var(--ink);">Exportación:</strong> Guarda como PNG.
            </span>
          </li>
        </ul>
      </div>
    </div>

  </section>
</main>

@include('layouts.footer')

</x-public-layout>