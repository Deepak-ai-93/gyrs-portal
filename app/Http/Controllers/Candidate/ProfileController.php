<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\ProfileUpdateRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->string('name')->trim()->value(),
                'email' => $request->input('email') ?: null,
                'phone' => $request->input('phone'),
                'district_id' => $request->input('district_id'),
                'qualification' => $request->string('qualification')->trim()->value(),
                'cgpa' => $request->input('cgpa'),
            ]);

            $ids = collect($request->input('skills', []))
                ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
                ->map(fn (string $skill) => Skill::firstOrCreate(
                    ['slug' => Str::slug(trim($skill))],
                    ['name' => trim($skill)],
                )->getKey())
                ->unique()
                ->values()
                ->all();

            $user->skills()->sync($ids);
        });

        return redirect()->route('candidate.dashboard')
            ->with('status', 'Profile updated successfully.');
    }
}
