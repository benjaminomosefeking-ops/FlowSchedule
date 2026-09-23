<x-public-layout>
  <x-slot name="title">Nueva contraseña — FlowSchedule</x-slot>

  <header>
    <nav>
      <div class="brand">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
            <span>FlowSchedule</span>
        </a>
      </div>
      <div class="nav-right">
        <a href="{{ url('/') }}" class="nav-auth-link">← Volver al inicio</a>
        <a href="{{ route('register') }}" class="nav-register">Registrarse</a>
      </div>
    </nav>
  </header>

  <main class="wrap">
    <section class="auth-page">
      <div class="auth-container">
        <div class="auth-intro-col">
          <span class="eyebrow blue">Nueva contraseña</span>
          <h1>Elige una clave segura.</h1>
          <p class="lead">Crea una contraseña nueva para volver a entrar a tu espacio sin perder el ritmo.</p>
          <div class="auth-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2 14.5 8.5 21 9.3 16 13.8 17.4 20.5 12 17 6.6 20.5 8 13.8 3 9.3 9.5 8.5Z" />
            </svg>
            <span class="hand">todo listo para seguir</span>
          </div>
        </div>

        <div class="auth-form-card">
          <span class="pin" aria-hidden="true"></span>

          <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-field">
              <label for="email">Correo electrónico</label>
              <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="tu@equipo.com">
              @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
              <label for="password">Nueva contraseña</label>
              <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Tu nueva contraseña">
              @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
              <label for="password_confirmation">Confirmar contraseña</label>
              <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite la contraseña">
              @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <button class="btn btn-primary" type="submit">Guardar contraseña</button>
          </form>

          <div class="auth-footer">
            <a href="{{ route('login') }}">Volver al inicio de sesión</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('layouts.footer')
</x-public-layout>
