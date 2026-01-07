<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'seito_id' => fake()->unique()->numerify('####'),
            'name' => fake()->name(),
            'grade' => fake()->numberBetween(1, 3),
            'class_id' => 'A101',
        ];
    }
}
