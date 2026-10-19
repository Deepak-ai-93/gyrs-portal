<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Ensure the authenticated user has one of the given roles.
     *
     * When a signed-in user browses into the other area (for example a
     * candidate opening the admin dashboard) we send them back to their own
     * dashboard with an explanation instead of a dead-end 403 page.
     * API calls and state-changing requests still receive an explicit 403.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($roles === [] || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        if ($request->expectsJson() || ! $request->isMethod('GET')) {
            abort(403);
        }

        return redirect()
            ->route($user->isAdmin() ? 'admin.dashboard' : 'candidate.dashboard')
            ->with('status', $this->wrongAreaMessage($user));
    }

    protected function wrongAreaMessage(object $user): string
    {
        return $user->isAdmin()
            ? 'The candidate area requires a candidate account. You are still signed in as an administrator — sign out if you want to view it.'
            : 'The administrator area requires an administrator account. You are still signed in as a candidate — sign out if you need to switch accounts.';
    }
}
