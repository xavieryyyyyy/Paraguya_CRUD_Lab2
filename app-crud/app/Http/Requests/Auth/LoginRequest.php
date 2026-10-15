<?php

// Login-specific validation object injected into AuthenticatedSessionController::store().
// Checks input, limits failed login attempts, and asks the web guard to authenticate using the
// remember checkbox value.


namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // Check the failed-attempt limit before trying credentials. The limit uses an email/IP
        // key; it is separate from the reset-email cooldown in config/auth.php.
        $this->ensureIsNotRateLimited();

        // only() selects email/password, and boolean(remember) converts the checkbox to
        // true/false. Auth::attempt loads the User, checks its password hash, and starts
        // authentication when valid. The ! operator enters this branch when authentication
        // fails.
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Count this failed attempt and throw a validation error for the email field. The
            // form receives the message after Laravel redirects back; the password is not
            // flashed.
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // A successful login clears the failed-attempt counter for this email/IP combination.
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Allow fewer than five recorded failures. Once the limit is reached, the following
        // code reports the remaining wait and throws a validation exception.
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Build a normalized key from lowercase email and request IP, so the limiter can
        // identify this login source consistently.
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
