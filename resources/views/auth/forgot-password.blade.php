<x-public-layout>
  <x-slot name="title">Recuperar acceso — FlowSchedule</x-slot>

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
          <span class="eyebrow red">Recuperar acceso</span>
          <h1>¿Te quedaste sin contraseña?</h1>
          <p class="lead">No pasa nada. Escribe tu correo y te enviaremos un enlace para que elijas una nueva. <br> <strong style="color: var(--red);">¡Recuerda que si usas un Email falso no podremos facilitarte el acceso con una nueva contraseña!</strong></p>
          <div class="auth-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2 14.5 8.5 21 9.3 16 13.8 17.4 20.5 12 17 6.6 20.5 8 13.8 3 9.3 9.5 8.5Z" />
            </svg>
            <span class="hand">te ayudamos a volver</span>
          </div>
        </div>
s
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

          <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-field">
              <label for="email">Correo electrónico</label>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@equipo.com">
              @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <button class="btn btn-primary" type="submit">Enviar enlace</button>
          </form>

          <div class="auth-footer">
            <a href="{{ route('login') }}">Volver a iniciar sesión</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('layouts.footer')
</x-public-layout>
