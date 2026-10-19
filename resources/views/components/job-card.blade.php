@props(['job', 'saved' => null, 'showInterested' => true])

<article class="job-card card is-interactive">
    <div class="card-body">
        <div class="job-card-top">
            <h3 class="job-card-title"><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></h3>
            @if (! is_null($job->match_score))
                <span class="badge badge-info">{{ (int) $job->match_score }}% match</span>
            @endif
        </div>
        <p class="job-card-dept">{{ $job->department }}</p>

        <ul class="meta-list">
            <li><x-icon name="map-pin" :size="16" /> {{ $job->district->name ?? $job->location }}</li>
            @if ($job->employment_type)
                <li><x-icon name="briefcase" :size="16" /> {{ $job->employment_type }}</li>
            @endif
            @if ($job->salary_text)
                <li><x-icon name="award" :size="16" /> {{ $job->salary_text }}</li>
            @endif
            @if ($job->last_date)
                <li><x-icon name="calendar" :size="16" /> Apply by {{ \Illuminate\Support\Carbon::parse($job->last_date)->format('d M Y') }}</li>
            @endif
        </ul>

        @if ($job->relationLoaded('skills') && $job->skills->isNotEmpty())
            <div class="badge-row">
                @foreach ($job->skills as $skill)
                    <span class="badge">{{ $skill->name }}</span>
                @endforeach
            </div>
        @endif
    </div>
    <div class="card-foot">
        <x-button variant="outline" size="sm" :href="route('jobs.show', $job)">View details</x-button>
        @if ($showInterested && ! is_null($saved))
            <x-interested-button :job="$job" :saved="$saved" />
        @endif
    </div>
</article>
