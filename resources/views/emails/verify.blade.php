@component('mail::message')
# Correo verificado correctamente

Hola {{ $name }},

¡Tu dirección de correo electrónico ha sido verificada correctamente!

Ya puedes acceder a todas las funciones de FlowSchedule.

@component('mail::button', ['url' => url('/dashboard')])
Comenzar
@endcomponent

¡Bienvenido al equipo de FlowSchedule!

Si tienes alguna pregunta, no dudes en contactar con nuestro equipo de soporte.
@endcomponent