<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->title(),
            'is_completed' => $this->faker->boolean(),
        ];
    }

    public function uncompleted(): self
    {
        return $this->state(['is_completed' => false]);
    }
}
