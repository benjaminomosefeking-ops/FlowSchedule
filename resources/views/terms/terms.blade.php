<x-public-layout>
    <x-slot name="title">Términos y Condiciones | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/terms.css') }}">
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

    <div class="terms-page">
        <div class="terms-container">

            <!-- Cabecera -->
            <header class="terms-box">
                <div class="terms-hero__body">
                    <p class="terms-badge">
                        Actualizado el {{ $ultimaActualizacion->translatedFormat('j \d\e F \d\e Y') }}
                    </p>

                    <h1 class="terms-title">Términos y Condiciones</h1>

                    <p class="terms-lead">
                        Estos Términos de Uso ("Términos") rigen su acceso y uso de FlowSchedule,
                        un proyecto personal de portfolio y demostración técnica.
                    </p>
                </div>
            </header>

            <div class="terms-layout">

                <!-- Contenido principal -->
                <div class="terms-main">
                    <article class="terms-box terms-article">

                        <section id="aceptacion" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">1</span>
                                <span class="terms-section__title-text">Aceptación de los Términos</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Al acceder o usar FlowSchedule, usted acepta estar obligado por estos Términos de Uso y nuestra Política de Privacidad. Si no está de acuerdo con alguno de estos términos, por favor no utilice el servicio.</p>

                                <p>Estos Términos se aplican a todos los visitantes, usuarios y otras personas que accedan o utilicen el proyecto FlowSchedule.</p>
                            </div>
                        </section>

                        <section id="descripcion-proyecto" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">2</span>
                                <span class="terms-section__title-text">Descripción del Proyecto</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule es un proyecto personal de portfolio y demostración técnica creado para mostrar capacidades de desarrollo web utilizando el stack tecnológico moderno:</p>

                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Backend:</strong> Laravel 13 con arquitectura hexagonal/Domain-Driven Design</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Frontend:</strong> Blade templates, Alpine.js, Tailwind CSS y Vite</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Base de datos:</strong> MySQL/SQLite con Eloquent ORM</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Funcionalidades destacadas:</strong> Gestión de turnos, tareas, calendario y pizarras tipo Kanban</li>
                                </ul>

                                <p><strong>Importante:</strong> FlowSchedule se proporciona como proyecto educativo y de demostración. No constituye un servicio comercial ni ofrece garantías de disponibilidad, funcionalidad o soporte técnico continuo.</p>
                            </div>
                        </section>

                        <section id="uso-del-proyecto" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">3</span>
                                <span class="terms-section__title-text">Uso del Proyecto</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule pone a disposición del usuario las siguientes funcionalidades:</p>

                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Registro y autenticación de usuarios</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Gestión de perfiles de usuario</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Creación y administración de turnos de trabajo</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Asignación de empleados a turnos basada en habilidades</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Gestión de tareas personales y de equipos</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Visualización y gestión de eventos de calendario</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Organización de trabajo mediante pizarras tipo Kanban</li>
                                </ul>

                                <p>El uso de FlowSchedule está sujeto a las siguientes condiciones:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Uso no comercial:</strong> El proyecto se proporciona únicamente para fines educativos, de demostración y evaluación personal.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Modificaciones permitidas:</strong> Está permitido examinar, estudiar y modificar el código fuente para fines de aprendizaje, siempre que se respeten las licencias aplicables.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Prohibiciones:</strong> Queda prohibido utilizar FlowSchedule para actividades illegales, comercializar una copia modificada sin autorización, o utilizar el proyecto para competir deslealmente con el trabajo original del autor.</li>
                                </ul>
                            </div>
                        </section>

                        <section id="cuenta-usuario" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">4</span>
                                <span class="terms-section__title-text">Cuenta de Usuario</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Para acceder a ciertas funcionalidades de FlowSchedule, puede ser necesario crear una cuenta. Las cuentas se sujetan a las siguientes condiciones:</p>

                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Información de registro:</strong> Debe proporcionar información precisa y válida durante el proceso de registro, incluyendo nombre y dirección de correo electrónico.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Responsabilidad de la cuenta:</strong> Usted es exclusivamente responsable de mantener la confidencialidad de su contraseña y de todas las actividades que ocurran bajo su cuenta.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Seguridad de la cuenta:</strong> Debe notificar inmediatamente cualquier uso no autorizado de su cuenta o sospecha de compromiso de seguridad.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span><strong>Recuperación de cuenta:</strong> El proceso de restablecimiento de contraseña utiliza enlaces temporales y seguros, pero requiere acceso al correo electrónico asociado a la cuenta.</li>
                                </ul>

                                <p>FlowSchedule no será responsable por ninguna pérdida o daño derivado de su incapacidad para mantener la seguridad de sus credenciales de acceso.</p>
                            </div>
                        </section>

                        <section id="propiedad-intelectual" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">5</span>
                                <span class="terms-section__title-text">Propiedad Intelectual</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule y todo su contenido original, código fuente, documentación y características son propiedad exclusiva de Benjamin King. El proyecto está protegido por las leyes aplicables de derechos de autor y propiedad intelectual.</p>

                                <p>El código fuente de FlowSchedule está disponible públicamente en repositorios de código abierto. Se permite su consulta, estudio y referencia de acuerdo con las licencias respectivas que lo rigen.</p>

                                <p>Queda expresamente prohibido:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Eliminar o modificar avisos de derechos de autor o propiedad intelectual</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Reproducir, distribuir o comunicar públicamente el proyecto o partes sustanciales de él con fines comerciales sin autorización</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Utilizar el proyecto como base para crear servicios o productos competitivos sin el debido reconocimiento y autorización</li>
                                </ul>

                                <p>Se le otorga una licencia limitada, no exclusiva, intransferible y revocable para acceder y usar FlowSchedule exclusivamente para sus propios fines personales, educativos y de demostración.</p>
                            </div>
                        </section>

                        <section id="limitacion-responsabilidad" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">6</span>
                                <span class="terms-section__title-text">Limitación de Responsabilidad</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule se proporciona "tal cual" y "según esté disponible" sin garantías de ningún tipo, ya sean expresas o implícitas.</p>

                                <p>En la máxima medida permitida por la ley aplicable, en ningún caso el responsable será responsable ante usted por ningún daño indirecto, incidental, especial, consequente o punitivo, incluyendo sin limitación pérdida de oportunidades, datos, uso o otras pérdidas intangibles, resultantes de:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Su acceso o uso o incapacidad para acceder o usar el proyecto;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Cualquier comportamiento o contenido de terceros que utilice el proyecto;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Cualquier interrupción, error, retraso o funcionamiento defectuoso del proyecto;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>El acceso no autorizado, uso o alteración de sus transmisiones o contenido.</li>
                                </ul>

                                <p>El proyecto no garantiza que:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>El servicio esté libre de errores o interrupciones;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>El servicio sea compatible con todos los dispositivos, navegadores o sistemas operativos;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>El servicio esté disponible de forma continua e ininterrumpida;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>Todas las funcionalidades anunciadas estén completamente implementadas y operativas en todo momento.</li>
                                </ul>

                                <p>Como proyecto de demostración, FlowSchedule puede estar sujeto a cambios, actualizaciones, modificaciones o incluso eliminación sin previo aviso, según las circunstancias de desarrollo y aprendizaje del autor.</p>
                            </div>
                        </section>

                        <section id="modificacion-terminos" class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">7</span>
                                <span class="terms-section__title-text">Modificación de los Términos</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>El responsable se reserva el derecho, a su entera discreción, de modificar o reemplazar estos Términos de Uso en cualquier momento. Dado que FlowSchedule es un proyecto de demostración y aprendizaje, los términos pueden actualizarse frecuentemente para reflejar cambios en el proyecto o en las circunstancias legales aplicables.</p>

                                <p>Si una revisión es significativa, intentaremos proporcionar notificación razonable a través de los canales disponibles del proyecto. Lo que constituye un cambio significativo será determinado a nuestra entera discreción.</p>

                                <p>Al continuar accediendo o utilizando nuestro proyecto después de que esas revisiones entren en vigor, usted acepta estar sujeto a los términos revisados.</p>
                            </div>
                        </section>

                        <section id="contacto-terminos" class="terms-box terms-contact" style="scroll-margin-top: 6rem;">
                            <div class="terms-contact__head">
                                <h2 class="terms-contact__title">¿Tiene alguna duda sobre estos términos y condiciones?</h2>
                                <p class="terms-contact__text">Para consultas relacionadas con estos Términos de Uso, la Política de Privacidad o el proyecto FlowSchedule en general, puede contactar a través de:</p>
                            </div>

                            <dl class="terms-contact__list">
                                <div class="terms-contact__item">
                                    <dt class="terms-contact__label">Correo electrónico</dt>
                                    <dd class="terms-contact__value">
<a href="mailto:{{ $responsable['email'] }}" class="terms-contact__link" style="color: #000000;">
    {{ $responsable['email'] }}
</a>                                    </dd>
                                </div>
                            </dl>
                        </section>

                    </article>
                </div>

                            <!-- Enlace de regreso -->
                        <div class="terms-noprint">
                            <a href="{{ route('help') }}" class="terms-btn">
                                <svg class="terms-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
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