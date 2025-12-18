<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
// use Database\Seeders\ThesisModuleSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
                // RoleSeeder::class,
            UserSeeder::class,
            // EdomCategorySeeder::class,
            // EdomQuestionnaireSeeder::class,
            // AlumniSeeder::class,
            // ThesisModuleSeeder::class
        ]);
    }
}