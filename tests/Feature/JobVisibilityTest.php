<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Job;
use App\Services\JobSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_search_only_returns_published_non_expired_jobs(): void
    {
        $open = Job::factory()->published()->create(['title' => 'Open Vacancy']);
        $draft = Job::factory()->draft()->create(['title' => 'Draft Vacancy']);
        $expired = Job::factory()->expired()->create(['title' => 'Expired Vacancy']);

        $results = app(JobSearchService::class)->search([]);

        $this->assertSame(1, $results->total());
        $this->assertTrue($results->pluck('id')->contains($open->getKey()));
        $this->assertFalse($results->pluck('id')->contains($draft->getKey()));
        $this->assertFalse($results->pluck('id')->contains($expired->getKey()));
    }

    public function test_search_filters_by_keyword(): void
    {
        Job::factory()->published()->create(['title' => 'Junior Software Engineer']);
        Job::factory()->published()->create(['title' => 'Solar Panel Engineer']);

        $results = app(JobSearchService::class)->search(['keyword' => 'Software']);

        $this->assertSame(1, $results->total());
        $this->assertSame('Junior Software Engineer', $results->first()->title);
    }

    public function test_search_filters_by_district_and_type(): void
    {
        $surat = District::factory()->create(['name' => 'Surat', 'slug' => 'surat']);
        $rajkot = District::factory()->create(['name' => 'Rajkot', 'slug' => 'rajkot']);

        Job::factory()->published()->create([
            'title' => 'Textile Technician',
            'district_id' => $surat->getKey(),
            'employment_type' => 'Full-Time',
        ]);
        Job::factory()->published()->create([
            'title' => 'Cyber Analyst',
            'district_id' => $rajkot->getKey(),
            'employment_type' => 'Govt Direct',
        ]);

        $service = app(JobSearchService::class);

        $this->assertSame(1, $service->search(['district' => 'surat'])->total());
        $this->assertSame(1, $service->search(['type' => 'Govt Direct'])->total());
        $this->assertSame('Cyber Analyst', $service->search(['type' => 'Govt Direct'])->first()->title);
    }

    public function test_expired_and_draft_jobs_are_not_open(): void
    {
        $this->assertFalse(Job::factory()->draft()->create()->isOpen());
        $this->assertFalse(Job::factory()->expired()->create()->isOpen());
        $this->assertTrue(Job::factory()->published()->create()->isOpen());
    }
}
