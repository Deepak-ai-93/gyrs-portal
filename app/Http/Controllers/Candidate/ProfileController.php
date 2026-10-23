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
        $phoneChanged = $user->phone !== $request->input('phone');

        DB::transaction(function () use ($request, $user, $phoneChanged) {
            $user->update([
                'name' => $request->string('name')->trim()->value(),
                'email' => $request->input('email') ?: null,
                'phone' => $request->input('phone'),
                'district_id' => $request->input('district_id'),
                'qualification' => $request->string('qualification')->trim()->value(),
                'cgpa' => $request->input('cgpa'),
            ]);

            // A new mobile number has not been proven yet: require a fresh OTP.
            if ($phoneChanged) {
                $user->forceFill(['phone_verified_at' => null])->save();
            }

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
