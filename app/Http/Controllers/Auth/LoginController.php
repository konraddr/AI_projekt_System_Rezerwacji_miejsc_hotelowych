<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function attemptLogin(Request $request): bool
    {
        $attempted = $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );

        if ($attempted && $this->guard()->user()->isBanned()) {
            $this->guard()->logout();

            return false;
        }

        return $attempted;
    }

    protected function sendFailedLoginResponse(Request $request): void
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user !== null && $user->isBanned()) {
            throw ValidationException::withMessages([
                'email' => ['Twoje konto zostało zablokowane.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}
