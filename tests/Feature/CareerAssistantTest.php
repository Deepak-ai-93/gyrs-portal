<?php

namespace Tests\Feature;

use App\Models\CandidateSkillPlan;
use App\Models\Job;
use App\Models\ResumeAnalysis;
use App\Models\ResumeJobMatch;
use App\Models\Skill;
use App\Models\User;
use App\Services\Ai\ResumeTextExtractor;
use App\Services\Ai\SkillPlanBuilder;
use App\Services\JobMatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use ZipArchive;

class CareerAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_gets_a_local_analysis_without_spending_ai_tokens(): void
    {
        Http::preventStrayRequests();
        config(['ai.enabled' => true, 'ai.api_key' => null]);
        $candidate = User::factory()->candidate()->create();
        $resume = 'Computer science graduate with Python and SQL skills. Built a database project and supported a student team.';

        $response = $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => $resume,
            'target_role' => 'Junior Data Analyst',
            'consent' => '1',
        ]);

        $response->assertRedirect();
        $analysis = $candidate->resumeAnalyses()->firstOrFail();
        $this->assertSame(ResumeAnalysis::STATUS_COMPLETED, $analysis->status);
        $this->assertFalse($analysis->is_ai_generated);
        $this->assertSame(0, $analysis->input_tokens);
        $this->assertSame(0, $analysis->output_tokens);
        $this->assertNotEmpty($analysis->extracted_profile['skills']);
        $this->assertSame(config('ai.prompt_version'), $analysis->prompt_version);
        $this->assertSame(config('ai.consent_version'), $analysis->consent_version);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $candidate->getKey(),
            'action' => 'resume.analysis.started',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $candidate->getKey(),
            'action' => 'resume.analysis.completed',
        ]);

        $ciphertext = DB::table('resume_analyses')->where('id', $analysis->getKey())->value('extracted_profile');
        $this->assertStringNotContainsString('Python', $ciphertext);
        Http::assertNothingSent();
    }

    public function test_candidate_cannot_view_another_candidates_analysis(): void
    {
        $owner = User::factory()->candidate()->create();
        $other = User::factory()->candidate()->create();
        $analysis = ResumeAnalysis::factory()->for($owner)->create();

        $this->actingAs($other)
            ->get(route('candidate.career.show', $analysis))
            ->assertForbidden();
    }

    public function test_candidate_career_page_renders_upload_and_fallback_guidance(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)
            ->get(route('candidate.career.index'))
            ->assertOk()
            ->assertSee('No analysis yet.')
            ->assertSee('no API tokens are spent');
    }

    public function test_candidate_can_view_their_completed_analysis(): void
    {
        $candidate = User::factory()->candidate()->create();
        $analysis = ResumeAnalysis::factory()->for($candidate)->create([
            'extracted_profile' => ['summary' => 'Python and SQL résumé snapshot.', 'skills' => []],
            'ai_analysis' => ['summary' => 'A practical profile.'],
        ]);

        $this->actingAs($candidate)
            ->get(route('candidate.career.show', $analysis))
            ->assertOk()
            ->assertSee('Your résumé analysis')
            ->assertSee('AI guidance only — verify eligibility');
    }

    public function test_admin_dashboard_shows_monthly_ai_usage_summary(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Career Assistant usage this month')
            ->assertSee('monthly cap');
    }

    public function test_ai_provider_receives_redacted_text_and_uses_the_output_token_cap(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'profile' => [
                                'summary' => 'A candidate with Python and SQL experience.',
                                'skills' => [['name' => 'Python', 'category' => 'technical', 'evidence' => 'Listed in experience.']],
                            ],
                            'analysis' => ['summary' => 'A practical early-career profile.'],
                            'skill_plan' => [],
                        ]),
                    ],
                ]],
                'usage' => ['prompt_tokens' => 140, 'completion_tokens' => 65],
            ]),
        ]);
        config([
            'ai.enabled' => true,
            'ai.provider' => 'openai',
            'ai.api_key' => 'test-provider-key',
            'ai.model' => 'test-model',
            'ai.max_output_tokens' => 800,
        ]);
        $candidate = User::factory()->candidate()->create(['name' => 'Alice Example']);

        $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => "Alice Example.\nPython and SQL experience.\nAddress: 10 Main Road.\nDOB: 01/01/1998.\nAadhaar: 1234 5678 9012.\nContact alice@example.com or 9876543210 for details.",
            'consent' => '1',
        ])->assertRedirect();

        $analysis = $candidate->resumeAnalyses()->firstOrFail();
        $this->assertTrue($analysis->is_ai_generated);
        $this->assertSame(140, $analysis->input_tokens);
        $this->assertSame(65, $analysis->output_tokens);
        $this->assertSame(1, DB::table('ai_monthly_spend')->where('month', now()->format('Y-m'))->count());
        $this->assertGreaterThan(0, (float) DB::table('ai_monthly_spend')->value('spent_usd'));

        Http::assertSent(function (Request $request): bool {
            $body = json_encode($request->data());

            return $request->url() === 'https://api.openai.com/v1/chat/completions'
                && $request->data()['max_tokens'] === 800
                && str_contains($body, '[email removed]')
                && str_contains($body, '[phone removed]')
                && str_contains($body, '[name removed]')
                && str_contains($body, '[address removed]')
                && str_contains($body, '[date removed]')
                && str_contains($body, '[id removed]')
                && ! str_contains($body, 'alice@example.com')
                && ! str_contains($body, '9876543210')
                && ! str_contains($body, 'Alice Example')
                && ! str_contains($body, '10 Main Road')
                && ! str_contains($body, '1234 5678 9012');
        });
        Http::assertSentCount(1);
    }

    public function test_monthly_spend_cap_falls_back_without_sending_an_ai_request(): void
    {
        Http::preventStrayRequests();
        config([
            'ai.enabled' => true,
            'ai.provider' => 'openai',
            'ai.api_key' => 'test-provider-key',
            'ai.model' => 'test-model',
            'ai.monthly_budget_usd' => 0.000001,
            'ai.input_cost_per_million' => 0.25,
            'ai.output_cost_per_million' => 1.5,
        ]);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => 'Python and SQL experience. Built a reporting database and worked with a small team.',
            'consent' => '1',
        ])->assertRedirect();

        $analysis = $candidate->resumeAnalyses()->firstOrFail();
        $this->assertFalse($analysis->is_ai_generated);
        $this->assertStringContainsString('monthly AI spend cap', $analysis->ai_plan['warning']);
        Http::assertNothingSent();
    }

    public function test_invalid_json_gets_one_repair_attempt_and_both_token_counts_are_saved(): void
    {
        Http::preventStrayRequests();
        $validJson = json_encode([
            'profile' => ['summary' => 'A candidate with practical web development skills.'],
            'analysis' => ['summary' => 'A useful early-career profile.'],
            'skill_plan' => [],
        ]);
        Http::fake([
            'api.openai.com/*' => Http::sequence()
                ->push([
                    'choices' => [['message' => ['content' => 'not valid JSON']]],
                    'usage' => ['prompt_tokens' => 90, 'completion_tokens' => 20],
                ])
                ->push([
                    'choices' => [['message' => ['content' => $validJson]]],
                    'usage' => ['prompt_tokens' => 30, 'completion_tokens' => 45],
                ]),
        ]);
        config([
            'ai.enabled' => true,
            'ai.provider' => 'openai',
            'ai.api_key' => 'test-provider-key',
            'ai.model' => 'test-model',
        ]);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => 'Web development experience using HTML and CSS. Built a small site for a local group.',
            'consent' => '1',
        ])->assertRedirect();

        $analysis = $candidate->resumeAnalyses()->firstOrFail();
        $this->assertTrue($analysis->is_ai_generated);
        $this->assertSame(120, $analysis->input_tokens);
        $this->assertSame(65, $analysis->output_tokens);
        Http::assertSentCount(2);
    }

    public function test_gemini_provider_uses_the_configured_model_and_key(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode([
                        'profile' => ['summary' => 'A candidate with data skills.'],
                        'analysis' => ['summary' => 'A useful profile.'],
                        'skill_plan' => [],
                    ])]]],
                ]],
                'usageMetadata' => ['promptTokenCount' => 80, 'candidatesTokenCount' => 40],
            ]),
        ]);
        config([
            'ai.enabled' => true,
            'ai.provider' => 'gemini',
            'ai.api_key' => 'gemini-test-key',
            'ai.model' => 'gemini-test-model',
        ]);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => 'Data analyst with SQL skills and experience creating reports for a small business.',
            'consent' => '1',
        ])->assertRedirect();

        $analysis = $candidate->resumeAnalyses()->firstOrFail();
        $this->assertTrue($analysis->is_ai_generated);
        $this->assertSame(80, $analysis->input_tokens);
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'models/gemini-test-model:generateContent')
            && str_contains($request->url(), 'key=gemini-test-key'));
    }

    public function test_openrouter_uses_its_default_endpoint(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'openrouter.ai/*' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'profile' => ['summary' => 'A candidate with technical skills.'],
                    'analysis' => ['summary' => 'A useful profile.'],
                    'skill_plan' => [],
                ])]]],
                'usage' => ['prompt_tokens' => 70, 'completion_tokens' => 35],
            ]),
        ]);
        config([
            'ai.enabled' => true,
            'ai.provider' => 'openrouter',
            'ai.api_key' => 'openrouter-test-key',
            'ai.model' => 'openrouter/test-model',
            'ai.base_url' => null,
        ]);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->post(route('candidate.career.analyze'), [
            'resume_text' => 'Technical candidate with cloud and networking skills and practical project experience.',
            'consent' => '1',
        ])->assertRedirect();

        $this->assertTrue($candidate->resumeAnalyses()->firstOrFail()->is_ai_generated);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://openrouter.ai/api/v1/chat/completions');
    }

    public function test_invalid_submission_returns_file_text_and_consent_errors(): void
    {
        $candidate = User::factory()->candidate()->create();

        $this->from(route('candidate.career.index'))
            ->actingAs($candidate)
            ->post(route('candidate.career.analyze'), [])
            ->assertRedirectBackWithErrors(['resume', 'resume_text', 'consent']);
    }

    public function test_daily_analysis_limit_returns_a_candidate_facing_message(): void
    {
        config(['ai.daily_limit' => 1, 'ai.api_key' => null]);
        $candidate = User::factory()->candidate()->create();
        $payload = [
            'resume_text' => 'Computer science graduate with Python skills and experience building a small web project.',
            'consent' => '1',
        ];

        $this->actingAs($candidate)
            ->post(route('candidate.career.analyze'), $payload)
            ->assertRedirect();

        $this->from(route('candidate.career.index'))
            ->post(route('candidate.career.analyze'), $payload)
            ->assertRedirect(route('candidate.career.index'))
            ->assertSessionHas('rate_limit', true);
    }

    public function test_skill_is_added_to_profile_only_when_the_candidate_selects_it(): void
    {
        $candidate = User::factory()->candidate()->create();
        $analysis = ResumeAnalysis::factory()->for($candidate)->create();
        $plan = CandidateSkillPlan::factory()->for($candidate)->create([
            'resume_analysis_id' => $analysis->getKey(),
            'skill_name' => 'Python',
        ]);

        $this->actingAs($candidate)
            ->patch(route('candidate.career.plan.update', $plan), ['status' => CandidateSkillPlan::STATUS_COMPLETED])
            ->assertRedirect();

        $this->assertSame(0, $candidate->skills()->count());

        $this->patch(route('candidate.career.plan.update', $plan), [
            'status' => CandidateSkillPlan::STATUS_COMPLETED,
            'add_to_profile' => '1',
        ])->assertRedirect();

        $skill = Skill::query()->where('slug', 'python')->firstOrFail();
        $this->assertDatabaseHas('profile_skills', [
            'user_id' => $candidate->getKey(),
            'skill_id' => $skill->getKey(),
        ]);
    }

    public function test_confirmed_profile_skill_refreshes_the_saved_job_match(): void
    {
        $candidate = User::factory()->candidate()->create();
        $skill = Skill::factory()->create(['name' => 'Python', 'slug' => 'python']);
        $job = Job::factory()->published()->create(['last_date' => now()->addDays(20)]);
        $job->skills()->attach($skill);
        $analysis = ResumeAnalysis::factory()->for($candidate)->create(['extracted_profile' => ['skills' => []]]);
        $plan = CandidateSkillPlan::factory()->for($candidate)->create([
            'resume_analysis_id' => $analysis->getKey(),
            'skill_name' => 'Python',
        ]);

        $this->actingAs($candidate)->patch(route('candidate.career.plan.update', $plan), [
            'status' => CandidateSkillPlan::STATUS_COMPLETED,
            'add_to_profile' => '1',
        ])->assertRedirect();

        $match = ResumeJobMatch::query()->where('resume_analysis_id', $analysis->getKey())->firstOrFail();
        $this->assertSame([$skill->name], $match->matched_skills);
        $this->assertSame(100, $match->breakdown['skills']);
    }

    public function test_skill_plan_has_staged_30_60_90_day_gaps_and_proof_of_skill(): void
    {
        $candidate = User::factory()->candidate()->create();
        $job = Job::factory()->published()->create();
        $skills = collect(['Python', 'SQL', 'Cloud', 'Networking', 'Cyber Security'])
            ->map(fn (string $name) => Skill::factory()->create(['name' => $name, 'slug' => str($name)->slug()->toString()]));
        $job->skills()->attach($skills);
        $matches = app(JobMatchService::class)->rankedMatches($candidate, [], null, 1);

        $plans = app(SkillPlanBuilder::class)->build($candidate, $matches, collect());

        $this->assertSame([30, 30, 60, 60, 90], array_column($plans, 'roadmap_days'));
        $this->assertNotEmpty($plans[0]['project']);
        $this->assertNotEmpty($plans[0]['proof']);
    }

    public function test_retention_command_permanently_removes_expired_analyses(): void
    {
        $analysis = ResumeAnalysis::factory()->create([
            'created_at' => now()->subDays((int) config('ai.retention_days', 180) + 1),
        ]);

        $this->artisan('ai:prune-resume-analyses')->assertSuccessful();

        $this->assertDatabaseMissing('resume_analyses', ['id' => $analysis->getKey()]);
    }

    public function test_resume_extractor_reads_docx_and_rejects_scanned_pdf(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gyrs-resume-');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('word/document.xml', '<?xml version="1.0"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>Python SQL and professional experience with customer reporting.</w:t></w:r></w:p></w:body></w:document>');
        $zip->close();
        $docx = new UploadedFile($path, 'candidate.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);

        try {
            $extracted = app(ResumeTextExtractor::class)->fromUpload($docx);
        } finally {
            unlink($path);
        }

        $this->assertStringContainsString('Python SQL', $extracted['text']);
        $this->assertSame('docx', $extracted['source_type']);

        $scannedPdf = UploadedFile::fake()->createWithContent('scanned.pdf', "%PDF-1.4\n%%EOF");
        try {
            app(ResumeTextExtractor::class)->fromUpload($scannedPdf);
            $this->fail('The extractor should reject PDFs that contain no readable text.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('resume', $exception->errors());
        }
    }

    public function test_resume_extractor_reads_text_pdf(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'gyrs-resume-');
        file_put_contents($path, $this->textPdf('Python and SQL experience. Built a database project and supported customer reporting teams.'));
        $pdf = new UploadedFile($path, 'candidate.pdf', 'application/pdf', null, true);

        try {
            $extracted = app(ResumeTextExtractor::class)->fromUpload($pdf);
        } finally {
            unlink($path);
        }

        $this->assertStringContainsString('Python and SQL experience', $extracted['text']);
        $this->assertSame('pdf', $extracted['source_type']);
    }

    public function test_upload_validation_rejects_oversized_and_unsupported_files(): void
    {
        config(['ai.max_file_kb' => 1]);
        $oversized = UploadedFile::fake()->createWithContent(
            'candidate.pdf',
            str_pad($this->textPdf('A résumé with readable experience and Python skills.'), 1100, ' '),
        );
        $candidate = User::factory()->candidate()->create();

        $this->from(route('candidate.career.index'))
            ->actingAs($candidate)
            ->post(route('candidate.career.analyze'), ['resume' => $oversized, 'consent' => '1'])
            ->assertRedirectBackWithErrors(['resume']);

        $unsupported = UploadedFile::fake()->createWithContent('candidate.exe', 'This is not a résumé file.');
        $this->from(route('candidate.career.index'))
            ->post(route('candidate.career.analyze'), ['resume' => $unsupported, 'consent' => '1'])
            ->assertRedirectBackWithErrors(['resume']);
    }

    private function textPdf(string $text): string
    {
        $escapedText = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $stream = 'BT /F1 12 Tf 50 700 Td ('.$escapedText.') Tj ET';
        $objects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Count 1 /Kids [3 0 R] >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            '5 0 obj << /Length '.strlen($stream)." >> stream\n".$stream."\nendstream endobj",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object."\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= 'xref'."\n0 ".count($offsets)."\n0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf.'trailer << /Size '.count($offsets).' /Root 1 0 R >>'."\nstartxref\n".$xrefOffset."\n%%EOF";
    }
}
