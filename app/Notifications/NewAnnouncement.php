<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncement extends Notification
{
    use Queueable;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $link = match ($notifiable->roles->pluck('name')->first()) {
            'mahasiswa' => route('student.announcements.show', $this->announcement->id),
            'dosen' => route('lecturer.announcements.show', $this->announcement->id),
            'admin' => route('admin.announcements.show', $this->announcement->id),
            'dekan' => route('dekan.announcements.show', $this->announcement->id),
            'kaprodi' => route('kaprodi.announcements.show', $this->announcement->id),
            default => null,
        };

        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => 'Pengumuman baru telah dipublikasikan',
            'link' => $link,
        ];
    }
}