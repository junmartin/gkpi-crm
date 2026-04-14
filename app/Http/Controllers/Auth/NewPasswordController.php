<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $user = User::query()
            ->where('email', $request->query('email'))
            ->first();

        return view('auth.reset-password', [
            'request' => $request,
            'username' => $user?->username,
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => User::normalizeTrigram($request->input('username')),
        ]);

        $request->validate([
            'token' => ['required'],
            'username' => ['required', 'string', 'size:'.User::TRIGRAM_LENGTH, 'regex:/^[A-Za-z]{3}$/'],
            'password' => ['required', 'confirmed', 'regex:'.User::PIN_REGEX],
        ], [], [
            'username' => 'trigram',
            'password' => 'PIN',
            'password_confirmation' => 'PIN confirmation',
        ]);

        $user = User::query()
            ->where('username', $request->string('username')->value())
            ->first();

        if (! $user || blank($user->email)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['username' => __('We could not reset the PIN for that trigram.')]);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            [
                'email' => $user->email,
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors(['username' => __($status)]);
    }
}
