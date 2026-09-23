<x-public-layout>
  <x-slot name="title">Iniciar sesión — FlowSchedule</x-slot>

  <header>
    <nav>
      <div class="brand">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
            <span>FlowSchedule</span>
        </a>
      </div>
<div class="nav-right">
  <a href="{{ url('/') }}" class="nav-auth-link nav-back">← Volver al inicio</a>
  <a href="{{ route('register') }}" class="nav-register">Registrarse</a>
</div>  
    </nav>
  </header>

  <main class="wrap">
    <section class="auth-page">
      <div class="auth-container">
        <div class="auth-intro-col">
          <span class="eyebrow blue">Bienvenido de nuevo</span>
          <h1>Qué bueno verte.</h1>
          <p class="lead">Entra para seguir organizando tu semana y gestionando los turnos de tu equipo.</p>
          <div class="auth-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2 14.5 8.5 21 9.3 16 13.8 17.4 20.5 12 17 6.6 20.5 8 13.8 3 9.3 9.5 8.5Z" />
            </svg>
            <span class="hand">tu equipo te espera</span>
          </div>
        </div>

        <div class="auth-form-card">
          <span class="pin" aria-hidden="true"></span>

          @if (session('status'))
          <div class="auth-status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m5 12 5 5 9-11" />
            </svg>
            {{ session('status') }}
          </div>
          @endif

          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-field">
              <label for="email">Correo electrónico</label>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@equipo.com">
              @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
              <label for="password">Contraseña</label>
              <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña">
              @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
              <label class="form-checkbox">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="checkbox-box" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m5 12 5 5 9-11" />
                  </svg>
                </span>
                <span>Recordarme</span>
              </label>
              @if (Route::has('password.request'))
              <a class="form-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
              @endif
            </div>

            <button class="btn btn-primary" type="submit">Entrar a mi espacio</button>
          </form>

          <div class="auth-footer">
            ¿Todavía no tienes cuenta? <a href="{{ route('register') }}">Crear una cuenta</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('layouts.footer')

</x-public-layout>