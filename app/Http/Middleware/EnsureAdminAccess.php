<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        if (! can_access_admin()) {
            abort(403, 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}
