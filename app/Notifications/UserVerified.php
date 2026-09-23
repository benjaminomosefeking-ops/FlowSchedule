<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserVerified extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Correo verificado correctamente')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('¡Tu dirección de correo electrónico ha sido verificada correctamente!')
            ->line('Ya puedes acceder a todas las funciones de FlowSchedule.')
            ->action('Comenzar', url('/dashboard'))
            ->line('¡Bienvenido al equipo de FlowSchedule!')
            ->line('Si tienes alguna pregunta, no dudes en contactar con nuestro equipo de soporte.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
