<x-public-layout>
    <x-slot name="title">Contacto | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/contact.css') }}">
    @endpush

    @include('layouts.navigation')

    @php
    // ─────────────────────────────────────────────
    // EDITA ESTOS DATOS con la información real de contacto
    // ─────────────────────────────────────────────
    $contacto = [
        'email'    => 'soporte@flowscheduler.com',
        'telefono' => '+34 900 000 000',
        'horario'  => 'Lunes a viernes, de 9:00 a 18:00',
    ];
    $telefonoLink = preg_replace('/[^\d+]/', '', $contacto['telefono']);
    @endphp

    <div class="contact-page">
        <div class="contact-container">

            <!-- Cabecera -->
            <header class="contact-box contact-hero">
                <h1 class="contact-title">Contacto</h1>
                <p class="contact-lead">
                    Estamos aquí para ayudarte. Si tienes una pregunta, una sugerencia o necesitas asistencia, escríbenos o llámanos, o déjanos un mensaje y te respondemos.
                </p>
            </header>

            <div class="contact-layout">

                <!-- Canales de contacto -->
                <section class="contact-box" aria-labelledby="canales-titulo">
                    <div class="contact-panel__head">
                        <h2 id="canales-titulo" class="contact-panel__title">¿Necesitas ayuda?</h2>
                        <p class="contact-panel__text">Elige el canal que te resulte más cómodo.</p>
                    </div>

                    <div class="contact-channel">
                        <span class="contact-channel__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="contact-channel__label">Correo electrónico</p>
                            <p class="contact-channel__value">
                                <a href="mailto:{{ $contacto['email'] }}" class="contact-link">{{ $contacto['email'] }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-channel">
                        <span class="contact-channel__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z" />
                            </svg>
                        </span>
                        <div>
                            <p class="contact-channel__label">Teléfono</p>
                            <p class="contact-channel__value">
                                <a href="tel:{{ $telefonoLink }}" class="contact-link">{{ $contacto['telefono'] }}</a>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Formulario -->
                <section class="contact-box" aria-labelledby="form-titulo">
                    <div class="contact-panel__head">
                        <h2 id="form-titulo" class="contact-panel__title">¿Prefieres que te contactemos nosotros?</h2>
                        <p class="contact-panel__text">Déjanos tus datos y tu mensaje, y te responderemos lo antes posible.</p>
                    </div>

                    @if (session('status'))
                        <p class="contact-alert" role="status">{{ session('status') }}</p>
                    @endif

                    {{-- Cambia action="#" por la ruta que procese el formulario, por ejemplo {{ route('contact.send') }} --}}
                    <form action="#" method="POST" class="contact-form" novalidate>
                        @csrf

                        <div class="contact-form__row">
                            <div class="contact-field">
                                <label for="nombre" class="contact-field__label">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" required autocomplete="name"
                                       value="{{ old('nombre') }}"
                                       class="contact-field__input"
                                       aria-invalid="{{ $errors->has('nombre') ? 'true' : 'false' }}"
                                       @if ($errors->has('nombre')) aria-describedby="nombre-error" @endif>
                                @error('nombre')
                                    <p id="nombre-error" class="contact-field__error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="contact-field">
                                <label for="email" class="contact-field__label">Correo electrónico</label>
                                <input type="email" id="email" name="email" required autocomplete="email"
                                       value="{{ old('email') }}"
                                       class="contact-field__input"
                                       aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                       @if ($errors->has('email')) aria-describedby="email-error" @endif>
                                @error('email')
                                    <p id="email-error" class="contact-field__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="contact-field">
                            <label for="mensaje" class="contact-field__label">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="6" required
                                      class="contact-field__input"
                                      aria-invalid="{{ $errors->has('mensaje') ? 'true' : 'false' }}"
                                      @if ($errors->has('mensaje')) aria-describedby="mensaje-error" @endif>{{ old('mensaje') }}</textarea>
                            @error('mensaje')
                                <p id="mensaje-error" class="contact-field__error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="contact-submit">Enviar mensaje</button>
                    </form>
                </section>
            </div>

            <!-- Enlace de regreso -->
            <div>
                <a href="{{ route('help') }}" class="contact-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    Volver a Ayuda y Soporte
                </a>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</x-public-layout>