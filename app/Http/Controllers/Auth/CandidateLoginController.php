<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CandidateLoginController extends Controller
{
    public function create(): View
    {
        return view('candidate.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $identifier = $request->string('identifier')->trim()->value();

        $candidate = User::query()
            ->where('role', User::ROLE_CANDIDATE)
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('phone', $identifier);
            })
            ->first();

        if (! $candidate || ! Auth::getProvider()->validateCredentials($candidate, $request->only('password'))) {
            throw ValidationException::withMessages([
                'identifier' => __('auth.failed'),
            ]);
        }

        Auth::login($candidate, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('candidate.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
