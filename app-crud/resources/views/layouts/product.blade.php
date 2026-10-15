@php
    // Section-based layout for products views. Uses title/content yields, not $slot. Loads
    // custom CSS, theme.js, product-messages.js, and includes a POST logout form.
@endphp
@php
    // SHARED VIEW LAYOUT: One HTML shell for the list, create, and edit pages. Child views
    // extend layouts.product and supply sections. These PHP comments are removed on the
    // server and do not appear in the page source.
@endphp
<!DOCTYPE html>
@php
    // Declare HTML5 above and English here so browsers and assistive tools know the document
    // format/language.
@endphp
<html lang="en">
<head>
    @php
        // UTF-8 supports ordinary text and many international characters.
    @endphp
    <meta charset="UTF-8">
    @php
        // Use the device width on mobile so the CSS media query can adapt the layout.
    @endphp
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        // Insert the child page title. Product Manager is the fallback when no title section is
        // supplied.
    @endphp
    <title>@yield('title', 'Product Manager')</title>

    @php
        // Load Inter once for all three pages through this shared layout. Google Fonts supplies
        // weights 400 (normal), 500 (medium), 600 (semibold), and 700 (bold). display=swap
        // shows a fallback while the font downloads. This needs internet; the CSS system-font
        // fallback works when offline.
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    @php
        // asset() generates the URL for public/css/style.css. Do not include public/ in the
        // URL. This project uses plain CSS directly; resources/css/app.css is not linked here.
    @endphp
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @php
        // Shared navigation stays the same on every child page. route() uses a named route to
        // build the list URL.
    @endphp
    <header class="navbar">
       <a href="{{ route('product.index') }}" class="brand">
    @php
        // Inline SVG draws a package outline, with no React dependency. viewBox is its drawing
        // coordinate system. fill="none" leaves it hollow; currentColor inherits the text
        // color; stroke-width controls line thickness. Each path d contains drawing
        // instructions. aria-hidden hides this decorative icon from screen readers because the
        // adjacent Product Manager text already names the link.
    @endphp
    <svg class="icon" xmlns="http://www.w3.org/2000/svg"
         width="24" height="24" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round"
         aria-hidden="true">
        <path d="M12 3 3 8v8l9 5 9-5V8Z"/>
        <path d="m3 8 9 5 9-5"/>
        <path d="M12 13v8"/>
        <path d="m7.5 5.5 9 5"/>
    </svg>

    Product Manager
</a>
        @php
            // Group the lab label and theme button as in the approved reference. The button
            // changes appearance only; it never changes product records.
        @endphp
        <div class="navbar-tools">
            <span>Laboratory 3</span>
            @auth
                @php
                    // Logout changes the session, so use Breeze's POST route and CSRF token.
                    // The shared product layout makes this available on list/add/edit pages.
                @endphp
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="button button-secondary logout-button">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 3H4v18h5M10 12h11m-4-4 4 4-4 4"/>
                        </svg>
                        Log out
                    </button>
                </form>
            @endauth
            <button type="button" id="theme-toggle" class="theme-toggle"
                    aria-label="Switch to light theme" aria-pressed="false">
                @php
                    // Sun = switch to light. CSS shows the moon when light mode is active.
                @endphp
                <svg class="icon sun-icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5 19 19M5 19l1.5-1.5M17.5 6.5 19 5"/>
                </svg>
                <svg class="icon moon-icon" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/>
                </svg>
            </button>
        </div>
    </header>

    @php
        // The main content container gets its glass appearance from public/css/style.css.
    @endphp
    <main class="glass-panel">
        @php
            // Place the child view content section here. This keeps the HTML shell from being
            // copied into each page.
        @endphp
        @yield('content')
    </main>
    @php
        // defer waits until HTML is parsed before the script finds the theme button. This is
        // plain JavaScript, not React, and requires no npm build command.
    @endphp
    <script src="{{ asset('js/theme.js') }}" defer></script>
    @php
        // Dismiss product success feedback after five seconds on the returned list page.
        // Keep this separate from theme switching so each script has one clear purpose.
    @endphp
    <script src="{{ asset('js/product-messages.js') }}" defer></script>
</body>
</html>
