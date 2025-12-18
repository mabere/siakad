<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // asumsi relasi ke tabel user
            'nim' => $this->faker->unique()->numerify('20210001'),
            'name' => $this->faker->name,
            'gender' => $this->faker->randomElement(['Laki-Laki', 'Perempuan']),
            'deppartment_id' => 1, // kamu bisa sesuaikan dengan yang ada
        ];
    }
}
