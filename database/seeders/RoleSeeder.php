<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'slug' => 'admin'],
            ['name' => 'dekan', 'slug' => 'dekan'],
            ['name' => 'ujm', 'slug' => 'ujm'],
            ['name' => 'kaprodi', 'slug' => 'kaprodi'],
            ['name' => 'dosen', 'slug' => 'dosen'],
            ['name' => 'mahasiswa', 'slug' => 'mahasiswa'],
            ['name' => 'staff', 'slug' => 'staff'],
            ['name' => 'ktu', 'slug' => 'ktu'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}