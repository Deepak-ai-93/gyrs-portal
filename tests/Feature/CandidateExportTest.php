<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_export(): void
    {
        $this->get(route('admin.exports.candidates'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_candidate_cannot_export_roster(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)
            ->get(route('admin.exports.candidates'))
            ->assertRedirect(route('candidate.dashboard'));
    }

    public function test_admin_can_export_roster_and_audit_log_is_written(): void
    {
        $admin = User::factory()->admin()->create();
        $district = District::factory()->create(['name' => 'Gandhinagar']);
        $skill = Skill::factory()->create(['name' => 'Python', 'slug' => 'python']);

        $candidate = User::factory()->candidate()->create([
            'name' => 'Jagan Trivedi',
            'district_id' => $district->getKey(),
        ]);
        $candidate->skills()->attach($skill);

        $response = $this->actingAs($admin)->get(route('admin.exports.candidates'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Jagan Trivedi', $response->streamedContent());

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->getKey(),
            'action' => 'candidates.exported',
        ]);
    }
}
