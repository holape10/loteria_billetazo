<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecuperarContrasena extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $enlace = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutos = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Recupera tu contraseña - El Billetazo')
            ->greeting('¡Hola, ' . strtok($notifiable->name, ' ') . '!')
            ->line('Recibimos una solicitud para cambiar la contraseña de tu cuenta en El Billetazo.')
            ->action('Crear nueva contraseña', $enlace)
            ->line("Este enlace vence en {$minutos} minutos.")
            ->line('Si tú no lo pediste, ignora este correo: tu contraseña sigue siendo la misma.')
            ->salutation('Saludos, El Billetazo');
    }
}
