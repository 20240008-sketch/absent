<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassModelFactory extends Factory
{
    protected $model = ClassModel::class;

    public function definition(): array
    {
        return [
            'class_id' => 'A' . fake()->unique()->numberBetween(101, 399),
            'class_name' => fake()->randomElement(['1年A組', '2年B組', '3年C組']),
            '担任' => fake()->name(),
            '年度' => 2025,
        ];
    }
}
