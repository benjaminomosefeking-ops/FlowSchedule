<x-public-layout>
    <x-slot name="title">Configuración | FlowScheduler</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush


    @include('layouts.navigation')

    <!-- SETTINGS CONTENT -->
    <main class="dashboard-wrap">

        <!-- Hero -->
        <section style="margin-bottom: 64px;">
            <div class="user-hero-content" style="display: flex; align-items: center; gap: 24px; margin-bottom: 32px;">
                <div class="user-avatar" style="width: 64px; height: 64px; display: grid; place-items: center; background: var(--blue); color: var(--paper); border-radius: 40px; font-weight: 700; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <span class="eyebrow blue" style="font-size: 0.75rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.05em;">Configuración</span>
                    <h1 style="margin: 4px 0 4px; font-size: 2rem; text-wrap: balance;">
                        Configuración de tu cuenta
                    </h1>
                    <p class="lead" style="margin: 0; opacity: 0.8; font-size: 1rem;">Personaliza tu experiencia en FlowScheduler</p>
                </div>
            </div>
        </section>

        <div class="dashboard-grid">
            <!-- Información de la cuenta -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Información de la cuenta</h2>
                </div>

                <div class="user-info-section">
                    <div class="user-info-row">
                        <span class="user-info-label">Nombre completo</span>
                        <span class="user-info-value">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Correo electrónico</span>
                        <span class="user-info-value">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Miembro desde</span>
                        <span class="user-info-value">{{ Auth::user()->created_at->locale('es')->isoFormat('DD MMM YYYY') }}</span>
                    </div>
                </div>
            </div>

            <!-- Preferencias -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Preferencias</h2>
                </div>

                <div class="user-info-section">
                    <div class="user-info-row">
                        <span class="user-info-label">Tema de color</span>
                        <span class="user-info-value">Claro (predeterminado)</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Formato de fecha</span>
                        <span class="user-info-value">DD/MM/YYYY</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Zona horaria</span>
                        <span class="user-info-value">{{ \Carbon\Carbon::now()->timezoneName }}</span>
                    </div>
                </div>
            </div>

            <!-- Notificaciones -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Notificaciones</h2>
                </div>

                <div class="user-info-section">
                    <div class="user-info-row">
                        <span class="user-info-label">Notificaciones por email</span>
                        <span class="user-info-value">Activadas</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Notificaciones push</span>
                        <span class="user-info-value">Disponibles en app móvil</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Recordatorios de tareas</span>
                        <span class="user-info-value">10 minutos antes</span>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="dashboard-card dashboard-card-full">
                <div class="card-header">
                    <h2 class="card-title">Acciones</h2>
                </div>

                <div class="quick-actions">
                    <a href="{{ route('profile.edit') }}" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Editar perfil
                    </a>
                    <a href="{{ route('logout') }}" class="quick-action-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Cerrar sesión
                    </a>
                </div>
            </div>

            @if(Route::has('logout'))
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endif
        </div>
    </main>

    @include('layouts.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any settings-specific JavaScript here
        });
    </script>
</x-public-layout>