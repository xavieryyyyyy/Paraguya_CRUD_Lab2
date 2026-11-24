<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->validate(['search' => 'nullable|string|max:255'])['search'] ?? '');

        // Filter in the database, then retrieve only the current page.
        $products = Product::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('id')
            ->paginate(10)
            ->appends($search === '' ? [] : ['search' => $search]);

        return view('products.index', compact('products', 'search'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('products.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);
        $data = $this->validatedProduct($request);
        $imagePath = $this->storeImage($request->file('image'));

        try {
            Product::create($data + ['image_path' => $imagePath]);
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);
            throw $exception;
        }

        return redirect()->route('product.index')
            ->with('success', 'Product saved successfully.');
    }

    public function edit(Product $product)
    {
        Gate::authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('update', $product);
        $data = $this->validatedProduct($request);
        $newPath = $this->storeImage($request->file('image'));

        try {
            // Read the latest image under a row lock so overlapping updates or
            // deletion cannot make us clean up the wrong version.
            $oldPath = DB::transaction(function () use ($product, $data, $newPath) {
                $current = Product::query()->lockForUpdate()->findOrFail($product->id);
                $oldPath = $current->image_path;

                if ($newPath !== null) {
                    $data['image_path'] = $newPath;
                }

                $current->update($data);

                return $oldPath;
            });
        } catch (Throwable $exception) {
            $this->deleteImage($newPath);
            throw $exception;
        }

        // The row now references the new image. An edit without a file keeps the old one.
        if ($newPath !== null) {
            $this->deleteImage($oldPath);
        }

        return redirect()->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $imagePath = DB::transaction(function () use ($product) {
            $current = Product::query()->lockForUpdate()->findOrFail($product->id);
            $current->delete();

            return $current->image_path;
        });

        // Remove the file only after the database delete succeeds.
        $this->deleteImage($imagePath);

        return redirect()->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function validatedProduct(Request $request): array
    {
        // These rules run for both create and update. Laravel returns errors
        // and old input before either action can change the database or disk.
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'qty' => 'required|integer|min:0|max:2147483647',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], ['qty.integer' => 'Stock must be a whole number.'], ['qty' => 'stock']);

        unset($data['image']);

        return $data;
    }

    private function storeImage(?UploadedFile $image): ?string
    {
        if ($image === null) {
            return null;
        }

        // Laravel generates a filename and guesses its extension from file content.
        $path = $image->store('products', 'public');

        if ($path === false) {
            throw ValidationException::withMessages(['image' => 'The image could not be saved. Please try again.']);
        }

        return $path;
    }

    private function deleteImage(?string $path): void
    {
        if ($path !== null && ! Storage::disk('public')->delete($path)) {
            Log::warning('Could not remove a product image.', ['path' => $path]);
        }
    }
}
