@extends('layouts.candidate')

@section('title', 'Career Assistant — GYRS')

@php
    $profile = $latest?->extracted_profile ?? [];
    $latestCompleted = $latest?->isCompleted() ?? false;
    $latestSkills = collect($profile['skills'] ?? [])->pluck('name')->filter()->take(6);
@endphp

@section('content')
    <section class="section" style="padding-bottom:0;">
        <div class="container">
            <div class="page-head">
                <div>
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Career Assistant</span>
                    <h1 style="margin-top:var(--space-3);">Turn your résumé into a plan.</h1>
                    <p class="lead">See what your résumé already says well, which open vacancies fit you, and the skills worth building next.</p>
                </div>
                <x-button variant="ghost" size="sm" :href="route('candidate.dashboard')">
                    <x-icon name="arrow-left" :size="16" /> Back to dashboard
                </x-button>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:var(--space-6);">
        <div class="container">
            @if ($latestCompleted)
                <div class="career-latest panel panel-accent">
                    <div class="career-latest-main">
                        <span class="badge badge-success"><x-icon name="check-circle" :size="13" /> Analysis ready</span>
                        <h2 style="margin:var(--space-3) 0 var(--space-2);">{{ $profile['summary'] ?? 'Your résumé analysis is ready.' }}</h2>
                        <p class="text-sm muted" style="margin:0;">
                            {{ $latest->sourceLabel() }} · analysed {{ $latest->completed_at?->diffForHumans() ?? 'recently' }}
                            @if ($latest->is_ai_generated)
                                · AI-assisted
                            @else
                                · local analysis
                            @endif
                        </p>
                        @if ($latestSkills->isNotEmpty())
                            <div class="badge-row" style="margin-top:var(--space-3);">
                                @foreach ($latestSkills as $skill)
                                    <span class="badge">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="career-latest-actions">
                        <x-button variant="primary" :href="route('candidate.career.show', $latest)">View my results</x-button>
                        <x-button variant="outline" :href="route('candidate.career.index') . '#analyse'">Analyse again</x-button>
                    </div>
                </div>
            @elseif ($latest && $latest->status === \App\Models\ResumeAnalysis::STATUS_FAILED)
                <div class="alert alert-danger" style="margin-bottom:var(--space-6);">
                    <strong>We could not finish the last analysis.</strong>
                    <p style="margin:8px 0 0;">{{ $latest->error_message ?: 'Please try again with a PDF, DOCX or pasted text.' }}</p>
                </div>
            @endif

            <!-- No analysis yet or AI unavailable -->
            @if (! $latest)
                <div class="alert alert-info" style="margin-bottom:var(--space-6);">
                    <strong>No analysis yet.</strong>
                    <p style="margin:8px 0 0;">Upload a résumé or paste text to get started with career suggestions.</p>
                </div>
            @endif

            @if ($latest && in_array($latest->status, [\App\Models\ResumeAnalysis::STATUS_PROCESSING, \App\Models\ResumeAnalysis::STATUS_FAILED]))
                <div class="alert alert-warning" style="margin-bottom:var(--space-6);">
                    @if ($latest->status === \App\Models\ResumeAnalysis::STATUS_PROCESSING)
                        <strong>Analysing your résumé…</strong>
                    @else
                        <strong>Analysis failed.</strong>
                    @endif
                    <p style="margin:8px 0 0;">{{ $latest->error_message ?: 'Please try again or paste the text instead.' }}</p>
                </div>
            @endif

            @if ($latest && $latest->status === \App\Models\ResumeAnalysis::STATUS_FAILED)
                @if ($latest->error_message && str_contains($latest->error_message, 'rate limit'))
                    <div class="alert alert-warning" style="margin-bottom:var(--space-6);">
                        <strong>Rate limit exceeded.</strong>
                        <p style="margin:8px 0 0;">You have reached the daily analysis limit. Please try again tomorrow.</p>
                    </div>
                @elseif ($latest->error_message && str_contains($latest->error_message, 'AI'))
                    <div class="alert alert-warning" style="margin-bottom:var(--space-6);">
                        <strong>AI service unavailable.</strong>
                        <p style="margin:8px 0 0;">The AI provider is currently unavailable. A local analysis will be used instead.</p>
                    </div>
                    @endif
                @endif

            @elseif ($latest && $latest->status === \App\Models\ResumeAnalysis::STATUS_COMPLETED)
                <div class="career-latest panel panel-accent">
                    <div class="career-latest-main">
                        <span class="badge badge-success"><x-icon name="check-circle" :size="13" /> Analysis ready</span>
                        <h2 style="margin:var(--space-3) 0 var(--space-2);">{{ $profile['summary'] ?? 'Your résumé analysis is ready.' }}</h2>
                        <p class="text-sm muted" style="margin:0;">
                            {{ $latest->sourceLabel() }} · analysed {{ $latest->completed_at?->diffForHumans() ?? 'recently' }}
                            @if ($latest->is_ai_generated)
                                · AI-assisted
                            @else
                                · local analysis
                            @endif
                        </p>
                        @if ($latestSkills->isNotEmpty())
                            <div class="badge-row" style="margin-top:var(--space-3);">
                                @foreach ($latestSkills as $skill)
                                    <span class="badge">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="career-latest-actions">
                        <x-button variant="primary" :href="route('candidate.career.show', $latest)">View my results</x-button>
                        <x-button variant="outline" :href="route('candidate.career.index') . '#analyse'">Analyse again</x-button>
                    </div>
                </div>
            @elseif ($latest && $latest->status === \App\Models\ResumeAnalysis::STATUS_FAILED)
                <div class="alert alert-danger" style="margin-bottom:var(--space-6);">
                    <strong>We could not finish the last analysis.</strong>
                    <p style="margin:8px 0 0;">{{ $latest->error_message ?: 'Please try again with a PDF, DOCX or pasted text.' }}</p>
                </div>
            @endif

            <div class="career-layout">
                <div class="career-main" id="analyse">
                    <form method="POST" action="{{ route('candidate.career.analyze') }}" enctype="multipart/form-data" class="panel career-form" data-ai-form>
                        @csrf

                        <div class="between" style="margin-bottom:var(--space-4);">
                            <div>
                                <h2 style="margin-bottom:var(--space-1);">Analyse your résumé</h2>
                                <p class="text-sm muted" style="margin:0;">Upload a file or paste the text. We remove contact details before sending anything to an AI provider.</p>
                            </div>
                            <span class="badge badge-info"><x-icon name="file" :size="13" /> PDF, DOCX or TXT</span>
                        </div>

                        <div class="career-upload" data-file-drop>
                            <input
                                type="file"
                                name="resume"
                                id="career-resume"
                                accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain"
                                class="career-file-input"
                                data-file-input
                            >
                            <label for="career-resume" class="career-upload-label">
                                <span class="career-upload-icon"><x-icon name="file" :size="24" /></span>
                                <span>
                                    <strong>Choose a résumé</strong>
                                    <span class="text-sm muted">or drag and drop it here · up to {{ config('ai.max_file_kb') }} KB</span>
                                </span>
                            </label>
                            <p class="career-file-name text-sm" data-file-name hidden></p>
                            @error('resume')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <details class="career-paste" @if ($errors->has('resume_text')) open @endif>
                            <summary>Or paste résumé text instead</summary>
                            <div class="form-field" style="margin-top:var(--space-3);">
                                <label class="form-label" for="career-resume-text">Résumé text</label>
                                <textarea
                                    id="career-resume-text"
                                    name="resume_text"
                                    class="form-control"
                                    rows="8"
                                    maxlength="20000"
                                    placeholder="Paste your education, experience, skills, projects and certifications here..."
                                >{{ old('resume_text') }}</textarea>
                                @error('resume_text')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </details>

                        <div class="form-grid">
                            <x-input name="target_role" label="Target role (optional)" :value="old('target_role')" hint="For example: Junior Software Engineer, Data Entry Operator, Cyber Security Analyst." />
                            <div class="form-field">
                                <p class="form-label">What happens next</p>
                                <div class="career-steps">
                                    <span><x-icon name="check" :size="14" /> Read your résumé</span>
                                    <span><x-icon name="check" :size="14" /> Match open vacancies</span>
                                    <span><x-icon name="check" :size="14" /> Build a skill plan</span>
                                </div>
                            </div>
                        </div>

                        <label class="checkbox-field text-sm" style="margin-top:var(--space-4);">
                            <input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
                            <span>I understand that my résumé will be processed to generate career suggestions. Contact details are removed before analysis. I can delete the analysis at any time.</span>
                        </label>
                        @error('consent')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <div class="between career-form-foot">
                            <p class="text-sm muted" style="margin:0;">
                                <x-icon name="lock" :size="14" /> Your file is processed for this analysis and is not stored.
                            </p>
                            <x-button variant="primary" type="submit" data-ai-submit>
                                <x-icon name="sparkles" :size="16" /> Analyse résumé
                            </x-button>
                        </div>

                        <div class="career-progress" data-ai-progress hidden aria-live="polite">
                            <div class="career-progress-head">
                                <span class="spinner" aria-hidden="true"></span>
                                <strong>Analysing your résumé…</strong>
                            </div>
                            <ol>
                                <li data-ai-step="1" class="is-active">Reading your résumé</li>
                                <li data-ai-step="2">Extracting skills and experience</li>
                                <li data-ai-step="3">Matching open vacancies</li>
                                <li data-ai-step="4">Building your skill plan</li>
                            </ol>
                            <p class="text-sm muted">This can take up to half a minute. Keep this page open.</p>
                        </div>
                    </form>
                </div>

                <aside class="career-aside">
                    <div class="panel">
                        <h3><x-icon name="shield" :size="18" /> Your privacy</h3>
                        <ul class="career-privacy-list text-sm">
                            <li>Email, phone and ID numbers are removed before analysis.</li>
                            <li>Your file is processed in memory and discarded.</li>
                            <li>Results are visible only to you and can be deleted.</li>
                            <li>AI suggestions are guidance — always check the official notification.</li>
                        </ul>
                    </div>

                    @if ($history->count() > 1)
                        <div class="panel">
                            <h3>Recent analyses</h3>
                            <div class="career-history">
                                @foreach ($history as $item)
                                    @if ($item->isCompleted())
                                        <a href="{{ route('candidate.career.show', $item) }}" class="career-history-row">
                                            <span>
                                                <strong>{{ $item->target_role ?: $item->sourceLabel() }}</strong>
                                                <span class="text-sm muted">{{ $item->completed_at?->format('d M Y') }}</span>
                                            </span>
                                            <x-icon name="arrow-right" :size="16" />
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
