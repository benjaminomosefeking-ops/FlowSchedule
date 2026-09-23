<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FlowSchedule' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=Kranky&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <style>
        :root{--ink:#eef1f6;--ink-muted:rgba(238,241,246,.64);--ink-dim:rgba(238,241,246,.4);--night:#080f1a;--panel:#101c2e;--line:rgba(238,241,246,.14);--orange:#f47920;--orange-soft:#ffb066;--green:#7de3a8;--display:'Kranky',cursive;--body:Arial,sans-serif;--mono:'JetBrains Mono',monospace}
        *{box-sizing:border-box}
        html{min-height:100%;background:var(--night)}
        body{min-height:100vh;margin:0;background:var(--night);background-image:radial-gradient(ellipse 70% 55% at 20% 0%,rgba(67,97,238,.16),transparent 70%),radial-gradient(circle,rgba(255,255,255,.045) 1px,transparent 1px);background-size:auto,26px 26px;color:var(--ink);font-family:var(--body);-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}
        button,input{font:inherit}
        .auth-shell{width:min(1120px,100%);min-height:100vh;margin:auto;padding:28px 32px;display:grid;grid-template-rows:auto 1fr;gap:38px}
        .auth-nav{display:flex;align-items:center;justify-content:space-between}
        .auth-brand{display:flex;align-items:center;gap:10px;font:400 1.4rem var(--display)}
        .auth-brand svg{width:30px;height:30px;color:var(--orange)}
        .auth-back{font-size:.88rem;color:var(--ink-muted);transition:color .2s ease}
        .auth-back:hover{color:var(--orange-soft)}
        .auth-main{display:grid;grid-template-columns:minmax(0,1fr) minmax(360px,440px);gap:90px;align-items:center;padding:0 5% 8vh}
        .auth-intro{max-width:470px}
        .auth-eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--orange-soft);font:600 .72rem var(--mono);letter-spacing:.06em;text-transform:uppercase}
        .auth-eyebrow:before{content:'';width:7px;height:7px;border-radius:50%;background:var(--orange);box-shadow:0 0 10px rgba(244,121,32,.7)}
        .auth-intro h1{margin:20px 0 18px;font:400 clamp(2.8rem,5vw,4.5rem)/1.02 var(--display);letter-spacing:0}
        .auth-intro p{max-width:410px;margin:0;color:var(--ink-muted);font-size:1rem;line-height:1.7}
        .auth-aside-note{display:flex;align-items:center;gap:10px;margin-top:38px;color:var(--ink-dim);font:600 1.28rem var(--display);transform:rotate(-2deg);transform-origin:left center}
        .auth-aside-note svg{width:25px;height:25px;color:var(--orange);flex:none}
        .auth-card{position:relative;padding:34px 36px;background:rgba(16,28,46,.92);border:1px solid var(--line);border-radius:14px;box-shadow:0 28px 70px rgba(0,0,0,.35)}
        .auth-card:before{content:'';position:absolute;inset:6px;border:1px dashed rgba(255,176,102,.18);border-radius:10px;pointer-events:none}
        .auth-card>*{position:relative}
        .auth-card h2{margin:0 0 8px;font:400 2.15rem var(--display)}
        .auth-card-lead{margin:0 0 26px;color:var(--ink-muted);font-size:.92rem;line-height:1.5}
        .auth-form{display:grid;gap:17px}
        .auth-field{display:grid;gap:7px}
        .auth-label{font-size:.78rem;font-weight:700;color:var(--ink-muted)}
        .auth-input{width:100%;padding:12px 13px;border:1px solid var(--line);border-radius:6px;background:rgba(8,15,26,.68);color:var(--ink);outline:none;transition:border-color .2s ease,box-shadow .2s ease}
        .auth-input:focus{border-color:var(--orange-soft);box-shadow:0 0 0 3px rgba(244,121,32,.12)}
        .auth-input::placeholder{color:var(--ink-dim)}
        .auth-error{margin:0;color:#ff9b8b;font-size:.78rem}
        .auth-row{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:2px}
        .auth-check{display:flex;align-items:center;gap:8px;color:var(--ink-muted);font-size:.8rem}
        .auth-check input{accent-color:var(--orange)}
        .auth-link{color:var(--orange-soft);font-size:.8rem;border-bottom:1px dashed rgba(255,176,102,.55)}
        .auth-link:hover{color:var(--ink)}
        .auth-submit{width:100%;padding:13px 20px;border:0;border-radius:6px;background:linear-gradient(135deg,var(--orange),#ff9a4d);color:var(--night);font-weight:700;cursor:pointer;box-shadow:0 9px 24px rgba(244,121,32,.22);transition:transform .2s ease,box-shadow .2s ease}
        .auth-submit:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(244,121,32,.36)}
        .auth-switch{margin:25px 0 0;text-align:center;color:var(--ink-dim);font-size:.82rem}
        .auth-status{margin:0 0 18px;padding:10px 12px;border:1px solid rgba(125,227,168,.28);border-radius:6px;color:var(--green);background:rgba(125,227,168,.08);font-size:.8rem}
        @media(max-width:800px){.auth-shell{padding:22px 20px;gap:28px}.auth-main{grid-template-columns:1fr;gap:28px;padding:0 0 35px}.auth-intro{max-width:600px}.auth-intro h1{font-size:3.4rem}.auth-aside-note{margin-top:22px}.auth-card{max-width:520px;width:100%;padding:30px 25px}}
        @media(max-width:480px){.auth-back{font-size:0}.auth-back:after{content:'Volver a inicio';font-size:.86rem}.auth-intro h1{font-size:2.8rem}.auth-card h2{font-size:1.9rem}.auth-row{align-items:flex-start;flex-direction:column}}
    </style>
<div class="auth-shell">
    <header class="auth-nav">
        <a href="{{ url('/') }}" class="auth-brand" aria-label="Volver a FlowSchedule">
            <img src="{{ asset('images/logo.png') }}" alt="FlowSchedule logo" class="brand-mark">
            <span>FlowSchedule</span>
        </a>
        <a class="auth-back" href="{{ url('/') }}">← Volver a inicio</a>
    </header>
    <main class="auth-main">
        <section class="auth-intro" aria-labelledby="auth-intro-title">
            <span class="auth-eyebrow">Planifica con claridad</span>
            <h1 id="auth-intro-title">Tu equipo,<br>en buen ritmo.</h1>
            <p>Organiza turnos, respeta descansos y toma decisiones con una vista clara de todo lo que ocurre.</p>
            <div class="auth-aside-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 2 14.5 8.5 21 9.3 16 13.8 17.4 20.5 12 17 6.6 20.5 8 13.8 3 9.3 9.5 8.5Z"/></svg>menos caos, más equipo</div>
        </section>
        <section class="auth-card" aria-label="Formulario de autenticación">{{ $slot }}</section>
    </main>
</div>
</body>
</html>
