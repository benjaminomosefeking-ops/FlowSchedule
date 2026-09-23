<x-public-layout>
    <x-slot name="title">Política de Privacidad | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/privacity.css') }}">
    @endpush

    @include('layouts.navigation')

    @php
    // Información de contacto real del proyecto
    $contacto = [
        'email'    => 'soporte@flowscheduler.com', // Este email puede ser inventado según las instrucciones
        'telefono' => '+34 900 000 000',           // Número de contacto ficticio según las instrucciones
        'horario'  => 'Lunes a viernes, de 9:00 a 18:00',
    ];
    $telefonoLink = preg_replace('/[^\d+]/', '', $contacto['telefono']);

    // Fecha de la última revisión legal
    $ultimaActualizacion = \Carbon\Carbon::parse('2026-09-20')->locale('es');

    // Cláusulas: el índice y el contenido se generan desde este array
    // 'items' = lista de [etiqueta, texto] · 'cierre' = párrafos tras la lista · 'nota' = [título, texto]
    $secciones = [
        [
            'id'     => 'informacion-recopilada',
            'titulo' => 'Información que recopilamos',
            'intro'  => 'FlowSchedule recopila la siguiente información personal cuando usted se registra y utiliza nuestra plataforma:',
            'items'  => [
                ['Información de cuenta', 'nombre completo, dirección de correo electrónico y contraseña cifrada.'],
                ['Datos de autenticación', 'marca de tiempo de verificación de correo electrónico y tokens de sesión para mantener su conexión segura.'],
                ['Información de perfil opcional (para usuarios tipo «jefe»)', 'nombre de la empresa y descripción de la empresa, solo si usted elige proporcionar esta información durante el proceso de configuración inicial.'],
                ['Información de contacto', 'cuando utiliza nuestro formulario de contacto, recopilamos su nombre, dirección de correo electrónico y el mensaje que nos envía.'],
            ],
            'nota'   => [
                'Nota importante sobre los correos electrónicos',
                'Las direcciones de correo electrónico mostradas en esta aplicación (como soporte@flowscheduler.com) son ejemplos y pueden ser inventados. Sin embargo, si usted proporciona su correo electrónico personal durante el registro o en el formulario de contacto, esa información es real y se trata según lo descrito en esta política.',
            ],
        ],
        [
            'id'     => 'uso-informacion',
            'titulo' => 'Cómo usamos su información',
            'intro'  => 'Utilizamos la información que recopilamos para los siguientes propósitos:',
            'items'  => [
                ['Proveer y mantener nuestro servicio', 'para crear y gestionar su cuenta, autenticar su acceso y proporcionar la funcionalidad principal de planificación y organización.'],
                ['Comunicaciones relacionadas con el servicio', 'para enviarle notificaciones sobre su cuenta, actualizaciones de funcionalidad y respuestas a sus consultas de soporte.'],
                ['Mejorar nuestro servicio', 'para entender cómo utiliza FlowSchedule e identificar oportunidades para mejorar la experiencia del usuario.'],
                ['Responder a solicitudes de contacto', 'para procesar y responder a los mensajes que nos envía mediante nuestro formulario de contacto.'],
            ],
            'cierre' => [
                'No utilizamos su información personal para fines de marketing ni para crear perfiles publicitarios.',
            ],
        ],
        [
            'id'     => 'base-legal',
            'titulo' => 'Base legal y fundamentación',
            'intro'  => 'El tratamiento de su información personal se basa en los siguientes fundamentos legales:',
            'items'  => [
                ['Ejecución de un contrato', 'cuando el tratamiento es necesario para proporcionar nuestro servicio de planificación según nuestros términos de servicio.'],
                ['Consentimiento', 'cuando usted proporciona información adicional opcional (como el nombre de la empresa) o se comunica con nosotros mediante el formulario de contacto.'],
                ['Interés legítimo', 'para mejorar nuestro servicio y proporcionar comunicaciones relacionadas con el servicio que razonablemente espera recibir como usuario.'],
                ['Cumplimiento de obligaciones legales', 'cuando estamos obligados por ley a conservar ciertos registros.'],
            ],
            'cierre' => [
                'Usted puede retirar su consentimiento en cualquier momento para el tratamiento basado en consentimiento, sin afectar al tratamiento basado en otras bases legales.',
            ],
        ],
        [
            'id'     => 'seguridad',
            'titulo' => 'Seguridad de su información',
            'intro'  => 'Implementamos medidas de seguridad razonables para proteger su información personal:',
            'items'  => [
                ['Contraseñas cifradas', 'todas las contraseñas se almacenan utilizando un hash criptográfico fuerte (bcrypt) y nunca en texto plano.'],
                ['Transmisión segura', 'utilizamos HTTPS para cifrar todos los datos transmitidos entre su navegador y nuestros servidores.'],
                ['Control de acceso', 'restringimos el acceso a la información personal solo al personal autorizado que necesita conocerla para desempeñar sus funciones.'],
                ['Monitoreo de seguridad', 'revisamos periódicamente nuestros sistemas para identificar y abordar vulnerabilidades de seguridad.'],
            ],
            'cierre' => [
                'Sin embargo, debe ser consciente de que ningún método de transmisión por Internet ni de almacenamiento electrónico es 100 % seguro.',
            ],
        ],
        [
            'id'     => 'derechos',
            'titulo' => 'Sus derechos',
            'intro'  => 'Dependiendo de su jurisdicción, usted puede tener ciertos derechos respecto de su información personal:',
            'items'  => [
                ['Derecho de acceso', 'puede solicitar una copia de la información personal que mantenemos sobre usted.'],
                ['Derecho de rectificación', 'puede solicitar que corrijamos cualquier información personal inexacta que mantenemos sobre usted.'],
                ['Derecho de eliminación', 'en ciertas circunstancias, puede solicitar que eliminemos su información personal.'],
                ['Derecho a retirar el consentimiento', 'cuando el tratamiento se base en su consentimiento, puede retirarlo en cualquier momento.'],
                ['Derecho a la portabilidad', 'en ciertas circunstancias, tiene derecho a recibir su información personal en un formato estructurado, de uso común y lectura mecánica.'],
            ],
            'cierre' => [
                'Para ejercer cualquiera de estos derechos, contáctenos utilizando la información de la sección de contacto que figura a continuación.',
            ],
            'nota'   => [
                'Importante sobre la recuperación de contraseñas',
                'Si olvida su contraseña, utilice la función «¿Olvidó su contraseña?» en la pantalla de inicio de sesión. Si desea cambiar el correo electrónico asociado a su cuenta para fines de recuperación, hágalo mediante la función «Editar Perfil» de su cuenta.',
            ],
        ],
    ];
    @endphp

    <div class="privacy-page">
        <div class="privacy-container">

            <!-- Cabecera -->
            <header class="privacy-box">
                <div class="privacy-hero__body">
                    <p class="privacy-badge">
                        Actualizado el {{ $ultimaActualizacion->translatedFormat('j \d\e F \d\e Y') }}
                    </p>

                    <h1 class="privacy-title">Política de Privacidad</h1>

                    <p class="privacy-lead">
                        Esta política describe cómo FlowSchedule recopila, utiliza y protege la información personal
                        de los usuarios que se registran y utilizan nuestra plataforma de gestión y planificación.
                    </p>
                </div>

                <!-- Resumen rápido -->
                <div class="privacy-summary">
                    <div class="privacy-summary__grid">
                        <div class="privacy-summary__item">
                            <h2 class="privacy-summary__title">Información recopilada</h2>
                            <p class="privacy-summary__text">Nombre, correo electrónico y datos básicos de la cuenta.</p>
                        </div>
                        <div class="privacy-summary__item">
                            <h2 class="privacy-summary__title">Uso de la información</h2>
                            <p class="privacy-summary__text">Para proporcionar y mejorar el servicio de planificación.</p>
                        </div>
                        <div class="privacy-summary__item">
                            <h2 class="privacy-summary__title">Privacidad</h2>
                            <p class="privacy-summary__text">No compartimos información personal con terceros para fines de marketing.</p>
                        </div>
                    </div>
                    <p class="privacy-summary__note">
                        Este resumen es orientativo y no sustituye a la política completa que figura a continuación.
                    </p>
                </div>
            </header>

            <div class="privacy-layout">

                <!-- Índice (móvil: desplegable) -->
                <details class="privacy-box privacy-toc-mobile privacy-noprint">
                    <summary class="privacy-toc-mobile__summary">
                        Índice de contenidos
                        <svg class="privacy-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <nav aria-label="Índice de contenidos" class="privacy-toc-mobile__nav">
                        <ul class="privacy-toc__list">
                            @foreach ($secciones as $s)
                            <li>
                                <a href="#{{ $s['id'] }}" data-toc="{{ $s['id'] }}" class="privacy-toc__link">
                                    <span class="privacy-toc__num">{{ $loop->iteration }}</span>
                                    <span>{{ $s['titulo'] }}</span>
                                </a>
                            </li>
                            @endforeach
                            <li>
                                <a href="#contacto" data-toc="contacto" class="privacy-toc__link">
                                    <span class="privacy-toc__num privacy-toc__num--mark" aria-hidden="true"></span>
                                    <span>Contacto</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </details>

                <!-- Índice (escritorio) -->
                <aside class="privacy-toc-desktop privacy-noprint">
                    <nav aria-label="Índice de contenidos" class="privacy-box privacy-toc-desktop__nav">
                        <ul class="privacy-toc__list">
                            @foreach ($secciones as $s)
                            <li>
                                <a href="#{{ $s['id'] }}" data-toc="{{ $s['id'] }}" class="privacy-toc__link">
                                    <span class="privacy-toc__num">{{ $loop->iteration }}</span>
                                    <span>{{ $s['titulo'] }}</span>
                                </a>
                            </li>
                            @endforeach
                            <li>
                                <a href="#contacto" data-toc="contacto" class="privacy-toc__link">
                                    <span class="privacy-toc__num privacy-toc__num--mark" aria-hidden="true"></span>
                                    <span>Contacto</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </aside>

                <!-- Contenido principal -->
                <div class="privacy-main">
                    <article class="privacy-box privacy-article">
                        @foreach ($secciones as $s)
                        <section id="{{ $s['id'] }}" data-section class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">{{ $loop->iteration }}</span>
                                <span class="privacy-section__title-text">{{ $s['titulo'] }}</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>{{ $s['intro'] }}</p>

                                <ul class="privacy-list">
                                    @foreach ($s['items'] as $item)
                                    <li>
                                        <span class="privacy-bullet" aria-hidden="true"></span>
                                        <span><strong>{{ $item[0] }}:</strong> {{ $item[1] }}</span>
                                    </li>
                                    @endforeach
                                </ul>

                                @isset($s['cierre'])
                                    @foreach ($s['cierre'] as $parrafo)
                                        <p>{{ $parrafo }}</p>
                                    @endforeach
                                @endisset

                                @isset($s['nota'])
                                    <p class="privacy-note">
                                        <strong class="privacy-note__title">{{ $s['nota'][0] }}</strong>
                                        {{ $s['nota'][1] }}
                                    </p>
                                @endisset
                            </div>
                        </section>
                        @endforeach
                    </article>

                    <!-- Contacto -->
                    <section id="contacto" data-section class="privacy-box privacy-contact" style="scroll-margin-top: 6rem;">
                        <div class="privacy-contact__head">
                            <h2 class="privacy-contact__title">¿Tiene alguna duda sobre nuestra política de privacidad?</h2>
                            <p class="privacy-contact__text">Escríbanos o llámenos y le ayudaremos a resolverla.</p>
                        </div>

                        <dl class="privacy-contact__list">
                            <div class="privacy-contact__item">
                                <dt class="privacy-contact__label">Correo electrónico</dt>
                                <dd class="privacy-contact__value">
                                    <a href="mailto:{{ $contacto['email'] }}" class="privacy-contact__link">{{ $contacto['email'] }}</a>
                                </dd>
                            </div>
                            <div class="privacy-contact__item">
                                <dt class="privacy-contact__label">Teléfono</dt>
                                <dd class="privacy-contact__value">
                                    <a href="tel:{{ $telefonoLink }}" class="privacy-contact__link">{{ $contacto['telefono'] }}</a>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Enlace de regreso -->
                    <div class="privacy-noprint">
                        <a href="{{ route('help') }}" class="privacy-btn">
                            <svg class="privacy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                            Volver a Ayuda y Soporte
                        </a>
                    </div>
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