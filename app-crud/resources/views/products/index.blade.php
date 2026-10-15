@php
    // ProductController::index passes $products here. Render rows, empty state, edit links, and
    // CSRF-protected DELETE forms. The success banner is removed later by product-messages.js.
@endphp
@php
    // READ VIEW: The controller passes $products to this page. Extend the shared
    // resources/views/layouts/app.blade.php shell; sections below fill its yield slots.
@endphp
@extends('layouts.product')

@php
    // Set the browser tab title; this is different from the visible h1 heading.
@endphp
@section('title', 'Products | Product Manager')

@php
    // Start the content that the shared layout displays inside main.
@endphp
@section('content')
    @php
        // CSS arranges the heading and Add Product link, wrapping them on narrow screens.
    @endphp
    <div class="page-header">
        <div>
            <h1>Products</h1>
            <p>Manage your product records.</p>
        </div>

        @php
            // This GET link opens the form; it does not save. The plus SVG is decorative. All
            // SVG icons use the same hollow stroke style and inherit the surrounding text
            // color.
        @endphp
        <a href="{{ route('product.create') }}" class="button button-primary">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round"
         stroke-linejoin="round" aria-hidden="true">
        <path d="M12 5v14"/>
        <path d="M5 12h14"/>
    </svg>

    Add Product
        </a>
    </div>

    @php
        // Only show this message when the controller flashed success to the session. It is
        // temporary feedback after a redirect, not a product database column.
    @endphp
    @if (session('success'))
        @php
            // role="status" makes success feedback available to assistive tools without
            // interrupting like an alert.
        @endphp
        <div class="success-message" role="status">
    <svg class="icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round"
         stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/>
        <path d="m8 12 3 3 5-6"/>
    </svg>

    <span>{{ session('success') }}</span>
</div>
    @endif

    @php
        // The wrapper provides horizontal scrolling on small screens while the surrounding page
        // fits the screen.
    @endphp
    <div class="table-container">
        <table>
            @php
                // thead contains column labels; tbody below contains the actual database
                // values.
            @endphp
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // Loop over the Collection. On each iteration $product is one Product
                    // object. If the Collection is empty, Laravel displays the @empty block
                    // instead.
                @endphp
                @forelse ($products as $product)
                    <tr>
                        @php
                            // The -> operator reads a model attribute. Double braces escape
                            // HTML so entered text is displayed as text instead of executed as
                            // markup.
                        @endphp
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->qty }}</td>
                        @php
                            // Format price for display with two decimals, e.g. 25 becomes
                            // 25.00. This formats output; controller validation and the
                            // migration handle storage rules.
                        @endphp
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->description }}</td>
                        <td>
                            <div class="actions">
                                @php
                                    //                                     // Passing the model lets route() place its route key,
                                    // normally id, into the URL. Clicking the link opens
                                    // edit(); the pencil does not perform the update.
                                @endphp
                                <a href="{{ route('product.edit', $product) }}"
                                   class="edit-link">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round"
         stroke-linejoin="round" aria-hidden="true">
        <path d="m16 3 5 5"/>
        <path d="m3 21 1-6L16 3a3.5 3.5 0 0 1 5 5L9 20Z"/>
    </svg>

    Edit
                                </a>

                                @php
                                    //                                     // Deleting changes data, so use a form instead of a GET
                                    // link. HTML submits POST; @method below supplies a hidden
                                    // _method value for Laravel.
                                @endphp
                                <form method="POST"
                                      action="{{ route('product.destroy', $product) }}">
                                    @php
                                        // Add a hidden session token. Laravel checks it to help
                                        // prevent cross-site request forgery. This is form
                                        // protection, not login or permission checking.
                                    @endphp
                                    @csrf
                                    @php
                                        //                                         // Laravel treats this POST form as DELETE so it matches
                                        // product.destroy.
                                    @endphp
                                    @method('DELETE')

                                    @php
                                        //                                         // confirm() is browser JavaScript: OK returns true and
                                        // submits; Cancel returns false and stops this
                                        // submission. It does not itself delete a record.
                                    @endphp
                                    <button type="submit" class="delete-button"
                                            onclick="return confirm('Delete this product?')">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round"
         stroke-linejoin="round" aria-hidden="true">
        <path d="M3 6h18"/>
        <path d="M9 6V4h6v2"/>
        <path d="m5 6 1 14h12l1-14"/>
        <path d="M10 10v6"/>
        <path d="M14 10v6"/>
    </svg>

    Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @php
                    // colspan="6" spans all six columns for a single empty-state message.
                @endphp
                @empty
                    <tr>
                        <td colspan="6">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @php
            // count() counts loaded products. The ternary expression condition ? yes : no
            // selects product for exactly one row and products for every other count.
        @endphp
        <p class="table-footer">
            {{ $products->count() }}
            {{ $products->count() === 1 ? 'product' : 'products' }}
        </p>
    </div>
@php
    // Close the content section that the layout renders.
@endphp
@endsection