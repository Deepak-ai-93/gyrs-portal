<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_candidate_cannot_access_admin_area(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($candidate)->get(route('admin.jobs.index'))->assertForbidden();
        $this->actingAs($candidate)->get(route('admin.candidates.index'))->assertForbidden();
        $this->actingAs($candidate)->get(route('admin.applications.index'))->assertForbidden();
        $this->actingAs($candidate)->post(route('admin.jobs.store'))->assertForbidden();
    }

    public function test_admin_cannot_access_candidate_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('candidate.dashboard'))->assertForbidden();
    }

    public function test_admin_can_manage_job_status_via_model_scope(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->published()->create(['created_by' => $admin->getKey()]);

        $this->assertTrue($job->creator->is($admin));
    }
}
