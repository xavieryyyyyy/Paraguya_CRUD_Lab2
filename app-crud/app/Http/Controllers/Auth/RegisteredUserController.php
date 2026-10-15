<?php

// Registration controller for GET/POST register. Validates account details, creates a User with
// a password hash, fires Registered, signs in that user, then redirects to Dashboard.
// Registration currently has a different default destination from login.


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Require a name, a valid lowercase unique email, and a confirmed password. Defaults
        // supplies Laravel's configured password rule; without customization its minimum is
        // eight characters.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Insert the validated account fields. Hash::make stores a one-way password hash, not
        // the original password. The User hashed cast recognizes an already valid hash rather
        // than hashing it a second time.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch the account-created event so Laravel listeners can react. Verification
        // notification enforcement depends on the User model implementing MustVerifyEmail,
        // which is currently absent.
        event(new Registered($user));

        // Sign in the newly created user automatically. This registration flow currently
        // returns Dashboard; AuthenticatedSessionController handles the separate login redirect
        // to Products.
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
