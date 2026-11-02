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

            session()->forget('google_auth_role');

            return $this->handleGoogleLogin($googleUser, $role);
        } catch (\Throwable $e) {
            session()->forget('google_auth_role');
            Log::error('Google OAuth callback failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route($loginRoute)
                ->with('error', 'Google sign-in failed. Please try again.');
        }
    }

    /**
     * Authenticate an existing user or register a new candidate.
     */
    private function handleGoogleLogin($googleUser, string $role): RedirectResponse
    {
        // 1. Find existing account by Google ID or verified Email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link Google ID and sync avatar / email verification if not yet set
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

            return $user->isAdmin()
                ? redirect()->intended(route('admin.dashboard'))
                : redirect()->intended(route('candidate.dashboard'));
        }

        // 2. Admin accounts cannot be auto-created via OAuth
        if ($role === User::ROLE_ADMIN) {
            return redirect()->route('admin.login')
                ->with('error', 'No administrator account found for this Google account.');
        }

        // 3. Auto-register a new candidate with safe name fallback
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
