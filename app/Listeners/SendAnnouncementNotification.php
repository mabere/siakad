<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAnnouncement;

class SendAnnouncementNotification
{
    public function handle(AnnouncementCreated $event)
    {
        $roles = $event->announcement->target_role == 'semua'
            ? ['mahasiswa', 'dosen', 'admin', 'dekan', 'kaprodi']
            : [$event->announcement->target_role];

        $users = User::whereIn('id', function ($query) use ($roles, $event) {
            $query->select('user_id')
                ->from('role_user')
                ->whereIn('role_id', function ($q) use ($roles) {
                    $q->select('id')
                        ->from('roles')
                        ->whereIn('name', $roles);
                })
                ->when($event->announcement->faculty_id, function ($q) use ($event) {
                    $q->whereIn('user_id', function ($sub) use ($event) {
                        $sub->select('user_id')
                            ->from('lecturers')
                            ->whereIn('department_id', function ($d) use ($event) {
                                $d->select('id')
                                    ->from('departments')
                                    ->where('faculty_id', $event->announcement->faculty_id);
                            });
                    });
                })
                ->when($event->announcement->department_id, function ($q) use ($event) {
                    $q->whereIn('user_id', function ($sub) use ($event) {
                        $sub->select('user_id')
                            ->from('lecturers')
                            ->where('department_id', $event->announcement->department_id);
                    });
                });
        })->get();

        Notification::send($users, new NewAnnouncement($event->announcement));
    }
}