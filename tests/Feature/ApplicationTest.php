<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_apply_once_and_history_and_audit_are_recorded(): void
    {
        $candidate = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();

        $application = app(ApplicationService::class)->create($candidate, $job);

        $this->assertDatabaseHas('applications', [
            'id' => $application->getKey(),
            'candidate_id' => $candidate->getKey(),
            'job_id' => $job->getKey(),
            'status' => Application::STATUS_APPLIED,
        ]);

        $this->assertDatabaseHas('application_status_history', [
            'application_id' => $application->getKey(),
            'to_status' => Application::STATUS_APPLIED,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'application.created',
            'entity_id' => $application->getKey(),
        ]);
    }

    public function test_duplicate_applications_are_prevented(): void
    {
        $candidate = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();

        $service = app(ApplicationService::class);
        $service->create($candidate, $job);

        $this->expectException(ValidationException::class);
        $service->create($candidate, $job);
    }

    public function test_cannot_apply_to_draft_or_expired_jobs(): void
    {
        $candidate = User::factory()->candidate()->create();
        $service = app(ApplicationService::class);

        $this->expectException(ValidationException::class);
        $service->create($candidate, Job::factory()->draft()->create());
    }

    public function test_cannot_apply_after_last_date(): void
    {
        $candidate = User::factory()->candidate()->create();
        $service = app(ApplicationService::class);

        $this->expectException(ValidationException::class);
        $service->create($candidate, Job::factory()->expired()->create());
    }

    public function test_candidate_apply_route_stores_application(): void
    {
        $candidate = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();

        $response = $this->actingAs($candidate)->post(route('jobs.applications.store', $job));

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertDatabaseHas('applications', [
            'candidate_id' => $candidate->getKey(),
            'job_id' => $job->getKey(),
        ]);
    }

    public function test_duplicate_apply_route_returns_validation_error(): void
    {
        $candidate = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();

        app(ApplicationService::class)->create($candidate, $job);

        $response = $this->actingAs($candidate)
            ->from(route('jobs.show', $job))
            ->post(route('jobs.applications.store', $job));

        $response->assertRedirect(route('jobs.show', $job));
        $response->assertSessionHasErrors('job');
        $this->assertSame(1, Application::query()->count());
    }

    public function test_admin_cannot_use_candidate_apply_route(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->published()->create();

        $this->actingAs($admin)
            ->post(route('jobs.applications.store', $job))
            ->assertForbidden();
    }
}
