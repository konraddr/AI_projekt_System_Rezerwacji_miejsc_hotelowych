<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()
            ->route('manage.admin.users.edit', $user)
            ->with('success', 'Użytkownik '.$user->email.' został utworzony.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('manage.admin.users.index')
            ->with('success', 'Użytkownik '.$user->email.' został zaktualizowany.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('manage.admin.users.index')
                ->with('error', 'Nie możesz usunąć własnego konta.');
        }

        $email = $user->email;
        $user->delete();

        return redirect()
            ->route('manage.admin.users.index')
            ->with('success', 'Użytkownik '.$email.' został usunięty.');
    }
}
