<x-public-layout>
    <x-slot name="title">Contacto | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/contact.css') }}">
    @endpush

    @include('layouts.navigation')

    @php
        // Información real de contacto
        $contacto = [
            'nombre'    => 'Benjamin King',
            'email'     => 'landinginformation222@gmail.com',
            'ubicacion' => 'Valencia, España',
        ];
    @endphp

    <div class="contact-page">
        <div class="contact-container">

            <!-- Cabecera -->
            <header class="contact-box contact-hero">
                <h1 class="contact-title">Contacto</h1>
                <p class="contact-leaf">
                    Información de contacto para consultas relacionadas con FlowSchedule
                </p>
            </header>

            <div class="contact-layout">

                <!-- Información de contacto -->
                <section class="contact-box" aria-labelledby="datos-titulo">
                    <div class="contact-panel__head">
                        <h2 id="datos-titulo" class="contact-panel__title">Datos de contacto</h2>
                        <p class="contact-panel__text">
                            Puede utilizar la siguiente información para ponerse en contacto
                            con el responsable de FlowSchedule.
                        </p>
                    </div>

                    <div class="contact-channel">
                        <span class="contact-channel__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="contact-channel__label">Responsable</p>
                            <p class="contact-channel__value">{{ $contacto['nombre'] }}</p>
                        </div>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="contact-channel__label">Ubicación</p>
                            <p class="contact-channel__value">{{ $contacto['ubicacion'] }}</p>
                        </div>
                    </div>
                </section>

                <!-- Información sobre el proyecto -->
                <section class="contact-box" aria-labelledby="proyecto-titulo">
                    <div class="contact-panel__head">
                        <h2 id="proyecto-titulo" class="contact-panel__title">Sobre FlowSchedule</h2>
                        <p class="contact-panel__text">
                            Información relevante sobre la naturaleza y propósito del proyecto.
                        </p>
                    </div>

                    <div class="contact-info">
                        <p><strong>Naturaleza del proyecto:</strong> FlowSchedule es un proyecto personal de portfolio y demostración técnica desarrollado para mostrar capacidades de desarrollo web.</p>
                        <p><strong>Finalidad:</strong> No constituye un servicio comercial ni ofrece productos de pago. Tiene como único propósito demostrar habilidades técnicas en desarrollo full-stack, arquitectura hexagonal y Laravel 13.</p>
                        <p><strong>Estado del proyecto:</strong> El código fuente está disponible públicamente en repositorios de código abierto para consulta y estudio.</p>
                        <p><strong>Limitaciones:</strong> Como proyecto de demostración, ciertas funcionalidades pueden estar en desarrollo, ser limitadas o sujetas a cambios sin previo aviso.</p>
                    </div>
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