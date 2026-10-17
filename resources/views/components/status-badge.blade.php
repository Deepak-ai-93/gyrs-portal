@props(['status' => null])

@php
    $key = strtolower(str_replace([' ', '-'], '_', (string) $status));

    $map = [
        'draft' => ['neutral', 'Draft'],
        'published' => ['success', 'Published'],
        'open' => ['success', 'Open'],
        'closed' => ['neutral', 'Closed'],
        'archived' => ['neutral', 'Archived'],
        'expired' => ['danger', 'Expired'],
        'applied' => ['info', 'Applied'],
        'under_review' => ['warning', 'Under review'],
        'shortlisted' => ['success', 'Shortlisted'],
        'interview_call' => ['success', 'Interview call'],
        'placed' => ['success', 'Placed'],
        'rejected' => ['danger', 'Rejected'],
        'pending' => ['warning', 'Pending'],
        'verified' => ['success', 'Verified'],
    ];

    [$variant, $label] = $map[$key] ?? ['neutral', $status ? \Illuminate\Support\Str::headline((string) $status) : 'Unknown'];
@endphp

<span class="badge badge-{{ $variant }} status-badge" data-status="{{ $key }}">{{ $label }}</span>
