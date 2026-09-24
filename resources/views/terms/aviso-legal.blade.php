<x-public-layout>
    <x-slot name="title">Aviso Legal | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/aviso-legal.css') }}">
    @endpush

    @include('layouts.navigation')

    @php
        // Información real del responsable
        $responsable = [
            'nombre'    => 'Benjamin King',
            'email'     => 'landinginformation222@gmail.com',
            'ubicacion' => 'Valencia, España',
        ];

        // Fecha de la última revisión legal
        $ultimaActualizacion = \Carbon\Carbon::parse('2026-09-24')->locale('es');
    @endphp

    <div class="legal-page">
        <div class="legal-container">

            <!-- Cabecera -->
            <header class="legal-box">
                <div class="legal-hero__body">
                    <p class="legal-badge">
                        Actualizado el {{ $ultimaActualizacion->translatedFormat('j \d\e F \d\e Y') }}
                    </p>

                    <h1 class="legal-title">Aviso Legal</h1>

                    <p class="legal-lead">
                        Este aviso legal regula el uso del sitio web FlowSchedule (en adelante, "el sitio web"),
                        accesible a través de su dominio principal.
                    </p>
                </div>
            </header>

            <div class="legal-layout">

                <!-- Contenido principal -->
                <div class="legal-main">
                    <article class="legal-box legal-article">

                        <section id="informacion-general" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">1</span>
                                <span class="legal-section__title-text">Información General</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>El sitio web FlowSchedule es un proyecto personal de portfolio y demostración técnica
                                creado para mostrar capacidades de desarrollo web. No constituye una entidad comercial
                                ni ofrece servicios de pago.</p>

                                <p>FlowSchedule se proporciona como una demostración técnica y proyecto educativo,
                                y sus funcionalidades pueden estar limitadas, en desarrollo o sujetas a cambios
                                sin previo aviso.</p>
                            </section>
                        </section>

                        <section id="responsable" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">2</span>
                                <span class="legal-section__title-text">Responsable del Sitio Web</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>El responsable del sitio web FlowSchedule es:</p>
                                <ul class="legal-list">
                                    <li><span class="legal-bullet" aria-hidden="true"></span><strong>Nombre:</strong> Benjamin King</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span><strong>Correo electrónico:</strong> <a href="mailto:{{ $responsable['email'] }}">{{ $responsable['email'] }}</a></li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span><strong>Ubicación:</strong> {{ $responsable['ubicacion'] }}</li>
                                </ul>
                            </div>
                        </section>

                        <section id="objeto" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">3</span>
                                <span class="legal-section__title-text">Objeto y Finalidad</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>FlowSchedule tiene como único propósito servir como:</p>
                                <ul class="legal-list">
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Proyecto personal de portfolio para demostrar habilidades de desarrollo full-stack</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Demostración técnica de arquitectura hexagonal con Laravel 13</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Ejemplo de implementación de sistemas de gestión de turnos para profesionales de la salud</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Plataforma de aprendizaje y experimentación con tecnologías modernas</li>
                                </ul>

                                <p>El sitio web NO ofrece:</p>
                                <ul class="legal-list">
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Servicios comerciales o de suscripción</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Productos de pago o transacciones financieras</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Asesoría profesional o médica</li>
                                    <li><span class="legal-bullet" aria-hidden="true"></span>Servicios de soporte técnico garantizado</li>
                                </ul>
                            </section>
                        </section>

                        <section id="propiedad-intelectual" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">4</span>
                                <span class="legal-section__title-text">Propiedad Intelectual</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>Todos los derechos de propiedad intelectual del código fuente, diseño, documentación y
                                contenido de FlowSchedule pertenecen a Benjamin King, salvo que se indique expresamente
                                lo contrario.</p>

                                <p>El código fuente de FlowSchedule está disponible públicamente en repositorios de
                                código abierto y puede ser consultado, estudiado y referenciado de acuerdo con sus
                                licencias respectivas.</p>

                                <p>Queda prohibida cualquier reproducción, distribución, modificación o comunicación
                                pública del sitio web o sus componentes con fines comerciales sin autorización
                                expresa y por escrito del responsable.</p>
                            </div>
                        </section>

                        <section id="limitacion-responsabilidad" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">5</span>
                                <span class="legal-section__title-text">Limitación de Responsabilidad</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>FlowSchedule se proporciona "tal cual" y "según esté disponible" sin garantías
                                de ningún tipo, ya sean expresas o implícitas.</p>

                                <p>En la máxima medida permitida por la ley, el responsable no será responsable
                                por ningún daño directo, indirecto, incidental, especial o consequente que resulte
                                del uso o la imposibilidad de usar el sitio web.</p>

                                <p>El responsable no garantiza que el sitio web esté libre de errores, virus u otros
                                componentes dañinos, ni que su uso vaya a producir resultados específicos o beneficiosos.</p>
                            </div>
                        </section>

                        <section id="ley-aplicable" class="legal-section">
                            <h2 class="legal-section__title">
                                <span class="legal-num" aria-hidden="true">6</span>
                                <span class="legal-section__title-text">Ley Aplicable y Jurisdicción</span>
                            </h2>

                            <div class="legal-section__body">
                                <p>El presente Aviso Legal se rige e interpreta de acuerdo con las leyes de España.</p>
                                <p>Para cualquier controversia que pudiera derivarse del uso del sitio web,
                                las partes se someten a la jurisdicción de los tribunales de Valencia, España,
                                renunciando expresamente a cualquier otro fuero que pudiera corresponderles.</p>
                            </div>
                        </section>

                        <section id="contacto-legal" class="legal-box legal-contact" style="scroll-margin-top: 6rem;">
                            <div class="legal-contact__head">
                                <h2 class="legal-contact__title">¿ necesita contactar con el responsable?</h2>
                                <p class="legal-contact__text">Para consultas relacionadas con este aviso legal,
                                el sitio web FlowSchedule o asuntos de privacidad, puede contactar a través de:</p>
                            </div>

                            <dl class="legal-contact__list">
                                <div class="legal-contact__item">
                                    <dt class="legal-contact__label">Correo electrónico</dt>
                                    <dd class="legal-contact__value">
                                        <a href="mailto:{{ $responsable['email'] }}" class="legal-contact__link">{{ $responsable['email'] }}</a>
                                    </dd>
                                </div>
                            </dl>
                        </section>

                    </article>
                </div>

                            <!-- Enlace de regreso -->
                        <div class="legal-noprint">
                            <a href="{{ route('help') }}" class="legal-btn">
                                <svg class="legal-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                Volver a Ayuda y Soporte
                            </a>
                        </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script>
        // Marca en el índice la sección que se está leyendo (el estilo está en el CSS)
        (function () {
            var links = document.querySelectorAll('[data-toc]');
            var sections = document.querySelectorAll('[data-section]');
            if (!links.length || !sections.length || !('IntersectionObserver' in window)) return;

            function activate(id) {
                links.forEach(function (a) {
                    if (a.getAttribute('data-toc') === id) {
                        a.setAttribute('aria-current', 'true');
                    } else {
                        a.removeAttribute('aria-current');
                    }
                });
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) activate(entry.target.id);
                });
            }, { rootMargin: '-20% 0px -70% 0px' });

            sections.forEach(function (s) { observer.observe(s); });
        })();
    </script>
</x-public-layout>