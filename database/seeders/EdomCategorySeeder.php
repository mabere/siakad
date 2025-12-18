<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdomCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'key' => 'PEDAGOGIK',
                'value' => 'Kemampuan Pedagogik',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'PROFESIONAL',
                'value' => 'Kemampuan Profesional',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'KEPRIBADIAN',
                'value' => 'Kemampuan Kepribadian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'SOSIAL',
                'value' => 'Kemampuan Sosial',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'LAINNYA',
                'value' => 'Kemampuan Lain',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Cek dan insert kategori yang belum ada
        foreach ($categories as $category) {
            DB::table('edom_categories')->updateOrInsert(
                ['key' => $category['key']],
                $category
            );
        }
    }
}