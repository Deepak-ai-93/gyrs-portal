<?php

namespace App\Services;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * Create a candidate application with duplicate protection, an immutable
     * status history entry and an audit log record.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ValidationException
     */
    public function create(User $candidate, Job $job, array $attributes = []): Application
    {
        if ($job->status !== Job::STATUS_PUBLISHED) {
            throw ValidationException::withMessages([
                'job' => 'This vacancy is not currently open for applications.',
            ]);
        }

        if ($job->isExpired()) {
            throw ValidationException::withMessages([
                'job' => 'The last date to apply for this vacancy has passed.',
            ]);
        }

        if ($this->alreadyApplied($candidate, $job)) {
            throw ValidationException::withMessages([
                'job' => 'You have already applied for this vacancy.',
            ]);
        }

        return DB::transaction(function () use ($candidate, $job, $attributes) {
            $application = Application::create([
                'candidate_id' => $candidate->getKey(),
                'job_id' => $job->getKey(),
                'status' => Application::STATUS_APPLIED,
                'applied_at' => now(),
                'status_changed_at' => now(),
                'notes' => $attributes['notes'] ?? null,
                'external_reference' => $attributes['external_reference'] ?? null,
                'external_url' => $attributes['external_url'] ?? $job->apply_url,
            ]);

            $application->histories()->create([
                'from_status' => null,
                'to_status' => Application::STATUS_APPLIED,
                'actor_id' => $candidate->getKey(),
                'notes' => 'Application submitted.',
                'created_at' => now(),
            ]);

            $this->audit($candidate, 'application.created', $application, [
                'job_id' => $job->getKey(),
                'candidate_id' => $candidate->getKey(),
                'status' => $application->status,
            ]);

            return $application;
        });
    }

    /**
     * Update the status of an application, recording history and audit trail.
     *
     * @throws ValidationException
     */
    public function updateStatus(Application $application, string $status, ?User $actor = null, ?string $notes = null): Application
    {
        if (! in_array($status, Application::statuses(), true)) {
            throw ValidationException::withMessages([
                'status' => 'The selected status is invalid.',
            ]);
        }

        if ($application->status === $status) {
            return $application;
        }

        return DB::transaction(function () use ($application, $status, $actor, $notes) {
            $from = $application->status;

            $application->update([
                'status' => $status,
                'status_changed_at' => now(),
                'notes' => $notes ?? $application->notes,
            ]);

            $application->histories()->create([
                'from_status' => $from,
                'to_status' => $status,
                'actor_id' => $actor?->getKey(),
                'notes' => $notes,
                'created_at' => now(),
            ]);

            $this->audit($actor, 'application.status_changed', $application, [
                'from' => $from,
                'to' => $status,
            ]);

            return $application->refresh();
        });
    }

    public function alreadyApplied(User $candidate, Job $job): bool
    {
        return Application::query()
            ->where('candidate_id', $candidate->getKey())
            ->where('job_id', $job->getKey())
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function audit(?User $actor, string $action, ?object $entity = null, array $metadata = []): AuditLog
    {
        return AuditLog::create([
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity && method_exists($entity, 'getKey') ? (string) $entity->getKey() : null,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }
}
