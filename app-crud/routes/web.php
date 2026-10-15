<?php

// Browser URL map. Home redirects based on login state. Product/Profile groups require auth.
// The last require loads routes/auth.php once. Product routes use destroy(), matching
// ProductController.

// A route matches an HTTP method and URL path to its handler.
// GET displays a resource/form; POST processes submitted data; PUT updates a product;
// DELETE removes one. Middleware runs around the handler: auth requires a signed-in
// session before the product controller is called. Definitions and examples are in
// docs/TERMS_AND_CONCEPTS.md at the workspace root.



use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Send guests to Login and authenticated users to the product list.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('product.index');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group middleware runs before the controller. For a guest HTML request Laravel redirects to
// named login; it does not grant admin rights to a signed-in user.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Require login before allowing access to any Product CRUD route.
// Guests are redirected to Breeze's login page.
Route::middleware('auth')->group(function () {
    Route::get('/product', [ProductController::class, 'index'])
        ->name('product.index');

    Route::get('/product/create', [ProductController::class, 'create'])
        ->name('product.create');

    Route::post('/product', [ProductController::class, 'store'])
        ->name('product.store');

    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])
        ->name('product.edit');

    Route::put('/product/{product}', [ProductController::class, 'update'])
        ->name('product.update');

    Route::delete('/product/{product}', [ProductController::class, 'destroy'])
        ->name('product.destroy');
});

require __DIR__.'/auth.php';
