<x-guest-layout>
    <h1>Page expired</h1>

    @auth
        <p class="auth-description">This form is out of date. Open Products to continue.</p>
        <a class="button button-primary" href="{{ route('product.index') }}">Back to Products</a>
    @else
        @if (request()->is('register'))
            <p class="auth-description">If you already created your account, sign in. Otherwise, open a fresh registration form.</p>
            <a class="button button-primary" href="{{ route('login') }}">Log in</a>
            <p class="auth-bottom"><a href="{{ route('register') }}">Open a new registration form</a></p>
        @else
            <p class="auth-description">This form is out of date. Sign in to continue.</p>
            <a class="button button-primary" href="{{ route('login') }}">Back to Login</a>
        @endif
    @endauth
</x-guest-layout>
