@php
    // Login form posts to login. email/password/remember names match LoginRequest; CSRF
    // protects the form. Errors and reset-status messages are rendered inside x-guest-layout.
@endphp
<x-guest-layout>
    @php
        // The guest layout supplies the navbar, glass panel, font, and theme.
        // This page supplies the login form displayed inside its $slot.
    @endphp

    <h1>Welcome back</h1>
    <p class="auth-description">Sign in to manage your products.</p>

    @php
        // Show feedback from actions such as completing a password reset.
    @endphp
    @if (session('status'))
        <div class="auth-status" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @php
            // Protect the submitted form with Laravel's session CSRF token.
            // Keep the email, password, and remember field names for Breeze.
        @endphp
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>

            @php
                // The wrapper positions a decorative icon inside the field.
                // The input keeps its original name, value, and login attributes.
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
                       value="{{ old('email') }}"
                       placeholder="you@example.com"
                       required autofocus autocomplete="username">
            </div>

            @error('email')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>

            @php
                // Never restore a password using old() or place it in the markup.
                // autocomplete helps password managers recognize a login field.
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
                       placeholder="Enter your password"
                       required autocomplete="current-password">
            </div>

            @error('password')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-options">
            <label for="remember_me" class="remember-label">
                @php
                    // Breeze reads remember as a boolean when authenticating.
                    // Preserve the choice after failed login, but never preserve the password.
                @endphp
                <input id="remember_me" type="checkbox" name="remember" value="1"
                       @checked(old('remember'))>
                <span>Remember me</span>
            </label>

            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        @php
            // The submit button sends the form to Breeze's existing login route.
            // auth-submit provides a white button in dark mode and dark in light mode.
        @endphp
        <button type="submit" class="auth-submit">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M15 3h5v18h-5"/>
                <path d="M3 12h12"/>
                <path d="m10 7 5 5-5 5"/>
            </svg>

            Log in
        </button>
    </form>

    <p class="auth-bottom">
        New here?
        <a href="{{ route('register') }}">Create an account</a>
    </p>
</x-guest-layout>
