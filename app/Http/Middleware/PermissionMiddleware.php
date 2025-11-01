<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();
        
        // Check if user has the permission using Spatie Permission
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Access denied. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}

