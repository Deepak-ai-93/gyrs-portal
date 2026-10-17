@php
    $job = $job ?? null;
    $employmentTypes = [
        'Full-Time' => 'Full-Time (Government & private)',
        'Govt Direct' => 'Official Government (Direct)',
        'Govt Contract' => 'Government Contract',
        'Apprenticeship' => 'Apprenticeship & Trainee',
    ];
    $statuses = ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'archived' => 'Archived'];
    $existingSkills = old('skills', $job && $job->skills ? $job->skills->pluck('name')->values()->all() : []);
    $skillSlots = max(5, count($existingSkills));
    $detailText = function ($relation) use ($job) {
        if (! $job) {
            return '';
        }
        $value = $job->{$relation};
        return $value ? $value->pluck('body')->implode("\n") : '';
    };
    $districtOptions = $districts ?? [];
@endphp

<fieldset class="form-section">
    <legend>Position</legend>

    <div class="form-grid">
        <x-input name="title" label="Job title" :value="$job->title ?? null" :required="true" />
        <x-input name="department" label="Department / organisation" :value="$job->department ?? null" :required="true" />
    </div>

    <div class="form-grid-3 form-grid">
        <x-select name="district_id" label="District" :options="$districtOptions" :value="$job->district_id ?? null" placeholder="Select district" :required="true" />
        <x-input name="location" label="Location" :value="$job->location ?? null" hint="Area or city within the district." />
        <x-select name="employment_type" label="Employment type" :options="$employmentTypes" :value="$job->employment_type ?? null" placeholder="Select type" :required="true" />
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Compensation &amp; eligibility</legend>

    <div class="form-grid-3 form-grid">
        <x-input name="salary_text" label="Salary / stipend" :value="$job->salary_text ?? null" hint="For example ₹35,000 – ₹45,000 / month." />
        <x-input name="qualification" label="Required qualification" :value="$job->qualification ?? null" />
        <x-input name="age_limit" label="Age limit" :value="$job->age_limit ?? null" />
    </div>

    <div class="form-grid-3 form-grid">
        <x-input name="total_posts" label="Total posts" type="number" :value="$job->total_posts ?? null" />
        <x-input name="match_score" label="Match score" type="number" :value="$job->match_score ?? null" hint="0–100 relevance score." />
        <x-input name="fees" label="Application fee" :value="$job->fees ?? null" />
    </div>

    <x-input name="vacancy_breakup" label="Vacancy breakup" :value="$job->vacancy_breakup ?? null" hint="For example UR: 20 | SEBC: 12 | SC: 6." />
</fieldset>

<fieldset class="form-section">
    <legend>Timeline &amp; reference</legend>

    <div class="form-grid-3 form-grid">
        <x-input name="advertisement_number" label="Advertisement number" :value="$job->advertisement_number ?? null" />
        <x-input name="posted_at" label="Posted on" type="date" :value="$job?->posted_at ? \Illuminate\Support\Carbon::parse($job->posted_at)->format('Y-m-d') : null" />
        <x-input name="last_date" label="Last date to apply" type="date" :value="$job?->last_date ? \Illuminate\Support\Carbon::parse($job->last_date)->format('Y-m-d') : null" />
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Content</legend>

    <x-textarea name="description" label="Job description" :value="$job->description ?? null" :required="true" :rows="5" />

    <div class="form-field">
        <p class="form-label" id="job-skills-label">Key skills</p>
        <div class="form-grid">
            @for ($i = 0; $i < $skillSlots; $i++)
                <x-input name="skills[]" :label="'Skill ' . ($i + 1)" aria-labelledby="job-skills-label" :value="$existingSkills[$i] ?? null" hint="{{ $i === 0 ? 'Type each required skill; leave unused rows blank.' : null }}" />
            @endfor
        </div>
    </div>

    <div class="form-grid">
        <x-textarea name="eligibility" label="Eligibility criteria" :value="$detailText('eligibility')" hint="One criterion per line." />
        <x-textarea name="responsibilities" label="Roles & responsibilities" :value="$detailText('responsibilities')" hint="One responsibility per line." />
    </div>

    <div class="form-grid">
        <x-textarea name="selection_steps" label="Selection process" :value="$detailText('selectionSteps')" hint="One step per line." />
        <x-textarea name="application_steps" label="How to apply" :value="$detailText('applicationSteps')" hint="One step per line." />
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Links &amp; publication</legend>

    <div class="form-grid">
        <x-input name="official_website_url" label="Official website URL" type="url" :value="$job->official_website_url ?? null" hint="https://ojas.gujarat.gov.in/" />
        <x-input name="apply_url" label="Apply now URL" type="url" :value="$job->apply_url ?? null" hint="External official application portal." />
    </div>

    <x-input name="notification_pdf_path" label="Notification PDF URL" type="url" :value="$job->notification_pdf_path ?? null" hint="Link to the official notification PDF, if published online." />

    <x-select name="status" label="Publication status" :options="$statuses" :value="$job->status ?? 'draft'" :required="true" hint="Draft jobs are hidden from the public portal." />
</fieldset>
