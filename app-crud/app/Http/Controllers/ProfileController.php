<?php

// Generated Breeze profile actions: display the current user, update profile details, or delete
// the account after current-password validation. Product records have no user_id relationship,
// so deleting an account does not cascade-delete products.


namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Work on the current authenticated account, not a user ID chosen by the browser.
        // validated() includes only fields accepted by ProfileUpdateRequest.
        $request->user()->fill($request->validated());

        // isDirty checks whether this attribute changed from the loaded database value. A
        // changed email clears the old verification timestamp before saving.
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Require the current password and store failures in the userDeletion error bag,
        // keeping deletion errors separate from profile/password-update forms.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
