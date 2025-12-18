<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class LetterStatusUpdated extends Notification
{
    use Queueable;

    protected $letterRequest;

    public function __construct($letterRequest)
    {
        $this->letterRequest = $letterRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // public function toArray($notifiable)
    // {
    //     $statusLabels = [
    //         'submitted' => 'Diajukan',
    //         'processing' => 'Sedang Diproses',
    //         'approved' => 'Disetujui',
    //         'rejected' => 'Ditolak',
    //     ];
    //     $status = $statusLabels[$this->letterRequest->status] ?? ucfirst($this->letterRequest->status);

    //     return [
    //         'letter_id' => $this->letterRequest->id,
    //         'title' => "Pembaruan Status Surat #{$this->letterRequest->id}",
    //         'message' => "Pengajuan surat '{$this->letterRequest->tipeSurat->name}' telah {$status}.",
    //         'notes' => $this->letterRequest->status === 'approved' ? $this->letterRequest->notes : null,
    //         'rejection_reason' => $this->letterRequest->status === 'rejected' ? $this->letterRequest->rejection_reason : null,
    //         'link' => route('student.request.surat.show', $this->letterRequest),
    //     ];
    // }


    public function toArray($notifiable)
    {
        $statusLabels = [
            'submitted' => 'Diajukan',
            'processing' => 'Sedang Diproses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
        $status = $statusLabels[$this->letterRequest->status] ?? ucfirst($this->letterRequest->status);

        // Tentukan route berdasarkan role
        $routeName = $notifiable->hasRole('dosen') ? 'lecturer.request.surat.show' : 'student.request.surat.show';

        return [
            'letter_id' => $this->letterRequest->id,
            'title' => "Pembaruan Status Surat #{$this->letterRequest->id}",
            'message' => "Pengajuan surat '{$this->letterRequest->letterType->name}' telah {$status}.",
            'notes' => $this->letterRequest->status === 'approved' ? $this->letterRequest->notes : null,
            'rejection_reason' => $this->letterRequest->status === 'rejected' ? $this->letterRequest->rejection_reason : null,
            'link' => route($routeName, $this->letterRequest),
        ];
    }
}