@php
    // Component layout for x-guest-layout. Receives $slot from authentication views; loads
    // Inter, Vite assets, custom CSS, theme.js, and password-visibility.js.
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Product Manager | Authentication</title>

    @php
        // Load Inter for every page using this shared guest layout.
        // System fonts remain available if the font download fails.
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    @php
        // Keep Breeze's compiled assets available while customizing its forms.
        // Load our styles afterward so they can override the default appearance.
    @endphp
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="auth-page">
    <header class="navbar">
        <a href="{{ url('/') }}" class="brand">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M12 3 3 8v8l9 5 9-5V8Z"/>
                <path d="m3 8 9 5 9-5"/>
                <path d="M12 13v8"/>
                <path d="m7.5 5.5 9 5"/>
            </svg>

            Product Manager
        </a>

        <div class="navbar-tools">
            <span>Laboratory 3</span>

            @php
                // Reuse the existing theme script and its expected button ID.
                // Decorative icons are hidden from screen readers.
            @endphp
            <button type="button" id="theme-toggle" class="theme-toggle"
                    aria-label="Switch to light theme" aria-pressed="false">
                <svg class="icon sun-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
                    <path d="m5 5 1.5 1.5M17.5 17.5 19 19"/>
                    <path d="m5 19 1.5-1.5M17.5 6.5 19 5"/>
                </svg>

                <svg class="icon moon-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/>
                </svg>
            </button>
        </div>
    </header>

    <main class="auth-container">
        <div class="glass-panel auth-card">
            @php
                // Breeze pages use <x-guest-layout>.
                // Their content becomes $slot, which belongs inside this panel.
                // Keep $slot here; these pages do not use @section('content').
            @endphp
            {{ $slot }}
        </div>
    </main>

    <footer class="auth-footer">
        Product Manager &middot; Laboratory 3
    </footer>

    <script src="{{ asset('js/theme.js') }}" defer></script>
    @php
        // Add Show/Hide controls to password inputs after the HTML has been parsed.
        // This changes visibility only; Breeze still validates and saves the same fields.
    @endphp
    <script src="{{ asset('js/password-visibility.js') }}" defer></script>
</body>
</html>
