<x-public-layout>
    <x-slot name="title">Términos y Condiciones | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/terms/ter.css') }}">
    @endpush

    @include('layouts.navigation')

    @php
    // Información de contacto real del proyecto
    $contacto = [
    'email' => 'soporte@flowscheduler.com', // Este email puede ser inventado según las instrucciones
    'telefono'=> '+34 900 000 000', // Número de contacto ficticio según las instrucciones
    'horario' => 'Lunes a viernes, de 9:00 a 18:00',
    ];
    $telefonoLink = preg_replace('/[^\d+]/', '', $contacto['telefono']);

    // Fecha de la última revisión legal
    $ultimaActualizacion = \Carbon\Carbon::parse('2026-09-20')->locale('es');
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
                        Estos Términos de Servicio ("Términos") rigen su uso de FlowSchedule, una plataforma de gestión y planificación.
                        Al acceder o usar FlowSchedule, usted acepta cumplir con estos Términos y nuestra Política de Privacidad.
                    </p>

                </div>

                <!-- Resumen rápido -->
                <div class="terms-summary">
                    <div class="terms-summary__grid">
                        <div class="terms-summary__item">
                            <h2 class="terms-summary__title">Uso del servicio</h2>
                            <p class="terms-summary__text">FlowSchedule se proporciona tal cual está disponible para uso personal y organizacional.</p>
                        </div>
                        <div class="terms-summary__item">
                            <h2 class="terms-summary__title">Cuenta de usuario</h2>
                            <p class="terms-summary__text">Usted es responsable de mantener la confidencialidad de su cuenta y contraseña.</p>
                        </div>
                        <div class="terms-summary__item">
                            <h2 class="terms-summary__title">Limitación</h2>
                            <p class="terms-summary__text">No garantizamos que el servicio esté libre de errores o interrupciones.</p>
                        </div>
                    </div>
                    <p class="terms-summary__note">
                        Este resumen es orientativo y no sustituye a los términos completos que figuran a continuación.
                    </p>
                </div>
            </header>

            <div class="terms-layout" style="max-height: calc(100vh - 200px); overflow-y: auto; padding-bottom: 2rem;">
                <!-- Índice (móvil: desplegable) -->
                <details class="terms-box terms-toc-mobile terms-noprint">
                    <summary class="terms-toc-mobile__summary">
                        Índice de contenidos
                        <svg class="terms-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <nav aria-label="Índice de contenidos" class="terms-toc-mobile__nav">
                        <ul class="terms-toc__list">
                            <li><a href="#aceptacion" data-toc="aceptacion" class="terms-toc__link"><span class="terms-toc__num">1</span><span>Aceptación de los términos</span></a></li>
                            <li><a href="#descripcion-servicio" data-toc="descripcion-servicio" class="terms-toc__link"><span class="terms-toc__num">2</span><span>Descripción del servicio</span></a></li>
                            <li><a href="#cuenta-usuario" data-toc="cuenta-usuario" class="terms-toc__link"><span class="terms-toc__num">3</span><span>Cuenta de usuario</span></a></li>
                            <li><a href="#uso-aceptable" data-toc="uso-aceptable" class="terms-toc__link"><span class="terms-toc__num">4</span><span>Uso aceptable</span></a></li>
                            <li><a href="#propiedad-intelectual" data-toc="propiedad-intelectual" class="terms-toc__link"><span class="terms-toc__num">5</span><span>Propiedad intelectual</span></a></li>
                            <li><a href="#limitacion-responsabilidad" data-toc="limitacion-responsabilidad" class="terms-toc__link"><span class="terms-toc__num">6</span><span>Limitación de responsabilidad</span></a></li>
                            <li><a href="#ley-aplicable" data-toc="ley-aplicable" class="terms-toc__link"><span class="terms-toc__num">7</span><span>Ley aplicable</span></a></li>
                            <li><a href="#modificacion-terminos" data-toc="modificacion-terminos" class="terms-toc__link"><span class="terms-toc__num">8</span><span>Modificación de los términos</span></a></li>
                            <li><a href="#contacto" data-toc="contacto" class="terms-toc__link"><span class="terms-toc__num" aria-hidden="true">·</span><span>Contacto</span></a></li>
                        </ul>
                    </nav>
                </details>

                <!-- Índice (escritorio: fijo) -->
                <aside class="terms-toc-desktop terms-noprint">
                    <nav aria-label="Índice de contenidos" class="terms-box terms-toc-desktop__nav">
                        <ul class="terms-toc__list">
                            <li><a href="#aceptacion" data-toc="aceptacion" class="terms-toc__link"><span class="terms-toc__num">1</span><span>Aceptación de los términos</span></a></li>
                            <li><a href="#descripcion-servicio" data-toc="descripcion-servicio" class="terms-toc__link"><span class="terms-toc__num">2</span><span>Descripción del servicio</span></a></li>
                            <li><a href="#cuenta-usuario" data-toc="cuenta-usuario" class="terms-toc__link"><span class="terms-toc__num">3</span><span>Cuenta de usuario</span></a></li>
                            <li><a href="#uso-aceptable" data-toc="uso-aceptable" class="terms-toc__link"><span class="terms-toc__num">4</span><span>Uso aceptable</span></a></li>
                            <li><a href="#propiedad-intelectual" data-toc="propiedad-intelectual" class="terms-toc__link"><span class="terms-toc__num">5</span><span>Propiedad intelectual</span></a></li>
                            <li><a href="#limitacion-responsabilidad" data-toc="limitacion-responsabilidad" class="terms-toc__link"><span class="terms-toc__num">6</span><span>Limitación de responsabilidad</span></a></li>
                            <li><a href="#ley-aplicable" data-toc="ley-aplicable" class="terms-toc__link"><span class="terms-toc__num">7</span><span>Ley aplicable</span></a></li>
                            <li><a href="#modificacion-terminos" data-toc="modificacion-terminos" class="terms-toc__link"><span class="terms-toc__num">8</span><span>Modificación de los términos</span></a></li>
                            <li><a href="#contacto" data-toc="contacto" class="terms-toc__link"><span class="terms-toc__num" aria-hidden="true">·</span><span>Contacto</span></a></li>
                        </ul>
                    </nav>
                </aside>

                <!-- Contenido principal -->
                <div class="terms-main">
                    <article class="terms-box terms-article">

                        <section id="aceptacion" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">1</span>
                                <span class="terms-section__title-text">Aceptación de los términos</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Al acceder o usar FlowSchedule, usted acepta estar obligado por estos Términos de Servicio, nuestra Política de Privacidad y cualquier otra política o directriz que pueda aplicarse al uso del servicio. Si no está de acuerdo con alguno de estos términos, por favor no utilice el servicio.</p>

                                <p>Estos Términos se aplican a todos los visitantes, usuarios y otras personas que accedan o utilicen el servicio.</p>
                            </div>
                        </section>

                        <section id="descripcion-servicio" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">2</span>
                                <span class="terms-section__title-text">Descripción del servicio</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule es una plataforma de gestión y planificación que permite a los usuarios organizar horarios, gestionar turnos y mantener bajo control las actividades y eventos personales y laborales desde un único lugar.</p>

                                <p>El servicio incluye funcionalidades como:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Visualización y gestión de calendarios</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Organización de horarios y turnos</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Gestión de tareas y eventos</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Interfaz accesible para la coordinación de equipos</li>
                                </ul>

                                <p><strong>Importante:</strong> FlowSchedule se proporciona "tal cual" y "según esté disponible". No garantizamos que el servicio esté libre de errores, interrumpido, oportuno, seguro o libre de otros daños.</p>
                            </div>
                        </section>

                        <section id="cuenta-usuario" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">3</span>
                                <span class="terms-section__title-text">Cuenta de usuario</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Para acceder a ciertas funciones de FlowSchedule, puede ser necesario crear una cuenta. Usted acepta proporcionar información precisa, actual y completa durante el proceso de registro y actualizar dicha información para mantenerla precisa, actual y completa.</p>

                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        <strong>Responsabilidad de la cuenta:</strong> Usted es totalmente responsable de mantener la confidencialidad de su contraseña y de todas las actividades que ocurran bajo su cuenta.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        <strong>Seguridad de la cuenta:</strong> Deberá notificarnos inmediatamente cualquier uso no autorizado de su cuenta o cualquier otra violación de seguridad.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        <strong>Límites de uso:</strong> Podemos establecer límites en ciertas funcionalidades del servicio sin previo aviso.</li>
                                </ul>

                                <p>No seremos responsables por ninguna pérdida o daño derivado de su incapacidad para cumplir con sus obligaciones de seguridad de la cuenta.</p>
                            </div>
                        </section>

                        <section id="uso-aceptable" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">4</span>
                                <span class="terms-section__title-text">Uso aceptable</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Al utilizar FlowSchedule, usted se compromete a no utilizar el servicio para ningún propósito ilegal o no autorizado. No debe, en el uso del Servicio, violar ninguna ley en su jurisdicción (incluidas pero no limitadas a leyes de derechos de autor).</p>

                                <p>Usted se compromete específicamente a:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        No utilizar FlowSchedule de ninguna manera que pueda dañar, desactivar, sobrecargar o deteriorar el servicio ni interferir con el uso y disfrute del servicio por parte de terceros.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        No intentar obtener acceso no autorizado a ningún componente del servicio, a otras cuentas, sistemas de ordenadores o redes conectadas a ningún servidor del servicio, mediante piratería, extracción de contraseñas o cualquier otro medio.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        No acosar, abusar, amenazar, suplantar o intimidar a otros usuarios del servicio.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        No utilizar el servicio para transmitir o colocar cualquier virus informático, gusano, bomba de lógica u otro material destructivo o disruptivo.</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        No utilizar el servicio para transmitir cualquier material que sea obsceno, ilegal, difamatorio o que invada la privacidad de otro.</li>
                                </ul>
                            </div>
                        </section>

                        <section id="propiedad-intelectual" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">5</span>
                                <span class="terms-section__title-text">Propiedad intelectual</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule y todo su contenido original, características y funcionalidad son propiedad exclusiva de FlowSchedule y sus licenciantes. El servicio está protegido por derechos de autor, marcas comerciales y otras leyes de propiedad intelectual.</p>

                                <p>Usted no podrá copiar, modificar, distribuir, vender o alquilar ninguna parte de nuestro servicio o contenido, ni podrá intentar obtener el código fuente del servicio.</p>

                                <p>Se le concede una licencia limitada, no exclusiva, intransferible y revocable para acceder y usar el servicio únicamente para su propio uso personal y organizacional, siempre que cumpla con estos Términos de Servicio.</p>
                            </div>
                        </section>

                        <section id="limitacion-responsabilidad" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">6</span>
                                <span class="terms-section__title-text">Limitación de responsabilidad</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>En la máxima medida permitida por la ley aplicable, en ningún caso FlowSchedule, sus directores, empleados o agentes serán responsables ante usted por ningún daño indirecto, incidental, especial, consequente o punitivo, incluyendo sin limitación pérdida de beneficios, datos, uso, buena voluntad u otras pérdidas intangibles, resultantes de:</p>
                                <ul class="terms-list">
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Su acceso o uso o incapacidad para acceder o usar el servicio;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Cualquier comportamiento o contenido de cualquier terceros que utilice el servicio;</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        Cualquier contenido obtenido del servicio; y</li>
                                    <li><span class="terms-bullet" aria-hidden="true"></span>
                                        El acceso no autorizado, uso o alteración de sus transmisiones o contenido.</li>
                                </ul>

                                <p>A pesar de cualquier daño que usted pudiera sufrir, la responsabilidad total de FlowSchedule y cualquiera de sus proveedores bajo cualquier disposición de estos términos y su recurso exclusivo por todo lo anterior se limitará al importe realmente pagado por usted a través del servicio o 1,00 EUR si usted no ha comprado nada a través del servicio.</p>

                                <p>FlowSchedule no será responsable de ningún daño, pérdida o lesión causado por hacking, manipulación u otro acceso o alteración no autorizado al servicio.</p>
                            </div>
                        </section>

                        <section id="ley-aplicable" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">7</span>
                                <span class="terms-section__title-text">Ley aplicable</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>Estos Términos se regirán e interpretarán de acuerdo con las leyes de España, sin tener en cuenta sus principios de conflicto de leyes.</p>

                                <p>El uso del servicio tampoco está permitido en ninguna jurisdicción que no dé efecto a todas las disposiciones de estos términos, incluyendo sin limitación esta sección.</p>
                            </div>
                        </section>

                        <section id="modificacion-terminos" data-section class="terms-section">
                            <h2 class="terms-section__title">
                                <span class="terms-num" aria-hidden="true">8</span>
                                <span class="terms-section__title-text">Modificación de los términos</span>
                            </h2>

                            <div class="terms-section__body">
                                <p>FlowSchedule se reserva el derecho, a su entera discreción, de modificar o reemplazar estos Términos en cualquier momento. Si una revisión es material, intentaremos proporcionar al menos 30 días de notificación previa a la entrada en vigor de quaisquier nuevos términos. Lo que constituye un cambio material será determinado a nuestra entera discreción.</p>

                                <p>Al continuar accediendo o utilizando nuestro servicio después de que esas revisiones entren en vigor, usted acepta estar sujeto a los términos revisados.</p>
                            </div>
                        </section>

                        <!-- Contacto -->
                        <section id="contacto" data-section class="terms-box terms-contact" style="scroll-margin-top: 6rem;">
                            <div class="terms-contact__head">
                                <h2 class="terms-contact__title">¿Tiene alguna duda sobre estos términos y condiciones?</h2>
                                <p class="terms-contact__text">Escríbanos o llámenos y le ayudaremos a resolverla.</p>
                            </div>

                            <dl class="terms-contact__list">
                                <div class="terms-contact__item">
                                    <dt class="terms-contact__label">Correo electrónico</dt>
                                    <dd class="terms-contact__value">
                                        <a href="mailto:{{ $contacto['email'] }}" class="terms-contact__link">{{ $contacto['email'] }}</a>
                                    </dd>
                                </div>
                                <div class="terms-contact__item">
                                    <dt class="terms-contact__label">Teléfono</dt>
                                    <dd class="terms-contact__value">
                                        <a href="tel:{{ $telefonoLink }}" class="terms-contact__link">{{ $contacto['telefono'] }}</a>
                                    </dd>
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