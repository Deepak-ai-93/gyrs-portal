<?php

namespace App\Services;

use App\Models\Job;

class NotificationTemplateService
{
    /**
     * WhatsApp job-alert message (see docs/NOTIFICATION_TEMPLATES.md).
     */
    public function whatsapp(Job $job): string
    {
        $detailsUrl = route('jobs.show', $job);
        $notification = $job->notification_pdf_path ?: $job->apply_url;

        return implode("\n", [
            'New Govt Job Alert — GYRS Gujarat',
            $job->title,
            $job->department.', '.($job->district?->name ?? $job->location),
            'Posts: '.$job->total_posts.' | Salary: '.$job->salary_text,
            'Last Date: '.$this->date($job),
            'Notification: '.($notification ?: '—'),
            'Details & Apply: '.$detailsUrl,
        ]);
    }

    public function emailSubject(Job $job): string
    {
        return sprintf(
            'New Vacancy: %s (%s) — Apply by %s',
            $job->title,
            $job->department,
            $this->date($job),
        );
    }

    public function emailBody(Job $job): string
    {
        $detailsUrl = route('jobs.show', $job);
        $notification = $job->notification_pdf_path ?: $job->apply_url;

        return implode("\n", [
            'Dear Candidate,',
            '',
            'A new verified vacancy is live on GYRS Gujarat Youth Rojgar Setu:',
            '',
            'Job: '.$job->title,
            'Department: '.$job->department,
            'Location: '.($job->district?->name ?? $job->location).', Gujarat',
            'Advt No: '.($job->advertisement_number ?: '—').' | Posts: '.$job->total_posts,
            'Salary: '.$job->salary_text,
            'Qualification: '.$job->qualification,
            'Last Date: '.$this->date($job),
            '',
            'Notification PDF: '.($notification ?: '—'),
            'View full details & Apply Now: '.$detailsUrl,
            '',
            '— GYRS Portal',
        ]);
    }

    /**
     * @return array{whatsapp: string, emailSubject: string, emailBody: string, detailsUrl: string}
     */
    public function all(Job $job): array
    {
        return [
            'whatsapp' => $this->whatsapp($job),
            'emailSubject' => $this->emailSubject($job),
            'emailBody' => $this->emailBody($job),
            'detailsUrl' => route('jobs.show', $job),
        ];
    }

    private function date(Job $job): string
    {
        return $job->last_date?->format('d M Y') ?? 'Refer notification';
    }
}
