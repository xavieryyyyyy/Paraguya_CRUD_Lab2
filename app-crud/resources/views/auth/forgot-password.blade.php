@php
    // Reset-link request form posts to password.email. This sends an email without changing the
    // password; status/errors show broker results and throttling.
@endphp
<x-guest-layout>
    <h1>Forgot password?</h1>
    <p class="auth-description">
        Enter your email to receive a reset link.
    </p>

    @php
        // After a successful request, Breeze returns a session status message.
        // Display it so the user knows the reset link was sent.
    @endphp
    @if (session('status'))
        <div class="auth-status" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @php
            // Send the email to Breeze's existing password-reset request route.
            // The CSRF token protects this form from forged submissions.
        @endphp
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>

            @php
                // Add an envelope icon without changing the password-reset request field.
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
                       required autofocus autocomplete="email">
            </div>

            @php
                // Show errors such as an invalid address or a throttled request.
                // old('email') preserves the submitted address when validation fails.
            @endphp
            @error('email')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-submit">
            Send reset link
        </button>
    </form>

    <p class="auth-bottom">
        <a href="{{ route('login') }}">Back to login</a>
    </p>
</x-guest-layout>