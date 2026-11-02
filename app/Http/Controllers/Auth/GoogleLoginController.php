<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Determine the redirect URL for Google OAuth callback.
     * Uses config services.google.redirect if set (resolving relative paths to full URLs),
     * otherwise defaults to route('auth.google.callback').
     */
    protected function getRedirectUrl(): string
    {
        $redirect = config('services.google.redirect');

        if (! empty($redirect)) {
            if (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://')) {
                return $redirect;
            }

            return url($redirect);
        }

        return route('auth.google.callback');
    }

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
            ->redirectUrl($this->getRedirectUrl())
            ->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $role = session('google_auth_role', User::ROLE_CANDIDATE);
        $loginRoute = ($role === User::ROLE_ADMIN) ? 'admin.login' : 'candidate.login';

        if ($request->has('error')) {
            session()->forget('google_auth_role');
            $errorDescription = $request->query('error_description') ?: 'Google sign-in was cancelled.';
            Log::warning('Google OAuth callback returned error: '.$request->query('error'), [
                'description' => $errorDescription,
            ]);

            return redirect()->route($loginRoute)->with('error', $errorDescription);
        }

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->getRedirectUrl())
                ->user();
        } catch (\Throwable $e) {
            session()->forget('google_auth_role');
            Log::error('Google OAuth callback failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route($loginRoute)
                ->with('error', 'Google sign-in failed. Please try again.');
        }

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

        // Link Google ID and sync avatar / email verification if needed.
        $updates = [];
        if (! $user->google_id) {
            $updates['google_id'] = $googleUser->getId();
        }
        if (! $user->avatar && $googleUser->getAvatar()) {
            $updates['avatar'] = $googleUser->getAvatar();
        }
        if (! $user->email_verified_at) {
            $updates['email_verified_at'] = now();
        }
        if (! empty($updates)) {
            $user->forceFill($updates)->save();
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
            // Link Google ID and sync avatar / email verification if needed.
            $updates = [];
            if (! $user->google_id) {
                $updates['google_id'] = $googleUser->getId();
            }
            if (! $user->avatar && $googleUser->getAvatar()) {
                $updates['avatar'] = $googleUser->getAvatar();
            }
            if (! $user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }
            if (! empty($updates)) {
                $user->forceFill($updates)->save();
            }

            Auth::login($user, remember: true);
            request()->session()->regenerate();

            return redirect()->intended(route('candidate.dashboard'));
        }

        // Auto-register a new candidate with safe name fallback.
        $rawName = (string) ($googleUser->getName() ?: $googleUser->getNickname());
        if (trim($rawName) === '') {
            $emailParts = explode('@', (string) $googleUser->getEmail());
            $cleanPrefix = str_replace(['.', '_', '-'], ' ', $emailParts[0] ?? '');
            $rawName = trim($cleanPrefix) !== '' ? Str::headline($cleanPrefix) : 'Candidate';
        }

        $user = User::create([
            'name' => $rawName,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => null,
            'role' => User::ROLE_CANDIDATE,
            'enrollment_id' => $this->nextEnrollmentId(),
            'status' => User::STATUS_PENDING,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

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
