<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_number' => 'TKT-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'customer_id' => fn (array $attributes) => Project::query()->find($attributes['project_id'])?->customer_id,
            'project_id' => Project::factory(),
            'subject' => fake()->sentence(5),
            'status' => TicketStatus::OPEN,
            'closed_at' => null,
        ];
    }

    public function forProject(Project $project): static
    {
        return $this->state(fn () => [
            'project_id' => $project->id,
            'customer_id' => $project->customer_id,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::CLOSED,
            'closed_at' => now(),
        ]);
    }
}
