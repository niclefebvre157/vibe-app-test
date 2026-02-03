<?php

namespace Tests\Feature;

use App\Mail\TeamInviteMail;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TeamInviteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createTeamWithAdmin(): array
    {
        $admin = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($admin->id, ['role' => 'admin']);

        return [$team, $admin];
    }

    public function test_admin_can_create_invite(): void
    {
        Mail::fake();

        [$team, $admin] = $this->createTeamWithAdmin();

        $response = $this->actingAs($admin)->post(route('team-invites.store', $team), [
            'email' => 'player@example.com',
        ]);

        $response->assertRedirect(route('teams.show', $team));
        $response->assertSessionHas('success', 'Invitation sent.');

        $this->assertDatabaseHas('team_invites', [
            'team_id' => $team->id,
            'invited_by' => $admin->id,
            'email' => 'player@example.com',
        ]);

        Mail::assertSent(TeamInviteMail::class, function ($mail) {
            return $mail->hasTo('player@example.com');
        });
    }

    public function test_non_admin_cannot_create_invite(): void
    {
        $player = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($player->id, ['role' => 'player']);

        $response = $this->actingAs($player)->post(route('team-invites.store', $team), [
            'email' => 'someone@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_accept_invite_creates_membership(): void
    {
        [$team, $admin] = $this->createTeamWithAdmin();
        $invite = TeamInvite::factory()->create([
            'team_id' => $team->id,
            'invited_by' => $admin->id,
            'email' => 'player@example.com',
        ]);

        $player = User::factory()->create(['email' => 'player@example.com']);

        $response = $this->actingAs($player)->get(route('team-invites.accept', $invite->token));

        $response->assertRedirect(route('teams.show', $team));

        $this->assertDatabaseHas('team_memberships', [
            'team_id' => $team->id,
            'user_id' => $player->id,
            'role' => 'player',
        ]);

        $invite->refresh();
        $this->assertNotNull($invite->accepted_at);
    }

    public function test_expired_token_is_rejected(): void
    {
        [$team, $admin] = $this->createTeamWithAdmin();
        $invite = TeamInvite::factory()->expired()->create([
            'team_id' => $team->id,
            'invited_by' => $admin->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('team-invites.accept', $invite->token));

        $response->assertStatus(410);

        $this->assertDatabaseMissing('team_memberships', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_token_cannot_be_reused(): void
    {
        [$team, $admin] = $this->createTeamWithAdmin();
        $invite = TeamInvite::factory()->accepted()->create([
            'team_id' => $team->id,
            'invited_by' => $admin->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('team-invites.accept', $invite->token));

        $response->assertStatus(410);
    }

    public function test_guest_redirected_to_login_with_intended_url(): void
    {
        [$team, $admin] = $this->createTeamWithAdmin();
        $invite = TeamInvite::factory()->create([
            'team_id' => $team->id,
            'invited_by' => $admin->id,
        ]);

        $response = $this->get(route('team-invites.accept', $invite->token));

        $response->assertRedirect(route('login'));
        $this->assertEquals(
            url("/invites/{$invite->token}/accept"),
            session('url.intended')
        );
    }

    public function test_cannot_invite_existing_team_member(): void
    {
        Mail::fake();

        [$team, $admin] = $this->createTeamWithAdmin();
        $existingMember = User::factory()->create(['email' => 'existing@example.com']);
        $team->members()->attach($existingMember->id, ['role' => 'player']);

        $response = $this->actingAs($admin)->post(route('team-invites.store', $team), [
            'email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        Mail::assertNothingSent();
    }

    public function test_cannot_send_duplicate_pending_invite(): void
    {
        Mail::fake();

        [$team, $admin] = $this->createTeamWithAdmin();
        TeamInvite::factory()->create([
            'team_id' => $team->id,
            'invited_by' => $admin->id,
            'email' => 'player@example.com',
        ]);

        $response = $this->actingAs($admin)->post(route('team-invites.store', $team), [
            'email' => 'player@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        Mail::assertNothingSent();
    }

    public function test_non_member_cannot_send_invite(): void
    {
        $nonMember = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($nonMember)->post(route('team-invites.store', $team), [
            'email' => 'someone@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_invalid_email_fails_validation(): void
    {
        [$team, $admin] = $this->createTeamWithAdmin();

        $response = $this->actingAs($admin)->post(route('team-invites.store', $team), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
