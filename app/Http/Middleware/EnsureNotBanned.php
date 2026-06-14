<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->isBanned()) {
            if ($request->is('api/*')) {
                $user->currentAccessToken()?->delete();

                return response()->json([
                    'message' => 'Twoje konto zostało zablokowane.',
                ], 403);
            }

            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Twoje konto zostało zablokowane.']);
        }

        return $next($request);
    }
}
