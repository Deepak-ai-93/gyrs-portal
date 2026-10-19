<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    /**
     * Ensure the authenticated administrator holds at least one of the given
     * granular permissions. Super administrators pass every check.
     *
     * Browsing into a forbidden section sends the administrator back to the
     * dashboard with an explanation; API and state-changing requests receive
     * an explicit 403.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        foreach ($permissions as $permission) {
            if ($user->hasAdminPermission($permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson() || ! $request->isMethod('GET')) {
            abort(403);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('error', 'Your administrator account does not have access to that section. Ask a super administrator to grant the required permission.');
    }
}
