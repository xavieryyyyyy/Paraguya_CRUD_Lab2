@php
    $currentImage = ($product ?? null)?->imageUrl();
@endphp

<div class="form-group product-upload">
    <label for="image">Product image <span>(optional)</span></label>
    <div class="product-image-preview">
        <img id="image-preview" alt="Product image preview"
             @if ($currentImage) src="{{ $currentImage }}" @else hidden @endif>
        <span id="image-placeholder" @if ($currentImage) hidden @endif>No image selected</span>
    </div>
    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
           aria-describedby="image-help" @error('image') aria-invalid="true" @enderror>
    <p id="image-help">
        JPEG, PNG, or WebP, up to 2 MB.
        @if ($currentImage)
            <br>Leave blank to keep the current image.
        @endif
    </p>
</div>

@push('scripts')
    <script src="{{ asset('js/product-image-preview.js') }}" defer></script>
@endpush
