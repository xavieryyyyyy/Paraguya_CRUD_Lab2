@php
    // Product form that posts with a hidden PUT method to product.update. Route binding
    // supplies the selected record; old(field, savedValue) chooses submitted or stored data.
@endphp
@php
    // UPDATE FORM: Display the selected record and submit changes. Use the shared layout and
    // the same form CSS as the other pages.
@endphp
@extends('layouts.product')

@php
    // Fill the shared layout browser-title slot.
@endphp
@section('title', 'Edit Product | Product Manager')

@php
    // Everything in this section becomes the main page content.
@endphp
@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Product</h1>
            <p>Update the product details below.</p>
        </div>
    </div>

    @php
        // Laravel makes validation errors available as $errors. any() checks whether at least
        // one exists. On failure the controller redirects back with these messages.
    @endphp
    @if ($errors->any())
        <div class="error-message" role="alert">
            <p>Please check the following:</p>

            <ul>
                @php
                    // all() returns the error messages; foreach prints each safely using double
                    // braces. role="alert" on the container identifies important error
                    // feedback.
                @endphp
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // The form action names the receiving route; name attributes below become Request keys.
        // class attributes only select CSS and are not submitted fields.
    @endphp
    <form method="POST"
          action="{{ route('product.update', $product) }}"
          class="product-form">
        @php
            // Laravel inserts a hidden CSRF token and verifies it before accepting this web
            // form.
        @endphp
        @csrf
        @php
            // HTML forms submit GET or POST. This adds hidden _method=PUT so Laravel matches
            // the PUT update route; $product in action identifies which row to update.
        @endphp
        @method('PUT')

        @php
            // A label for="name" matches input id="name", linking the label to its field.
            // name="name" is the key the controller validates; id is primarily for labels and
            // DOM targeting.
        @endphp
        <div class="form-group">
            <label for="name">Name</label>
            @php
                // old(field, savedValue) prefers previous submitted input after validation
                // failure; otherwise it displays the saved value. maxlength and required also
                // help the browser check input.
            @endphp
            <input type="text" id="name" name="name"
                   maxlength="255"
                   value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            @php
                // min=0 rejects negatives in the browser; step=1 requests whole-number
                // quantities. The controller must check again because browser restrictions can
                // be bypassed.
            @endphp
            <label for="qty">Quantity</label>
            <input type="number" id="qty" name="qty"
                   min="0" step="1"
                   value="{{ old('qty', $product->qty) }}" required>
        </div>

        <div class="form-group">
            @php
                // step=0.01 accepts increments of one cent. max matches decimal(8, 2). These
                // are browser checks; Laravel numeric/min/max/decimal rules enforce server
                // checks.
            @endphp
            <label for="price">Price</label>
            <input type="number" id="price" name="price"
                   min="0" max="999999.99" step="0.01"
                   value="{{ old('price', $product->price) }}" required>
        </div>

        <div class="form-group">
            @php
                // Description is optional. textarea text goes between its opening and closing
                // tags rather than in a value attribute. rows sets its initial visible height.
            @endphp
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description"
                      rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        @php
            // The submit button sends the form. Cancel is a GET link back to the list and does
            // not save changes. CSS wraps these controls on narrow screens.
        @endphp
        <div class="form-actions">
            <button type="submit" class="button button-primary">
                Update Product
            </button>

            <a href="{{ route('product.index') }}"
               class="button button-secondary">
                Cancel
            </a>
        </div>
    </form>
@php
    // Close the content section.
@endphp
@endsection