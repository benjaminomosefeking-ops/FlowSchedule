<footer>
    <div class="wrap footer-row flex flex-row justify-between items-center">
        <span class="footer-brand">© {{ date('Y') }} FlowSchedule · hecho en España</span>
        <div class="footer-links mt-4 md:mt-0">
            <a href="{{ route('aviso-legal') }}">Aviso Legal</a>
            <a href="{{ route('privacidad') }}">Privacidad</a>
            <a href="{{ route('terminos') }}">Términos y Condiciones</a>
            <a href="{{ route('contacto') }}">Contacto</a>
        </div>
    </div>
</footer>
