@php
    // Registration form posts to register. Name/email/password/password_confirmation match
    // RegisteredUserController validation. Password fields never use old().
@endphp
<x-guest-layout>
    @php
        // The shared guest layout supplies the glass panel and theme controls.
        // This form keeps the field names expected by Breeze's registration controller.
    @endphp

    <h1>Create an account</h1>
    <p class="auth-description">Start managing your products.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label for="name">Name</label>

            @php
                // Add a decorative person icon without changing the submitted name field.
                // The existing auth-input CSS positions the icon and adds space for the text.
            @endphp
            <div class="auth-input">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                </svg>

                <input id="name" type="text" name="name"
                       value="{{ old('name') }}"
                       placeholder="Your name"
                       required autofocus autocomplete="name">
            </div>

            @error('name')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="email">Email</label>

            @php
                // Display an envelope inside the field while preserving Breeze's email input.
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
                       required autocomplete="username">
            </div>

            @error('email')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>

            @php
                // Password fields are never filled using old().
                // Breeze validates the password and hashes it before database storage.
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
                       placeholder="Create a password"
                       required autocomplete="new-password">
            </div>

            @error('password')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirm password</label>

            @php
                // The confirmation field must use this exact name.
                // Laravel's confirmed rule compares it with the password field.
                // Reuse the lock icon while keeping the exact confirmation field name.
                // Laravel compares this field with password during registration.
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
                       placeholder="Confirm your password"
                       required autocomplete="new-password">
            </div>

            @error('password_confirmation')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-submit">
            Create account
        </button>
    </form>

    <p class="auth-bottom">
        Already registered?
        <a href="{{ route('login') }}">Log in</a>
    </p>
</x-guest-layout>