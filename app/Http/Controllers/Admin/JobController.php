<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobRequest;
use App\Models\Job;
use App\Models\Skill;
use App\Services\JobSearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function __construct(private readonly JobSearchService $search) {}

    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->input('keyword'),
            'district' => $request->input('district'),
            'type' => $request->input('type'),
            'status' => $request->input('status'),
        ];

        return view('admin.jobs.index', [
            'jobs' => $this->search->adminSearch($filters),
            'filters' => $filters,
            'districts' => $this->search->districts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.jobs.create', [
            'job' => new Job,
            'districts' => $this->search->districts(),
            'allSkills' => Skill::query()->orderBy('name')->get(),
        ]);
    }

    public function store(JobRequest $request): RedirectResponse
    {
        $job = DB::transaction(function () use ($request) {
            $job = Job::create($this->payload($request) + [
                'slug' => Job::uniqueSlug($request->string('title')->value()),
                'created_by' => $request->user()->getKey(),
            ]);

            $this->syncRelations($job, $request->validated());

            return $job;
        });

        return redirect()->route('admin.jobs.show', $job)
            ->with('status', 'Job posting created successfully.');
    }

    public function show(Job $job): View
    {
        $job->load([
            'district',
            'skills',
            'eligibility',
            'responsibilities',
            'selectionSteps',
            'applicationSteps',
            'applications.candidate',
        ]);

        return view('admin.jobs.show', [
            'job' => $job,
        ]);
    }

    public function edit(Job $job): View
    {
        $job->load(['skills', 'eligibility', 'responsibilities', 'selectionSteps', 'applicationSteps']);

        return view('admin.jobs.edit', [
            'job' => $job,
            'districts' => $this->search->districts(),
            'allSkills' => Skill::query()->orderBy('name')->get(),
        ]);
    }

    public function update(JobRequest $request, Job $job): RedirectResponse
    {
        DB::transaction(function () use ($request, $job) {
            $job->update($this->payload($request));
            $this->syncRelations($job, $request->validated());
        });

        return redirect()->route('admin.jobs.show', $job)
            ->with('status', 'Job posting updated successfully.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('status', 'Job posting removed successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(JobRequest $request): array
    {
        return $request->safe()->only([
            'title',
            'department',
            'district_id',
            'location',
            'salary_text',
            'qualification',
            'employment_type',
            'description',
            'match_score',
            'advertisement_number',
            'total_posts',
            'posted_at',
            'last_date',
            'age_limit',
            'fees',
            'vacancy_breakup',
            'official_website_url',
            'apply_url',
            'notification_pdf_path',
            'status',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncRelations(Job $job, array $data): void
    {
        $skillIds = collect($data['skills'] ?? [])
            ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
            ->map(fn (string $skill) => Skill::firstOrCreate(
                ['slug' => Str::slug(trim($skill))],
                ['name' => trim($skill)],
            )->getKey())
            ->unique()
            ->values()
            ->all();

        $job->skills()->sync($skillIds);

        $this->syncSteps($job->eligibility(), $data['eligibility'] ?? []);
        $this->syncSteps($job->responsibilities(), $data['responsibilities'] ?? []);
        $this->syncSteps($job->selectionSteps(), $data['selection_steps'] ?? []);
        $this->syncSteps($job->applicationSteps(), $data['application_steps'] ?? []);
    }

    /**
     * @param  HasMany  $relation
     * @param  array<int, string>  $rows
     */
    private function syncSteps($relation, array $rows): void
    {
        $query = $relation->getRelated()->newQuery()
            ->where($relation->getForeignKeyName(), $relation->getParentKey());

        $query->delete();

        foreach (array_values(array_filter($rows, fn ($row) => is_string($row) && trim($row) !== '')) as $index => $row) {
            $relation->create([
                'body' => trim($row),
                'sort_order' => $index,
            ]);
        }
    }
}
