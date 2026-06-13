<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageHotels
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  'panel'|'create'  $ability
     */
    public function handle(Request $request, Closure $next, string $ability = 'panel'): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Wymagane logowanie.');
        }

        $allowed = match ($ability) {
            'create' => $user->canCreateHotel(),
            default => $user->canAccessHotelPanel(),
        };

        if (! $allowed) {
            abort(403, 'Brak uprawnień do panelu hoteli.');
        }

        return $next($request);
    }
}
