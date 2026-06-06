<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Wymagane logowanie.');
        }

        $adminEmails = config('maciej.admin_emails', []);

        if (! in_array($user->email, $adminEmails, true)) {
            abort(403, 'Brak uprawnień administratora.');
        }

        return $next($request);
    }
}
