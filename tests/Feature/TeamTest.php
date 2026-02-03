<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_user_can_create_a_team_and_becomes_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/teams', [
            'name' => 'Test Team',
        ]);

        $team = Team::where('name', 'Test Team')->first();

        $this->assertNotNull($team);
        $response->assertRedirect(route('teams.show', $team));

        $this->assertDatabaseHas('team_memberships', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
    }

    public function test_user_can_see_their_teams(): void
    {
        $user = User::factory()->create();
        $team1 = Team::factory()->create(['name' => 'Alpha Team']);
        $team2 = Team::factory()->create(['name' => 'Beta Team']);

        $team1->members()->attach($user->id, ['role' => 'admin']);
        $team2->members()->attach($user->id, ['role' => 'player']);

        $response = $this->actingAs($user)->get('/teams');

        $response->assertStatus(200);
        $response->assertSeeText('Alpha Team');
        $response->assertSeeText('Beta Team');
    }

    public function test_non_member_receives_403_on_team_show(): void
    {
        $team = Team::factory()->create();
        $nonMember = User::factory()->create();

        $response = $this->actingAs($nonMember)->get("/teams/{$team->id}");

        $response->assertStatus(403);
    }

    public function test_member_can_view_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'My Team']);

        $team->members()->attach($user->id, ['role' => 'player']);

        $response = $this->actingAs($user)->get("/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertSeeText('My Team');
        $response->assertSeeText($user->name);
    }
}
