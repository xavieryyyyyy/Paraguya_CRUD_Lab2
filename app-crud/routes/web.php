<?php

// ROUTES: Match an HTTP method and URL to a controller method. web.php routes use Laravel's web
// middleware, including sessions and CSRF protection.
use Illuminate\Support\Facades\Route;
// Import the controller instead of placing database operations in this file.
use App\Http\Controllers\ProductController;

// Opening the home URL redirects to the list. This is an anonymous PHP function.
Route::get('/', function () {
    return redirect()->route('product.index');
});

// GET displays the list. The name product.index is a label used by route() helpers.
// ProductController::class identifies the class; index identifies the public method.
Route::get('/product', [ProductController::class, 'index'])
    ->name('product.index');

// GET opens the blank form. Visiting a link should not create a database record.
Route::get('/product/create', [ProductController::class, 'create'])
    ->name('product.create');

// POST saves a new record. Same URL as the list, but a different HTTP method.
Route::post('/product', [ProductController::class, 'store'])
    ->name('product.store');

// {product} is a URL placeholder, for example /product/1/edit. Its name matches Product
// $product in the controller for route model binding.
Route::get('/product/{product}/edit', [ProductController::class, 'edit'])
    ->name('product.edit');

// PUT updates the chosen record. Blade uses POST plus @method('PUT') because native HTML forms
// support GET and POST, not PUT directly.
Route::put('/product/{product}', [ProductController::class, 'update'])
    ->name('product.update');

// DELETE removes the chosen record. The view uses @method('DELETE'). Explicit routes are used
// here to make each CRUD operation easy to explain.
Route::delete('/product/{product}', [ProductController::class, 'destroy'])
    ->name('product.destroy');