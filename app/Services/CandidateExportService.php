<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateExportService
{
    public const COLUMNS = [
        'Enrollment ID',
        'Full Name',
        'Email',
        'Phone',
        'District',
        'Qualification',
        'CGPA',
        'Skills',
        'Status',
        'Registered Date',
    ];

    /**
     * Stream the candidate roster as CSV and record an audit log entry.
     *
     * Authorization is enforced by the caller (policy) before this runs.
     *
     * @param  Collection<int, User>  $candidates
     */
    public function stream(Collection $candidates, ?User $actor = null): StreamedResponse
    {
        $filename = 'GYRS_Student_Roster_'.now()->format('Y-m-d').'.csv';

        AuditLog::create([
            'actor_id' => $actor?->getKey(),
            'action' => 'candidates.exported',
            'entity_type' => User::class,
            'entity_id' => null,
            'metadata' => ['count' => $candidates->count(), 'filename' => $filename],
            'created_at' => now(),
        ]);

        return response()->streamDownload(function () use ($candidates) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, self::COLUMNS);

            foreach ($candidates as $candidate) {
                $skills = $candidate->relationLoaded('skills')
                    ? $candidate->skills->pluck('name')->implode('; ')
                    : '';

                fputcsv($handle, [
                    $candidate->enrollment_id,
                    $candidate->name,
                    $candidate->email,
                    $candidate->phone,
                    $candidate->district?->name,
                    $candidate->qualification,
                    $candidate->cgpa,
                    $skills,
                    $candidate->statusLabel(),
                    optional($candidate->created_at)->format('d M Y'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
