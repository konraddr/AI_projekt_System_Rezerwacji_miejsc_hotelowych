<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'last_name', 'phone', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $request->user()->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil został zaktualizowany.');
    }

    public function destroy(DestroyProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('hotels.index')
            ->with('success', 'Twoje konto zostało usunięte.');
    }
}
