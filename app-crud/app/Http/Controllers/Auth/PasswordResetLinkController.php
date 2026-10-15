<?php

// Forgot Password controller for password.request/password.email routes. The view collects an
// email; the password broker creates a reset token and sends a notification through
// config/mail.php. No password is changed at this stage.


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Pass only the email to Laravel's password broker. It finds the User, enforces reset
        // throttling, stores a token hash, and sends the ResetPassword notification through the
        // configured mailer. A returned status describes the outcome.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Compare the broker status with its success constant. The ternary expression returns
        // one of two redirect chains. __() translates the status key into the message shown in
        // the view.
        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
