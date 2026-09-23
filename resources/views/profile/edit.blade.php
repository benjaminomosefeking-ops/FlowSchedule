<x-public-layout>
    <x-slot name="title">Mi Perfil — FlowSchedule</x-slot>
    <link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">


    <header>
        <nav>
            <div class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
                FlowScheduler
            </div>
            <div class="nav-right">
                <a href="{{ route('dashboard') }}" class="nav-auth-link">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="nav-register">Cerrar sesión</button>
                </form>
            </div>
        </nav>
    </header>
      <a href="{{ url('/') }}" class="nav-auth-link nav-back">← Volver al inicio</a>

    <main class="wrap profile-page">
        <section class="profile-hero" aria-labelledby="profile-title">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="profile-header-copy">
                <span class="eyebrow blue">Configuración</span>
                <h1 id="profile-title" class="profile-title">Mi Perfil</h1>
                <p class="profile-description">Gestiona tu información personal y ajustes de seguridad.</p>
            </div>
        </section>

        <div class="profile-grid">
            <article class="profile-card">
                <div class="profile-card-header">
                    <div class="header-left">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <h2>Información Personal</h2>
                    </div>
                </div>
                <div class="profile-card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </article>

            <article class="profile-card">
                <div class="profile-card-header">
                    <div class="header-left">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <h2>Seguridad y Acceso</h2>
                    </div>
                </div>
                <div class="profile-card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </article>

            <article class="profile-card danger">
                <div class="profile-card-header">
                    <div class="header-left">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                        <h2>Zona de Peligro</h2>
                    </div>
                </div>
                <div class="profile-card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </article>
        </div>
    </main>

    @include('layouts.footer')
</x-public-layout>
