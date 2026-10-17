<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\RegisterRequest;
use App\Models\District;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CandidateRegisterController extends Controller
{
    public function create(): View
    {
        return view('candidate.auth.register', [
            'districts' => District::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $candidate = DB::transaction(function () use ($request) {
            $candidate = User::create([
                'name' => $request->string('name')->trim()->value(),
                'email' => $request->input('email') ?: null,
                'phone' => $request->input('phone'),
                'password' => $request->input('password'),
                'role' => User::ROLE_CANDIDATE,
                'district_id' => $request->input('district_id'),
                'enrollment_id' => $this->nextEnrollmentId(),
                'qualification' => $request->string('qualification')->trim()->value(),
                'cgpa' => $request->input('cgpa'),
                'status' => User::STATUS_PENDING,
            ]);

            $this->syncSkills($candidate, $request->input('skills', []));

            return $candidate;
        });

        Auth::login($candidate);
        $request->session()->regenerate();

        return redirect()->route('candidate.dashboard')
            ->with('status', 'Registration successful. Welcome to GYRS.');
    }

    /**
     * @param  array<int, string>  $skills
     */
    private function syncSkills(User $candidate, array $skills): void
    {
        $ids = collect($skills)
            ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
            ->map(fn (string $skill) => Skill::firstOrCreate(
                ['slug' => Str::slug(trim($skill))],
                ['name' => trim($skill)],
            )->getKey())
            ->unique()
            ->values()
            ->all();

        $candidate->skills()->sync($ids);
    }

    private function nextEnrollmentId(): string
    {
        $prefix = (string) config('gyrs.enrollment_prefix', 'GYRS-2026-');
        $sequence = User::query()->whereNotNull('enrollment_id')->count() + 1;

        do {
            $candidate = $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (User::query()->where('enrollment_id', $candidate)->exists());

        return $candidate;
    }
}
