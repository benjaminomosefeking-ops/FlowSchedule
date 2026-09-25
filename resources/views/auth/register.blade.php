<x-public-layout>
<x-slot name="title">Crear cuenta — FlowSchedule</x-slot>

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
        <span class="eyebrow red">Empieza hoy</span>
        <h1>Tu equipo,<br>organizado.</h1>
        <p class="lead">Crea tu espacio de trabajo y pon orden a los turnos de tu equipo. Sin complicaciones, sin hojas de cálculo. <br> <strong style="color: var(--red);">Recuerda que puedes usar un email falso para crear esta página web , pero no podremos contactarte. Esta acción es irreversible.</strong></p>
        <div class="auth-meta">
          <span>Sin tarjeta</span>
          <span>Cancela cuando quieras</span>
          <span>Listo en 60 segundos</span>
        </div>
      </div>

      <div class="auth-form-card">
        <span class="pin" aria-hidden="true"></span>

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="form-field">
            <label for="name">Nombre completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Tu nombre">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-field">
            <label for="email">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autocomplete="username" placeholder="tu@equipo.com">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-field">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-field">
            <label for="password_confirmation">Repite la contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Vuelve a escribirla">
            @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <button class="btn btn-primary" type="submit">Crear mi cuenta</button>
        </form>

        <div class="auth-footer">
          ¿Ya tienes una cuenta? <a href="{{ route('login') }}">Iniciar sesión</a>
        </div>
      </div>
    </div>
  </section>
</main>

@include('layouts.footer')
</x-public-layout>
