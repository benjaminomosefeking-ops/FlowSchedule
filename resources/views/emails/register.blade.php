@component('mail::message')
# ¡Bienvenido a FlowSchedule!

Hola {{ $name }},

¡Bienvenido a FlowSchedule! Nos alegra mucho tenerte con nosotros.

@component('mail::button', ['url' => url('/email/verify')])
Verifica tu correo electrónico
@endcomponent

Haz clic en el botón de arriba para verificar tu dirección de correo electrónico y comenzar.

¡Gracias por unirte a nuestra comunidad!
@endcomponent