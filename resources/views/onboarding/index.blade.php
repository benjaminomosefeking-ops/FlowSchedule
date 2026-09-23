<x-public-layout>
<x-slot name="title">Configuración inicial — FlowSchedule</x-slot>
<link rel="stylesheet" href="{{ asset('css/onboarding/index.css') }}">


@include('layouts.navigation')

<main class="wrap">
  <section class="auth-page" style="padding: 84px 0;">

    <div style="text-align: center; max-width: 640px; margin: 0 auto 54px;">
      <span class="eyebrow blue">Paso 1 de 1</span>
      <h1 style="margin: 18px 0 18px;">Bienvenido a FlowSchedule</h1>
      <p class="lead" style="margin: 0 auto;">Configuraremos tu cuenta para que puedas comenzar a organizar tu tiempo y tareas de forma personal.</p>
    </div>

    <div style="max-width: 680px; margin: 0 auto;">
      <form method="POST" action="{{ route('onboarding.store') }}" id="onboarding-form">
        @csrf

        <!-- Información básica del usuario -->
        @if ($errors->any())
          <div style="background: rgba(200, 52, 26, 0.1); color: var(--red); padding: 12px; border: 1.5px solid var(--red); border-radius: 2px; margin-bottom: 24px; font-family: var(--font-display); font-size: 0.82rem;">
            <strong style="display: block; margin-bottom: 4px; color: var(--red);"> Error de validación:</strong>
            <ul style="margin: 0; padding-left: 18px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="auth-form-card" style="max-width: 100%;">
          <div class="form-field">
            <label for="name">Tu nombre</label>
            <input
              id="name"
              name="name"
              type="text"
              value="{{ old('name') }}"
              required
              autofocus
              placeholder="Ej: Juan Pérez"
              maxlength="255"
            >
            @error('name')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-field">
            <label for="email">Tu correo electrónico</label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              required
              placeholder="Ej: juan@ejemplo.com"
            >
            @error('email')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-field">
            <label for="password">Contraseña</label>
            <input
              id="password"
              name="password"
              type="password"
              required
              placeholder="••••••••"
              minlength="8"
            >
            @error('password')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              placeholder="••••••••"
              minlength="8"
            >
            @error('password_confirmation')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div style="text-align: center; margin-top: 38px;">
          <button class="btn btn-primary" type="submit" style="padding: 16px 42px; font-size: 0.9rem;">
            Empezar a usar FlowSchedule
          </button>
        </div>
      </form>
    </div>

  </section>
</main>

@include('layouts.footer')

</x-public-layout>