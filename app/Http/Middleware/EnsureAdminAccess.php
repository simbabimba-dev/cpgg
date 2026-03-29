<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, __('User does not have the right permissions.'));
        }

        if ($user->can('*') || $user->hasRole('Admin')) {
            return $next($request);
        }

        $hasAnyAdminPermission = $user->getAllPermissions()
            ->contains(static fn ($permission) => is_string($permission->name) && str_starts_with($permission->name, 'admin.'));

        if (!$hasAnyAdminPermission) {
            abort(403, __('User does not have the right permissions.'));
        }

        return $next($request);
    }
}
