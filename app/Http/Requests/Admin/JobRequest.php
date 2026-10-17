<?php

namespace App\Http\Requests\Admin;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $jobId = $this->route('job')?->getKey();

        return [
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'exists:districts,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_text' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(Job::TYPES)],
            'description' => ['required', 'string'],
            'match_score' => ['nullable', 'integer', 'between:0,100'],
            'advertisement_number' => ['nullable', 'string', 'max:255'],
            'total_posts' => ['required', 'integer', 'min:0'],
            'posted_at' => ['nullable', 'date'],
            'last_date' => ['nullable', 'date', 'after_or_equal:posted_at'],
            'age_limit' => ['nullable', 'string', 'max:255'],
            'fees' => ['nullable', 'string', 'max:255'],
            'vacancy_breakup' => ['nullable', 'string'],
            'official_website_url' => ['nullable', 'url', 'max:2048'],
            'apply_url' => ['nullable', 'url', 'max:2048'],
            'notification_pdf_path' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in([
                Job::STATUS_DRAFT,
                Job::STATUS_PUBLISHED,
                Job::STATUS_CLOSED,
                Job::STATUS_ARCHIVED,
            ])],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            'eligibility' => ['nullable', 'array'],
            'eligibility.*' => ['string', 'max:500'],
            'responsibilities' => ['nullable', 'array'],
            'responsibilities.*' => ['string', 'max:500'],
            'selection_steps' => ['nullable', 'array'],
            'selection_steps.*' => ['string', 'max:500'],
            'application_steps' => ['nullable', 'array'],
            'application_steps.*' => ['string', 'max:500'],
        ];
    }
}
