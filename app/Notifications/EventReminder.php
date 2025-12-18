<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Event;

class EventReminder extends Notification
{
    use Queueable;

    protected $event;
    protected $message;

    public function __construct(Event $event, $message)
    {
        $this->event = $event;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengingat Acara Akademik: ' . $this->event->title)
            ->line($this->message)
            ->line('Judul: ' . $this->event->title)
            ->line('Tanggal: ' . $this->event->start_date->format('d M Y H:i') . ' - ' . $this->event->end_date->format('d M Y H:i'))
            ->line('Deskripsi: ' . ($this->event->description ?? 'Tidak ada deskripsi'))
            ->action('Lihat Kalender', url('/calendar'));
    }
}