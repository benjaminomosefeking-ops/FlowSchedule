<x-public-layout>
  <x-slot name="title">FlowSchedule — Tu horario personal, sin complicaciones</x-slot>

  @include('layouts.navigation')

  <main class="wrap">

    <!-- ══════════ HERO ══════════ -->
    <section class="hero" id="hero">

      <div class="hero-annotation" aria-hidden="true">
        <span class="anno-line" id="anno-line"></span><span class="anno-caret"></span>
        <div class="anno-stamp" id="anno-stamp">0</div>
        <div class="anno-sig" id="anno-sig">— revisado</div>
      </div>

      <div class="hero-copy" data-reveal>
        <span class="eyebrow blue">Horario personal · semana 34</span>
        <h1>
          Tu tiempo organizado,<br>
          <span class="strike">sin</span> <span class="accent">agobios.</span>
        </h1>
        <p class="lead">FlowSchedule organiza tus turnos y tareas respetando tu energía, descansos y prioridades — y valida cada cambio al instante, sin que tengas que revisarlo tú.</p>
        <div class="hero-cta">
          <a href="#probarlo" class="btn btn-primary">Probar gratis</a>
          <a href="#como-funciona" class="more">Ver cómo funciona →</a>
        </div>
        <div class="hero-meta">
          <span>Solo para ti</span>
          <span>Totalmente gratis</span>
          <span>En español</span>
        </div>
      </div>

      <div class="hero-visual" data-reveal>
        <div class="sheet">
          <span class="pin" aria-hidden="true"></span>

          {{-- ── Cabecera con navegación de semana ── --}}
          <div class="sheet-head">
            <div class="sheet-head-left">
              <button type="button" class="sheet-nav" aria-label="Semana anterior">‹</button>
              <span class="sheet-title">Mi horario</span>
              <button type="button" class="sheet-nav" aria-label="Semana siguiente">›</button>
            </div>
            <span class="sheet-week">sem. 34 · 18–22 ago</span>
          </div>

          {{-- ── Calendario ── --}}
          <div class="schedule">
            <div class="col-head"></div>
            <div class="col-head">Lun</div>
            <div class="col-head">Mar</div>
            <div class="col-head">Mié</div>
            <div class="col-head">Jue</div>
            <div class="col-head col-head-today">Vie</div>

            {{-- TRABAJO --}}
            <div class="row-head">
              <span class="row-label">Trabajo</span>
              <span class="row-sub">8h/día</span>
            </div>
            <div class="cell"><span class="shift shift-blue"><span class="shift-name">Proyecto A</span><span class="shift-time">09—12</span></span></div>
            <div class="cell"><span class="shift shift-blue"><span class="shift-name">Reuniones</span><span class="shift-time">13—15</span></span></div>
            <div class="cell"><span class="shift shift-blue"><span class="shift-name">Tareas admin</span><span class="shift-time">15—17</span></span></div>
            <div class="cell">
              <span class="shift shift-red">
                <span class="shift-name">Entrega</span>
                <span class="shift-time">10—13</span>
              </span>
              <span class="shift-note">¡deadline!</span>
            </div>
            <div class="cell cell-hl"><span class="shift shift-yellow"><span class="shift-name">Ejercicio</span><span class="shift-time">18—19</span></span></div>

            {{-- ESTUDIO --}}
            <div class="row-head">
              <span class="row-label">Estudio</span>
              <span class="row-sub">2h/día</span>
            </div>
            <div class="cell"></div>
            <div class="cell"><span class="shift shift-ink"><span class="shift-name">Curso online</span><span class="shift-time">19—21</span></span></div>
            <div class="cell"><span class="shift shift-ink"><span class="shift-name">Lectura</span><span class="shift-time">21—22</span></span></div>
            <div class="cell"><span class="shift shift-ink"><span class="shift-name">Repaso</span><span class="shift-time">08—09</span></span></div>
            <div class="cell"></div>

            {{-- DESCANSO --}}
            <div class="row-head">
              <span class="row-label">Descanso</span>
              <span class="row-sub">esencial</span>
            </div>
            <div class="cell"><span class="shift shift-yellow"><span class="shift-name">Caminata</span><span class="shift-time">07—08</span></span></div>
            <div class="cell"><span class="shift shift-yellow"><span class="shift-name">Meditación</span><span class="shift-time">20—20:15</span></span></div>
            <div class="cell"></div>
            <div class="cell"><span class="shift shift-yellow"><span class="shift-name">Estiramiento</span><span class="shift-time">12:30—13</span></span></div>
            <div class="cell"></div>

            {{-- PERSONAL --}}
            <div class="row-head">
              <span class="row-label">Personal</span>
              <span class="row-sub">flexible</span>
            </div>
            <div class="cell"><span class="shift shift-red"><span class="shift-name">Café con Ana</span><span class="shift-time">17:30—18</span></span></div>
            <div class="cell"></div>
            <div class="cell"><span class="shift shift-red"><span class="shift-name">Cine</span><span class="shift-time">21—23</span></span></div>
            <div class="cell"></div>
            <div class="cell"><span class="shift shift-red"><span class="shift-name">Cena familiar</span><span class="shift-time">20—22</span></span></div>
          </div>

          {{-- ── Tira de estadísticas ── --}}
          <div class="sheet-stats">
            <div class="stat">
              <span class="stat-num">38h</span>
              <span class="stat-lbl">Trabajo</span>
            </div>
            <div class="stat">
              <span class="stat-num">10h</span>
              <span class="stat-lbl">Estudio</span>
            </div>
            <div class="stat">
              <span class="stat-num">6h</span>
              <span class="stat-lbl">Descanso</span>
            </div>
            <div class="stat">
              <span class="stat-num">7h</span>
              <span class="stat-lbl">Personal</span>
            </div>
          </div>

          {{-- ── Footer con leyenda ── --}}
          <div class="sheet-footer">
            <div class="sheet-legend">
              <span class="legend-item"><i class="dot dot-blue"></i>Trabajo</span>
              <span class="legend-item"><i class="dot dot-ink"></i>Estudio</span>
              <span class="legend-item"><i class="dot dot-yellow"></i>Descanso</span>
              <span class="legend-item"><i class="dot dot-red"></i>Personal</span>
            </div>
            <span class="approved">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m5 12 5 5 9-11" />
              </svg>
              Sin sobrecarga
            </span>
          </div>
        </div>

        <div class="sticky" aria-hidden="true">recuerda: los bloques personales son sagrados!!!</div>
      </div>
    </section>

    <!-- ══════════ SOCIAL  ══════════ -->
    <section class="social-proof" data-reveal>
      <div class="proof-content">
        <span class="proof-label">Gestiona tu tiempo de forma inteligente.</span>
      </div>
    </section>

    <!-- ══════════ BENEFICIOS ══════════ -->
    <section id="beneficios" class="section">
      <div class="section-head" data-reveal>
        <span class="eyebrow">Beneficios</span>
        <h2>Menos tiempo organizando,<br>más tiempo viviendo.</h2>
        <p>Organizador personal de horarios y tareas para quienes valoran su tiempo. FlowSchedule se encarga de la lógica; tú decides qué hacer y cuándo.</p>
      </div>
      <div class="benefits">
        <div class="benefit" data-reveal data-color="blue">
          <div class="num">01</div>
          <div>
            <h3>Equilibrio natural</h3>
            <p>Distribuye tus actividades respetando tus niveles de energía a lo largo del día y la semana.</p>
            <span class="tag">energía optimizada</span>
          </div>
        </div>
        <div class="benefit" data-reveal data-color="red">
          <div class="num">02</div>
          <div>
            <h3>Descansos protejidos</h3>
            <p>El sistema controla que tengas suficientes pausas y tiempo de recuperación antes de permitirte sobrecargarte.</p>
            <span class="tag">autocuidado garantizado</span>
          </div>
        </div>
        <div class="benefit" data-reveal data-color="yellow">
          <div class="num">03</div>
          <div>
            <h3>Enfoque mejorado</h3>
            <p>Solo se muestran como disponibles los bloques de tiempo que coinciden con tus verdaderas prioridades y objetivos.</p>
            <span class="tag">prioridades claras</span>
          </div>
        </div>
        <div class="benefit" data-reveal data-color="ink">
          <div class="num">04</div>
          <div>
            <h3>Ajustes al instante</h3>
            <p>Arrastra, suelta y reprograma. Cada cambio se valida automáticamente en el momento, sin revisiones mentales agotadoras.</p>
            <span class="tag">flexibilidad consciente</span>
          </div>
        </div>
      </div>

      <!-- ══════════ CÓMO FUNCIONA ══════════ -->
      <section id="como-funciona" class="section">
        <div class="section-head" data-reveal>
          <span class="eyebrow red">Cómo funciona</span>
          <h2>De cero a tu horario organizado<br>en tres pasos.</h2>
          <p>Sin instalaciones, sin formaciones, llamadas comerciales. Lo configuras mientras se te hace el café.</p>
        </div>
        <div class="steps">
          <div class="step" data-reveal>
            <span class="pinhole" aria-hidden="true"></span>
            <h3><span class="n">01</span> Define tu semana</h3>
            <h4>Bloques de tiempo, prioridades, descansos</h4>
            <p>Configura tus compromisos fijos, nivel de energía esperado y objetivos personales.</p>
            <div class="footer-line">~ 2 minutos</div>
          </div>
          <div class="step" data-reveal>
            <span class="pinhole" aria-hidden="true"></span>
            <h3><span class="n">02</span> Añade tus actividades</h3>
            <h4>Trabajo, estudio, autocuidado, ocio</h4>
            <p>Incluye tus tareas con su duración estimada, importancia y momento preferido del día.</p>
            <div class="footer-line">sin agobiarse, sin olvidar nada</div>
          </div>
          <div class="step" data-reveal>
            <span class="pinhole" aria-hidden="true"></span>
            <h3><span class="n">03</span> Fluye con tu plan</h3>
            <h4>Adaptación inteligente</h4>
            <p>Visualiza tu horario óptimo. Cada ajuste se revisa automáticamente: descansos, límites de energía y coherencia con objetivos.</p>
            <div class="footer-line">en armonía contigo mismo</div>
          </div>
        </div>
      </section>

      <!-- ══════════ CTA FINAL ══════════ -->
      <section id="probarlo" class="cta-final">
        <div class="cta-slip" data-reveal>
          <div>
            <span class="eyebrow">Empieza hoy</span>
            <h2>Tu primer horario organizado,<br>esta misma semana.</h2>
            <p>Pruébalo gratis. Es un proyecto real construido con Laravel y Arquitectura Hexagonal. Explóralo sin compromiso.</p>
            <div class="cta-small">Sin tarjeta · Sin compromiso</div>
          </div>
          <div class="cta-actions" id="cta-actions">
            <a href="{{ route('register') }}" class="btn btn-primary">Registrarme</a>
            <a href="#como-funciona" class="btn btn-ghost">Tomar recorrido</a>
          </div>
        </div>
      </section>
    </main>

    @include('layouts.footer')

    <script>
      (function() {
        'use strict';

        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ═══ signature moment: red-pen annotation typing out ═══ */
        function runAnnotation() {
          var el = document.getElementById('anno-line');
          var stamp = document.getElementById('anno-stamp');
          var sig = document.getElementById('anno-sig');
          if (!el) return;
          var text = 'Bloques libres esta semana: ';
          var i = 0;
          if (reduceMotion) {
            el.textContent = text + '0';
            if (stamp) stamp.classList.add('is-on');
            if (sig) sig.classList.add('is-on');
            return;
          }

          function typeNext() {
            if (i <= text.length) {
              el.textContent = text.slice(0, i);
              i++;
              setTimeout(typeNext, 28 + Math.random() * 22);
            } else {
              setTimeout(function() {
                el.textContent = text;
                if (stamp) stamp.classList.add('is-on');
                setTimeout(function() {
                  if (sig) sig.classList.add('is-on');
                }, 350);
              }, 240);
            }
          }
          typeNext();
        }

        /* ═══ scroll reveal ═══ */
        function initReveal() {
          var els = document.querySelectorAll('[data-reveal]');
          if (reduceMotion) {
            els.forEach(function(el) {
              el.classList.add('in');
            });
            return;
          }
          var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting) {
                entry.target.classList.add('in');
                io.unobserve(entry.target);
              }
            });
          }, {
            threshold: 0.15
          });
          els.forEach(function(el) {
            io.observe(el);
          });
        }

        /* ═══ NOTA: el menú móvil se controla desde layouts/navigation.blade.php.
           No añadir aquí otro toggle para evitar listeners duplicados. ═══ */

        window.addEventListener('load', function() {
          initReveal();
          runAnnotation();
        });

        document.fonts && document.fonts.ready.then(function() {
          // no-op
        });

      })();
    </script>
  </x-public-layout>