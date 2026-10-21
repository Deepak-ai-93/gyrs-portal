<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     *
     * We store the intended role (candidate or admin) in the session so the
     * callback knows which flow the user initiated.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $role = $request->query('role', 'candidate');

        if (! in_array($role, [User::ROLE_CANDIDATE, User::ROLE_ADMIN], true)) {
            $role = User::ROLE_CANDIDATE;
        }

        session(['google_auth_role' => $role]);

        return Socialite::driver('google')
            ->redirectUrl(url('/auth/google/callback'))
            ->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(url('/auth/google/callback'))
                ->user();
        } catch (\Throwable $e) {
            return redirect()->route('candidate.login')
                ->with('error', 'Google sign-in failed. Please try again.');
        }

        $role = session('google_auth_role', User::ROLE_CANDIDATE);
        session()->forget('google_auth_role');

        // ── Admin flow ─────────────────────────────────────────────────
        if ($role === User::ROLE_ADMIN) {
            return $this->handleAdminLogin($googleUser);
        }

        // ── Candidate flow ─────────────────────────────────────────────
        return $this->handleCandidateLogin($googleUser);
    }

    /**
     * Admin: only allow Google sign-in for existing admin accounts.
     * We never auto-create admin accounts via OAuth.
     */
    private function handleAdminLogin($googleUser): RedirectResponse
    {
        $user = User::where('email', $googleUser->getEmail())
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (! $user) {
            return redirect()->route('admin.login')
                ->with('error', 'No administrator account found for this Google account.');
        }

        // Link Google ID if not already linked.
        if (! $user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Candidate: find an existing user by google_id or email, or create a
     * brand-new candidate account automatically.
     */
    private function handleCandidateLogin($googleUser): RedirectResponse
    {
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())
                ->where('role', User::ROLE_CANDIDATE)
                ->first();

        if ($user) {
            // Link Google ID if signing in by email match for the first time.
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user, remember: true);
            request()->session()->regenerate();

            return redirect()->intended(route('candidate.dashboard'));
        }

        // Auto-register a new candidate.
        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => null,
            'role' => User::ROLE_CANDIDATE,
            'enrollment_id' => $this->nextEnrollmentId(),
            'status' => User::STATUS_PENDING,
        ]);

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->route('candidate.dashboard')
            ->with('status', 'Welcome to GYRS! Complete your profile to get started.');
    }

    private function nextEnrollmentId(): string
    {
        $prefix = (string) config('gyrs.enrollment_prefix', 'GYRS-2026-');
        $sequence = User::query()->whereNotNull('enrollment_id')->count() + 1;

        do {
            $id = $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (User::query()->where('enrollment_id', $id)->exists());

        return $id;
    }
}
