<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => User::normalizeTrigram($request->input('username')),
        ]);

        $request->validate([
            'username' => ['required', 'string', 'size:'.User::TRIGRAM_LENGTH, 'regex:/^[A-Za-z]{3}$/'],
        ], [], [
            'username' => 'trigram',
        ]);

        $user = User::query()
            ->where('username', $request->string('username')->value())
            ->first();

        if (! $user || blank($user->email)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => __('We could not find an account with a recoverable email for that trigram.')]);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('username'))
                        ->withErrors(['username' => __($status)]);
    }
}
