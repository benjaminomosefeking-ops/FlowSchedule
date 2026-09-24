<x-public-layout>
    <x-slot name="title">Política de Privacidad | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/privacy.css') }}">
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
                        de acuerdo con su naturaleza como proyecto personal de portfolio y demostración técnica.
                    </p>
                </div>
            </header>

            <div class="privacy-layout">

                <!-- Contenido principal -->
                <div class="privacy-main">
                    <article class="privacy-box privacy-article">

                        <section id="responsable-tratamiento" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">1</span>
                                <span class="privacy-section__title-text">Responsable del Tratamiento</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>El responsable del tratamiento de sus datos personales es:</p>
                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Nombre:</strong> Benjamin King</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Correo electrónico:</strong> <a href="mailto:{{ $responsable['email'] }}">{{ $responsable['email'] }}</a></li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Ubicación:</strong> {{ $responsable['ubicacion'] }}</li>
                                </ul>
                            </div>
                        </section>

                        <section id="datos-recopilados" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">2</span>
                                <span class="privacy-section__title-text">Datos que Recopilamos</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>FlowSchedule recopila únicamente los datos necesarios para proporcionar su funcionalidad como sistema de gestión de turnos y tareas. No recopilamos datos con fines de marketing, publicidad o análisis de terceros.</p>

                                <p>Los datos que recopilamos se clasifican en las siguientes categorías:</p>

                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de cuenta:</strong> Nombre, dirección de correo electrónico y contraseña almacenada mediante hash seguro (bcrypt).</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de empleados (opcional):</strong> Nombre, correo laboral, skills (en formato JSON) y contador de fines de semana para distribución equitativa.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de turnos y asignaciones:</strong> Título, descripción, tipo de turno, habilidades requeridas, horarios de inicio y fin, estado del turno y relaciones entre usuarios, empleados y turnos.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de tareas:</strong> Título, descripción, fecha de vencimiento, estado de completado, prioridad y relaciones con usuarios y equipos.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de calendario:</strong> Fecha, título, nota, color y datos de dibujo (para eventos personales de calendario).</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de pizarras:</strong> Nombre de la pizarra y contenido serializado (para organización tipo Kanban).</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Datos de equipos:</strong> Nombre del equipo y relaciones de pertenencia entre usuarios y equipos.</li>
                                </ul>

                                <p class="privacy-note">
                                    <strong>Nota importante:</strong> No utilizamos cookies de tracking, no integremos Google Analytics ni servicios similares, y no compartimos datos personales con terceros para fines comerciales.
                                </p>
                            </div>
                        </section>

                        <section id="uso-datos" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">3</span>
                                <span class="privacy-section__title-text">Cómo Utilizamos sus Datos</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>Utilizamos la información que recopilamos exclusivamente para proporcionar y mantener la funcionalidad de FlowSchedule como sistema de gestión:</p>

                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Gestión de cuentas:</strong> Autenticación, recuperación de contraseña y personalización de la experiencia de usuario.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Funcionalidad principal:</strong> Creación, gestión y asignación de turnos; organización de tareas y eventos; gestión de pizarras tipo Kanban; y visualización de calendarios.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Seguridad:</strong> Protección de cuentas mediante hash de contraseñas bcrypt y validación de sesiones.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Comunicaciones esenciales:</strong> Notificaciones relacionadas con la funcionalidad del sistema (restablecimiento de contraseña, etc.) cuando estén configuradas.</li>
                                </ul>

                                <p class="privacy-note">
                                    <strong>Importante:</strong> Las contraseñas se almacenan mediante hash seguro (bcrypt), lo que significa que son irreversibles y no pueden ser descifradas. Nunca almacenamos contraseñas en texto plano.
                                </p>
                            </div>
                        </section>

                        <section id="base-legal" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">4</span>
                                <span class="privacy-section__title-text">Base Legal del Tratamiento</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>El tratamiento de sus datos personales en FlowSchedule se basa en los siguientes fundamentos:</p>

                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Ejecución de funcionalidad:</strong> El tratamiento es necesario para proporcionar el servicio de gestión de turnos y tareas que solicita explícitamente al usar la aplicación.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Interés legítimo:</strong> Mejorar la funcionalidad y seguridad de la aplicación basado en el uso legítimo que usted hace de ella como usuario registrado.</li>
                                </ul>

                                <p>No tratamos sus datos personales con base en el consentimiento para fines de marketing o publicidad, ya que FlowSchedule no realiza estas actividades.</p>
                            </div>
                        </section>

                        <section id="seguridad" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">5</span>
                                <span class="privacy-section__title-text">Medidas de Seguridad</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>Implementamos las siguientes medidas técnicas de seguridad para proteger sus datos personales:</p>

                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Almacenamiento seguro de contraseñas:</strong> Todas las contraseñas se almacenan utilizando hash bcrypt, que es un algoritmo de criptografía unidireccional.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Sesiones seguras:</strong> Utilizamos las sesiones de Laravel con almacenamiento en base de datos para mantener la seguridad de las conexiones.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Protección CSRF:</strong> Todas las formas incluyen protección contra falsificación de solicitudes entre sitios.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Encabezados de seguridad:</strong> Implementamos SecureHeadersMiddleware para proteger contra vulnerabilidades web comunes.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Almacenamiento en base de datos:</strong> Sesiones, caché y colas se almacenan en base de datos para mayor control y seguridad.</li>
                                </ul>

                                <p class="privacy-note">
                                    <strong>Limitación de responsabilidad:</strong> Aunque implementamos medidas de seguridad razonables, ningún sistema puede garantizar seguridad absoluta. Le recomendamos utilizar contraseñas únicas y mantener buenas prácticas de seguridad.
                                </p>
                            </div>
                        </section>

                        <section id="derechos" class="privacy-section">
                            <h2 class="privacy-section__title">
                                <span class="privacy-num" aria-hidden="true">6</span>
                                <span class="privacy-section__title-text">Derechos de los Usuarios</span>
                            </h2>

                            <div class="privacy-section__body">
                                <p>Como responsable del tratamiento, le facilitamos el ejercicio de sus derechos respecto a sus datos personales:</p>

                                <ul class="privacy-list">
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Derecho de acceso:</strong> Puede solicitar una copia de los datos personales que mantenemos sobre usted.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Derecho de rectificación:</strong> Puede solicitar que corrijamos cualquier dato personal inexacto que mantenemos sobre usted.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Derecho de eliminación:</strong> En las circunstancias legalmente previstas, puede solicitar la eliminación de sus datos personales.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Derecho a la limitación:</strong> Puede solicitar que limitemos el tratamiento de sus datos en determinadas circunstancias.</li>
                                    <li><span class="privacy-bullet" aria-hidden="true"></span><strong>Derecho a la portabilidad:</strong> Cuando sea técnicamente factible, tiene derecho a recibir sus datos en un formato estructurado y de uso común.</li>
                                </ul>

                                <p>Para ejercer cualquiera de estos derechos o realizar consultas relacionadas con su privacidad, por favor contacte a través de:</p>
                                <p class="privacy-contact">
                                    <a href="mailto:{{ $responsable['email'] }}">{{ $responsable['email'] }}</a>
                                </p>

                                <p class="privacy-note">
                                    <strong>Nota importante:</strong> FlowSchedule no realiza verificación obligatoria de correo electrónico. Puede utilizar la aplicación sin verificar su dirección de correo electrónico, aunque ciertas funcionalidades de recuperación de cuenta podrían verse afectadas.
                                </p>
                            </div>
                        </section>

                    </article>
                </div>

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