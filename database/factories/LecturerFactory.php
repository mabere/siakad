<?php

namespace Database\Factories;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LecturerFactory extends Factory
{
    protected $model = Lecturer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // relasi ke akun login
            'nidn' => $this->faker->unique()->numerify('1001009999'),
            'name' => $this->faker->name,
            'gender' => $this->faker->randomElement(['Laki-Laki', 'Perempuan']),
            'email' => $this->faker->unique()->safeEmail,
            'department_id' => 1,
        ];
    }
}
