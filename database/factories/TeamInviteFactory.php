<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamInvite>
 */
class TeamInviteFactory extends Factory
{
    protected $model = TeamInvite::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'invited_by' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'accepted_at' => now(),
        ]);
    }
}
