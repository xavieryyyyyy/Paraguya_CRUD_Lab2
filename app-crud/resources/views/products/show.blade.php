@extends('layouts.product')

@section('title', $product->name . ' | Product Manager')

@section('content')
    <div class="page-header product-detail-header">
        <h1>{{ $product->name }}</h1>
        <a href="{{ route('product.index') }}" class="button button-secondary">Back to Products</a>
    </div>

    @php
        $imageUrl = $product->imageUrl();
    @endphp
    <div @class(['product-detail-layout', 'product-detail-layout-with-image' => $imageUrl])>
        @if ($imageUrl)
            <div class="product-detail-media">
                <img class="product-detail-image" src="{{ $imageUrl }}" alt="{{ $product->name }}">
            </div>
        @endif

        <div class="product-detail-content">
            <dl class="product-details">
                <div>
                    <dt>Stock</dt>
                    <dd class="product-detail-value">{{ $product->qty }}</dd>
                </div>
                <div>
                    <dt>Price</dt>
                    <dd class="product-detail-value">{{ number_format($product->price, 2) }}</dd>
                </div>
                <div class="product-detail-description">
                    <dt>Description</dt>
                    <dd>{{ filled($product->description) ? $product->description : 'No description.' }}</dd>
                </div>
            </dl>

            @can('update', $product)
                <a href="{{ route('product.edit', $product) }}" class="button button-primary">Edit Product</a>
            @endcan
        </div>
    </div>
@endsection
