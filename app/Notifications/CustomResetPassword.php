<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->greeting('¡Hola!')
            ->line('Recibes este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', url(config('app.url') . route('password.reset', [$this->token], false)))
            ->line('Este enlace para restablecer la contraseña caducará en 60 minutos.')
            ->line('Si no has solicitado restablecer la contraseña, no es necesario que hagas nada.')
            ->line('Si necesitas ayuda, contacta con nuestro equipo de soporte.');
    }
}
