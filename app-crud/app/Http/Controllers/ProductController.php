<?php

// CRUD coordinator called by the six protected product routes in routes/web.php. Reads and
// validates input, calls the Product model, then returns a products view or a redirect with
// success feedback.


// CONTROLLER: Receive requests, ask the model to work with records, and choose a view or
// redirect. Keep HTML in Blade views and table structure in migrations.
namespace App\Http\Controllers;

// Import the Product model to call its Eloquent database methods.
use App\Models\Product;
// Request is Laravel's object containing the incoming form data and request details.
use Illuminate\Http\Request;

// Inherit the project's base controller. public methods can be called by routes. $ introduces a
// PHP variable; -> accesses an object; :: calls a class method.
class ProductController extends Controller
{
    // READ: GET /product calls this method to display the product list.
    public function index()
    {
        // all() reads every product and returns a Collection of Product objects. It does not
        // insert data. An empty table produces an empty Collection.
        $products = Product::all();

        // Dot notation resolves to resources/views/products/index.blade.php.
        // compact('products') makes ['products' => $products], passing the records to Blade as
        // $products. Returning the view sends rendered HTML to the browser.
        return view('products.index', compact('products'));
    }

    // CREATE FORM: GET /product/create only shows the form; it does not save yet.
    public function create()
    {
        // No existing record is needed for a blank Add Product form.
        return view('products.create');
    }

    // CREATE RECORD: POST /product submits the Add Product form. Laravel supplies the Request
    // object automatically; the controller does not need to create it manually.
    public function store(Request $request)
    {
        // Server-side validation cannot be bypassed simply by changing browser HTML. If
        // validation fails in this web form, Laravel redirects back with errors and previous
        // input. The save below is not reached. If it succeeds, $data contains the validated
        // fields, not every arbitrary submitted field.
        $data = $request->validate([
            // A name is required, must be text, and may contain at most 255 characters.
            'name' => 'required|string|max:255',
            // Quantity must be a whole number, including zero, with no negative value.
            'qty' => 'required|integer|min:0',
            // Price must be numeric and nonnegative with zero to two decimal places. decimal(8,
            // 2) in the migration allows six digits before the decimal, so 999999.99 is the
            // largest allowed value. 25, 25.0, and 25.00 are valid. The | character separates
            // rules; 1.999 fails the decimal-place rule.
            'price' => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            // Description is optional. If supplied as a non-null value, it must be text.
            'description' => 'nullable|string',
        ]);

        // Eloquent inserts one row using the validated data and model fillable fields. The
        // database creates id; Eloquent supplies the timestamps.
        Product::create($data);

        // Send the browser back to the named list route after saving. with() below stores a
        // temporary session message for the next request. Redirecting after POST lets a normal
        // refresh reload the list instead of resubmitting the create form.
        return redirect()->route('product.index')
            ->with('success', 'Product saved successfully.');
    }

    // UPDATE FORM: GET /product/{product}/edit opens a saved record. Route model binding
    // connects {product} to this Product $product parameter. Normally Laravel finds it by id;
    // an unknown id produces a 404 response.
    public function edit(Product $product)
    {
        // Pass this one record as $product so the form can show its saved values. Singular
        // product is one object; plural products in index is a Collection.
        return view('products.edit', compact('product'));
    }

    // UPDATE RECORD: PUT /product/{product} submits the edit form. Request holds the new
    // values; route model binding supplies the existing record. Use the same validation as
    // store so edited data follows the same rules.
    public function update(Request $request, Product $product)
    {
        // Reject invalid changes before touching the existing database row. See the
        // rule-by-rule explanations in store() above; the rules are identical.
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'description' => 'nullable|string',
        ]);

        // Change this existing row; do not create another row. Its id stays the same,
        // created_at stays the same, and Eloquent updates updated_at.
        $product->update($data);

        return redirect()->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    // DELETE: DELETE /product/{product} removes the selected bound record. The browser
    // confirmation lives in the view; this method performs deletion.
    public function destroy(Product $product)
    {
        // This is a permanent database delete because Product does not use SoftDeletes.
        // Confirmation reduces accidental clicks; it is not an authorization check.
        $product->delete();

        return redirect()->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }
}