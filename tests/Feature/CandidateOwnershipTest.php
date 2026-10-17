<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CandidateOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_view_only_their_own_application(): void
    {
        $owner = User::factory()->candidate()->create();
        $other = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();

        $application = Application::factory()->create([
            'candidate_id' => $owner->getKey(),
            'job_id' => $job->getKey(),
        ]);

        $this->assertTrue(Gate::forUser($owner)->allows('view', $application));
        $this->assertFalse(Gate::forUser($other)->allows('view', $application));
    }

    public function test_admin_can_view_any_application(): void
    {
        $admin = User::factory()->admin()->create();
        $application = Application::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('view', $application));
    }

    public function test_candidate_application_list_is_scoped_to_the_authenticated_user(): void
    {
        $candidate = User::factory()->candidate()->create();
        $other = User::factory()->candidate()->create();

        Application::factory()->count(2)->create(['candidate_id' => $candidate->getKey()]);
        Application::factory()->create(['candidate_id' => $other->getKey()]);

        $this->assertSame(2, $candidate->applications()->count());
    }

    public function test_guest_is_redirected_from_candidate_dashboard(): void
    {
        $this->get(route('candidate.dashboard'))
            ->assertRedirect(route('candidate.login'));
    }
}
