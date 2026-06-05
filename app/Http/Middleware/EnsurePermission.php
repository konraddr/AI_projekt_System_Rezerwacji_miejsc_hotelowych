<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions  Wartości liczbowe enum UserPermission (np. 0, 1, 5)
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $allowed = array_map(
            fn (string $value): UserPermission => UserPermission::from((int) $value),
            $permissions
        );

        if (! $user->hasPermission(...$allowed)) {
            abort(403);
        }

        return $next($request);
    }
}
