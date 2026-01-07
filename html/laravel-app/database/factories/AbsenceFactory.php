<?php

namespace Database\Factories;

use App\Models\Absence;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition(): array
    {
        return [
            'seito_id' => '1001',
            'division' => fake()->randomElement(['欠席', '遅刻', '早退']),
            'reason' => fake()->sentence(),
            'scheduled_time' => null,
            'absence_date' => fake()->date(),
        ];
    }
}
