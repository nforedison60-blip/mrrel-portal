<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'You do not have access to this area.');
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (! in_array($user->role, $roles, true)) {
            // Someone opened a page that belongs to another kind of account (an old bookmark,
            // for example). Take them to their own area instead of showing an error page.
            if ($request->isMethod('GET')) {
                $home = match ($user->role) {
                    'Admin'    => 'admin.dashboard',
                    'Provider' => 'provider.dashboard',
                    'Landlord' => 'landlord.properties',
                    default    => 'customer.dashboard',
                };
                return redirect()->route($home)
                    ->with('error', 'That page is for a different kind of account, so we brought you back to your own area.');
            }
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
