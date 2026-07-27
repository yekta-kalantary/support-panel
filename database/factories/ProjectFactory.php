<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'name' => fake()->words(3, true),
            'website_url' => fake()->url(),
            'status' => RecordStatus::ACTIVE,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => RecordStatus::INACTIVE]);
    }
}
