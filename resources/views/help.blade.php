<x-public-layout>
    <x-slot name="title">Ayuda y Soporte | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/help.css') }}">
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

    // Pasos de "Cómo funciona"
    $pasos = [
        [
            'titulo' => 'Seleccione un día',
            'texto'  => 'Haga clic en cualquier día del calendario para abrir el panel de edición de turnos. Los días del mes actual se ven con opacidad completa y los de otros meses, más tenues.',
        ],
        [
            'titulo' => 'Añada la información',
            'texto'  => 'En el panel puede completar:',
            'items'  => [
                ['Nombre del calendario', 'título breve del turno (máx. 60 caracteres).'],
                ['Nota', 'descripción detallada (máx. 200 caracteres).'],
                ['Color', 'identifica el turno de un vistazo.'],
                ['Dibujo', 'bocetos o diagramas sencillos.'],
            ],
        ],
        [
            'titulo' => 'Se guarda solo',
            'texto'  => 'Los cambios se guardan automáticamente al salir del panel o al hacer clic en cualquier otra parte del calendario. Un indicador en la parte superior muestra si se está guardando, si se guardó correctamente o si hubo un error.',
        ],
    ];

    // Características (icono = trazado SVG)
    $caracteristicas = [
        [
            'titulo' => 'Navegación',
            'texto'  => 'Use las flechas de la parte superior para moverse entre meses y años, o pulse «Hoy» para volver a la fecha actual.',
            'icono'  => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        ],
        [
            'titulo' => 'Números de semana',
            'texto'  => 'Cada semana muestra su número en el margen izquierdo, siguiendo la norma ISO 8601.',
            'icono'  => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14',
        ],
        [
            'titulo' => 'Día actual destacado',
            'texto'  => 'El día de hoy aparece con fondo azul y texto blanco para localizarlo al instante.',
            'icono'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        ],
        [
            'titulo' => 'Información del turno',
            'texto'  => 'Al seleccionar un día con turno asignado verá el título, la nota (hasta 2 líneas) y un indicador si existe un dibujo.',
            'icono'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'titulo' => 'Colores personalizados',
            'texto'  => 'Asigne un color distinto a cada tipo de turno para organizar el calendario visualmente.',
            'icono'  => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
        ],
        [
            'titulo' => 'Notas y dibujos',
            'texto'  => 'Añada una descripción o un boceto a cada turno con el pad de dibujo integrado.',
            'icono'  => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        ],
    ];

    // Preguntas frecuentes
    $preguntas = [
        [
            'pregunta'  => 'El calendario no muestra mis turnos',
            'respuesta' => 'Compruebe que está viendo el mes correcto. Los turnos solo aparecen en las fechas concretas a las que fueron asignados.',
        ],
        [
            'pregunta'  => 'No puedo guardar los cambios',
            'respuesta' => 'Compruebe su conexión a internet y que no ha superado el límite de caracteres: 60 en el título y 200 en la nota.',
        ],
        [
            'pregunta'  => 'El panel de edición no aparece',
            'respuesta' => 'Vuelva a hacer clic en el día que desea editar. Si el problema continúa, recargue la página.',
        ],
    ];
    @endphp

    <div class="help-page">
        <div class="help-container">

            <!-- Cabecera -->
            <header class="help-box">
                <div class="help-hero__body">
                    <h1 class="help-hero__title">Ayuda y soporte</h1>
                    <p class="help-hero__lead">
                        Todo lo que necesita saber para usar FlowSchedule, la plataforma de gestión y planificación.
                    </p>

                    <div class="help-hero__actions">
                        <a href="#como-funciona" class="help-btn help-btn--primary">Ver cómo funciona</a>
                        <a href="{{ route('contacto') }}" class="help-btn">Contactar con soporte</a>
                    </div>
                </div>

                <nav aria-label="Secciones de esta página" class="help-jump">
                    <span class="help-jump__label">Ir a</span>
                    <a href="#acerca" class="help-jump__link">Qué es</a>
                    <a href="#como-funciona" class="help-jump__link">Cómo funciona</a>
                    <a href="#caracteristicas" class="help-jump__link">Características</a>
                    <a href="#preguntas" class="help-jump__link">Preguntas frecuentes</a>
                    <a href="#contacto" class="help-jump__link">Contacto</a>
                </nav>
            </header>

            <!-- Qué es FlowSchedule -->
            <section id="acerca" class="help-section help-box" aria-labelledby="acerca-titulo">
                <div class="help-section__head">
                    <h2 id="acerca-titulo" class="help-section__title">Qué es FlowSchedule</h2>
                    <p class="help-section__lead">Una herramienta para tener toda la planificación del equipo en un solo lugar.</p>
                </div>

                <div class="help-about">
                    <p class="help-about__main">
                        FlowSchedule es una plataforma diseñada para facilitar la planificación, la organización y la gestión, tanto personales como laborales. Permite visualizar calendarios, organizar horarios, gestionar turnos y mantener bajo control las diferentes actividades y eventos de una organización desde un único lugar.
                    </p>
                    <p class="help-about__side">
                        Con una interfaz clara y sencilla, facilita la coordinación de los equipos y mejora la productividad gracias a una planificación accesible para todos.
                    </p>
                </div>
            </section>

            <!-- Cómo funciona -->
            <section id="como-funciona" class="help-section help-box" aria-labelledby="como-funciona-titulo">
                <div class="help-section__head">
                    <h2 id="como-funciona-titulo" class="help-section__title">Cómo funciona</h2>
                    <p class="help-section__lead">Registrar un turno lleva solo tres pasos.</p>
                </div>

                <ol class="help-steps">
                    @foreach ($pasos as $paso)
                    <li class="help-step">
                        <span class="help-step__num" aria-hidden="true">{{ $loop->iteration }}</span>
                        <h3 class="help-step__title">{{ $paso['titulo'] }}</h3>
                        <p class="help-step__text">{{ $paso['texto'] }}</p>

                        @isset($paso['items'])
                        <ul class="help-step__list">
                            @foreach ($paso['items'] as $item)
                            <li><strong>{{ $item[0] }}:</strong> {{ $item[1] }}</li>
                            @endforeach
                        </ul>
                        @endisset
                    </li>
                    @endforeach
                </ol>
            </section>

            <!-- Características -->
            <section id="caracteristicas" class="help-section" aria-labelledby="caracteristicas-titulo">
                <div class="help-box">
                    <div class="help-section__head">
                        <h2 id="caracteristicas-titulo" class="help-section__title">Características principales</h2>
                        <p class="help-section__lead">Lo que puede hacer desde el calendario.</p>
                    </div>

                    <div class="help-features">
                        @foreach ($caracteristicas as $c)
                        <div class="help-feature">
                            <span class="help-feature__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icono'] }}" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="help-feature__title">{{ $c['titulo'] }}</h3>
                                <p class="help-feature__text">{{ $c['texto'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="help-tip">
                    <p class="help-tip__title">Consejo</p>
                    <p class="help-tip__text">
                        Use colores consistentes para tipos similares de turnos: por ejemplo, azul para reuniones internas, verde para capacitaciones y naranja para fechas límite. Así el calendario se lee de un vistazo.
                    </p>
                </div>
            </section>

            <!-- Preguntas frecuentes -->
            <section id="preguntas" class="help-section help-box" aria-labelledby="preguntas-titulo">
                <div class="help-section__head">
                    <h2 id="preguntas-titulo" class="help-section__title">Preguntas frecuentes</h2>
                    <p class="help-section__lead">Soluciones rápidas a los problemas más comunes.</p>
                </div>

                <div class="help-faq">
                    @foreach ($preguntas as $p)
                    <details class="help-faq__item" @if ($loop->first) open @endif>
                        <summary class="help-faq__question">
                            {{ $p['pregunta'] }}
                            <svg class="help-faq__chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <p class="help-faq__answer">{{ $p['respuesta'] }}</p>
                    </details>
                    @endforeach
                </div>
            </section>

            <!-- Contacto -->
            <section id="contacto" class="help-section help-contact" aria-labelledby="contacto-titulo">
                <div>
                    <h2 id="contacto-titulo" class="help-contact__title">¿Necesita más ayuda?</h2>
                    <p class="help-contact__text">
                        Si no encuentra la respuesta que busca, escríbanos o llámenos. Le atenderemos lo antes posible.
                    </p>
                </div>

                <ul class="help-contact__list">
                    <li class="help-contact__item">
                        <span class="help-contact__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="help-contact__label">Correo electrónico</p>
                            <p class="help-contact__value">
                                <a href="mailto:{{ $contacto['email'] }}" class="help-contact__link">{{ $contacto['email'] }}</a>
                            </p>
                        </div>
                    </li>

                    <li class="help-contact__item">
                        <span class="help-contact__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z" />
                            </svg>
                        </span>
                        <div>
                            <p class="help-contact__label">Teléfono</p>
                            <p class="help-contact__value">
                                <a href="tel:{{ $telefonoLink }}" class="help-contact__link">{{ $contacto['telefono'] }}</a>
                            </p>
                        </div>
                    </li>

                    <li class="help-contact__item">
                        <span class="help-contact__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="help-contact__label">Horario de atención</p>
                            <p class="help-contact__value">{{ $contacto['horario'] }}</p>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Documentos legales -->
            <div class="help-legal">
                <span class="help-legal__label">Documentos legales</span>
                <a href="{{ route('terminos') }}" class="help-btn">Términos y Condiciones</a>
                <a href="{{ route('privacidad') }}" class="help-btn">Política de Privacidad</a>
            </div>

        </div>
    </div>

    @include('layouts.footer')

</x-public-layout>