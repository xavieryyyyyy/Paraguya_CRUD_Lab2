<?php

// Reset Password controller. The email link supplies a token and email to the view. The POST
// handler validates input, asks the password broker to verify the token, hashes the new
// password, and returns to Login with feedback.


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        // Pass the current Request to the view so its hidden token comes from the route and its
        // initial email comes from the reset link query string.
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The broker verifies that the email/token match a valid, unexpired reset request
        // before calling the callback. Invalid tokens never reach forceFill/save.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            // This closure runs after token verification. use($request) makes the outer Request
            // available inside it. $user is the account selected by the broker.
            function (User $user) use ($request) {
                // Set only the explicitly chosen password hash and a new random remember token.
                // forceFill bypasses fillable, so it is limited to these server-selected keys,
                // not arbitrary form input. save() persists the existing User.
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Successful reset returns to Login with feedback. Failed reset keeps the email
        // and displays the broker error; the new password is not returned to the form.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
