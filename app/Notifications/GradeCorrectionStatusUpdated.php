<?php

namespace App\Notifications;

use App\Models\GradeCorrectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeCorrectionStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $gradeCorrectionRequest;

    /**
     * Create a new notification instance.
     *
     * @param GradeCorrectionRequest $gradeCorrectionRequest
     */
    public function __construct(GradeCorrectionRequest $gradeCorrectionRequest)
    {
        $this->gradeCorrectionRequest = $gradeCorrectionRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Menggunakan email dan database
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Update Status Permintaan Perbaikan Nilai')
            ->line('Status permintaan perbaikan nilai Anda telah diperbarui.')
            ->line('Detail:')
            ->line('Mata Kuliah: ' . $this->gradeCorrectionRequest->course->name)
            ->line('Status: ' . $this->gradeCorrectionRequest->status)
            ->action('Lihat Detail', url('/academic/grade-corrections/' . $this->gradeCorrectionRequest->id))
            ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Status permintaan perbaikan nilai untuk mata kuliah ' . $this->gradeCorrectionRequest->course->name . ' telah diperbarui menjadi ' . $this->gradeCorrectionRequest->status,
            'grade_correction_request_id' => $this->gradeCorrectionRequest->id,
            'url' => url('/academic/grade-corrections/' . $this->gradeCorrectionRequest->id),
        ];
    }
}