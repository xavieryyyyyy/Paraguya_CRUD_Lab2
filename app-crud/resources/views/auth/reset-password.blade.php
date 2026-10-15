@php
    // New-password form posts to password.store. Preserve the hidden token from the reset URL
    // and both password field names; NewPasswordController verifies and saves the change.
@endphp
<x-guest-layout>
    <h1>Reset password</h1>
    <p class="auth-description">
        Choose a new password for your account.
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        @php
            // The email link contains a reset token in its URL.
            // Submit that token so Laravel can verify the password-reset request.
            // Keep this hidden field even though it is not visible on the page.
        @endphp
        <input type="hidden" name="token"
               value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email">Email</label>

            @php
                // Initially display the email supplied by the reset link.
                // After validation fails, old() restores the submitted address.
                // Add an envelope icon while preserving the email from the reset link.
                // old() restores the submitted email if validation fails.
            @endphp
            <div class="auth-input">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="m3 6 9 7 9-7"/>
                </svg>

                <input id="email" type="email" name="email"
                       value="{{ old('email', $request->email) }}"
                       required autofocus autocomplete="username">
            </div>

            @error('email')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">New password</label>

            @php
                // Display a decorative lock without changing the new-password field.
                // Password values are not restored after validation fails.
            @endphp
            <div class="auth-input">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <rect x="5" y="10" width="14" height="11" rx="2"/>
                    <path d="M8 10V6a4 4 0 0 1 8 0v4"/>
                </svg>

                <input id="password" type="password" name="password"
                       placeholder="Enter a new password"
                       required autocomplete="new-password">
            </div>

            @error('password')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirm password</label>

            @php
                // Laravel compares password_confirmation with password.
                // Password values are never restored into the HTML.
            @endphp
            <div class="auth-input">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <rect x="5" y="10" width="14" height="11" rx="2"/>
                    <path d="M8 10V6a4 4 0 0 1 8 0v4"/>
                </svg>

                <input id="password_confirmation" type="password"
                       name="password_confirmation"
                       placeholder="Confirm your new password"
                       required autocomplete="new-password">
            </div>

            @error('password_confirmation')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-submit">
            Reset password
        </button>
    </form>

    <p class="auth-bottom">
        <a href="{{ route('login') }}">Back to login</a>
    </p>
</x-guest-layout>