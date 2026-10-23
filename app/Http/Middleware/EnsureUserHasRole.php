<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(in_array($request->user()?->role, $roles, true), 403);

        if ($request->user()?->role === User::ROLE_ADMIN && in_array(User::ROLE_ADMIN, $roles, true)) {
            return app(AdminModuleAccess::class)->handle($request, $next);
        }

        return $next($request);
    }
}
