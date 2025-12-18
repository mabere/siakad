<?php

namespace Database\Seeders;

use App\Models\Thesis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ThesisMilestone;
use Illuminate\Database\Seeder;

class ThesisMilestoneSeeder extends Seeder
{
    public function run()
    {
        $theses = Thesis::create([
            'student_id' => 1,
            'title' => 'Karakter Tokoh Utama Pie',
            'status' => 'active',
            'start_date' => '2024-01-02',
            'end_date' => '2024-10-02'
        ]);

        $defaultMilestones = [
            [
                'thesis_id' => 1,
                'title' => 'Proposal Skripsi',
                'description' => 'Pengajuan dan presentasi proposal skripsi',
                'order' => 1,
                'tasks' => [
                    'Pengajuan judul skripsi',
                    'Penulisan proposal',
                    'Review proposal oleh pembimbing',
                    'Revisi proposal',
                    'Seminar proposal'
                ]
            ],
            [
                'thesis_id' => 1,
                'title' => 'Bab 1 - Pendahuluan',
                'description' => 'Penulisan dan revisi Bab 1',
                'order' => 2,
                'tasks' => [
                    'Penulisan latar belakang',
                    'Penulisan rumusan masalah',
                    'Penulisan tujuan penelitian',
                    'Penulisan batasan masalah',
                    'Review dan revisi'
                ]
            ],
            // ... tambahkan milestone lainnya
        ];

        foreach ($defaultMilestones as $milestoneData) {
            // Buat milestone
            $milestone = ThesisMilestone::create([
                'thesis_id' => $milestoneData['thesis_id'],
                'title' => $milestoneData['title'],
                'description' => $milestoneData['description'],
                'order' => $milestoneData['order'],
            ]);


        }
    }
}