<?php

// Login/logout controller called from routes/auth.php. LoginRequest validates credentials;
// successful login regenerates the session and returns to the intended page or Products. Logout
// clears the session and returns to Login.


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Laravel has already run LoginRequest::rules before invoking this method.
        // authenticate() additionally checks credentials and rate limiting; invalid
        // input/credentials stop execution before session regeneration.
        $request->authenticate();

        // Change the session ID after login to reduce session fixation risk. This does not
        // create a new User record.
        $request->session()->regenerate();

        // Return to the protected page requested before login.
        // If no page was requested, open the product list.
        return redirect()->intended(route('product.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // End authentication on the configured web guard. The following two calls invalidate
        // session data and rotate the CSRF token; all three steps belong together.
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Return to Login after the session has been cleared.
        return redirect()->route('login');
    }
}
