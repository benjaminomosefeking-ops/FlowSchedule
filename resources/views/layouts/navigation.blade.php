@php
    $showBackToBoards = request()->routeIs('boards.show', 'boards.create');
@endphp

<header>
    <nav>
        {{-- Marca --}}
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
            <span>FlowSchedule</span>
        </a>

        <div class="nav-links">
            @guest
                <a href="{{ url('/') }}#beneficios">Beneficios</a>
                <a href="{{ url('/') }}#como-funciona">Cómo funciona</a>
                <a href="{{ url('/') }}#probarlo">Probarlo</a>
            @else
                <a href="{{ route('dashboard') }}"
                   @class(['is-active' => request()->routeIs('dashboard')])>Dashboard</a>

                <a href="{{ route('boards.create') }}"
                   @class(['is-active' => request()->routeIs('boards.create')])>Crear Pizarra</a>

                <a href="{{ route('profile.edit') }}"
                   @class(['is-active' => request()->routeIs('profile.*')])>Perfil</a>
            @endguest
        </div>

        {{-- Zona derecha de escritorio --}}
        <div class="nav-right">
            @guest
                <a href="{{ route('login') }}" class="nav-auth-link">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="nav-register">Registrarse</a>
            @else
                @if ($showBackToBoards)
                    <a href="{{ route('boards.index') }}">← Volver a pizarras</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-auth-link nav-logout">Cerrar sesión</button>
                </form>
            @endguest
        </div>

        {{-- Botón hamburguesa (solo móvil) --}}
        <button id="mobile-menu-button"
                class="mobile-menu-button"
                type="button"
                aria-controls="mobile-menu-sidebar"
                aria-expanded="false"
                aria-label="Abrir menú">
            <svg class="icon-open" width="24" height="24" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg class="icon-close" width="24" height="24" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </nav>
</header>

{{-- Menú móvil--}}
<aside id="mobile-menu-sidebar" class="mobile-menu-sidebar" aria-hidden="true">
    <div class="mobile-menu-backdrop" id="mobile-menu-backdrop"></div>

    <div class="mobile-menu-sidebar-content" role="dialog" aria-modal="true" aria-label="Menú de navegación">
        <div class="mobile-menu-head">
            <span class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
                <span>FlowSchedule</span>
            </span>

            <button type="button" class="mobile-menu-close" id="mobile-menu-close" aria-label="Cerrar menú">
                <svg width="22" height="22" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        {{-- OJO: div, no <nav>, para no heredar el CSS global de nav --}}
        <div class="mobile-menu-nav">
            @guest
                <a href="{{ url('/') }}#beneficios" class="mobile-menu-link">Beneficios</a>
                <a href="{{ url('/') }}#como-funciona" class="mobile-menu-link">Cómo funciona</a>
                <a href="{{ url('/') }}#probarlo" class="mobile-menu-link">Probarlo</a>

                <div class="mobile-menu-sep"></div>

                <a href="{{ route('login') }}" class="mobile-menu-link">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="mobile-menu-cta">Registrarse</a>
            @else
                @if ($showBackToBoards)
                    <a href="{{ route('boards.index') }}" class="mobile-menu-link">← Volver a pizarras</a>
                @endif

                <a href="{{ route('dashboard') }}" class="mobile-menu-link">Dashboard</a>
                <a href="{{ route('boards.index') }}" class="mobile-menu-link">Crear Pizarra</a>
                <a href="{{ route('profile.edit') }}" class="mobile-menu-link">Perfil</a>

                <div class="mobile-menu-sep"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mobile-menu-link">Cerrar sesión</button>
                </form>
            @endguest
        </div>
    </div>
</aside>

<script>
(function () {
    'use strict';

    var btn      = document.getElementById('mobile-menu-button');
    var sidebar  = document.getElementById('mobile-menu-sidebar');
    var backdrop = document.getElementById('mobile-menu-backdrop');
    var closeBtn = document.getElementById('mobile-menu-close');
    if (!btn || !sidebar) return;

    var lastFocused = null;

    function openMenu() {
        lastFocused = document.activeElement;
        sidebar.classList.add('is-open');
        sidebar.setAttribute('aria-hidden', 'false');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'Cerrar menú');
        document.body.classList.add('menu-open');

        var first = sidebar.querySelector('a, button');
        if (first) first.focus();
    }

    function closeMenu() {
        if (!sidebar.classList.contains('is-open')) return;
        sidebar.classList.remove('is-open');
        sidebar.setAttribute('aria-hidden', 'true');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-label', 'Abrir menú');
        document.body.classList.remove('menu-open');

        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    function toggleMenu() {
        if (sidebar.classList.contains('is-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    btn.addEventListener('click', toggleMenu);

    if (backdrop) backdrop.addEventListener('click', closeMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);

    // Cierra al pulsar cualquier enlace dentro del menú
    sidebar.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', closeMenu);
    });

    // Cierra con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });

    // Si el viewport pasa a escritorio con el menú abierto, ciérralo
    var mq = window.matchMedia('(min-width:761px)');
    function onChange(e) { if (e.matches) closeMenu(); }
    if (mq.addEventListener) mq.addEventListener('change', onChange);
    else mq.addListener(onChange);
})();
</script>