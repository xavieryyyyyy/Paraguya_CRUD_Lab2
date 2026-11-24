# Lab 4 testing report

This upgrade builds on the existing Laravel Breeze Product Manager. It adds product images, admin permissions, and catalog search and pagination, and shares the same validation rules between product creation and updates.

## Set up the demonstration

New registrations and existing accounts start with the `user` role. A trusted person with terminal access can promote one registered account. From `app-crud`, run:

```powershell
& "C:\xampp\php\php.exe" artisan migrate
& "C:\xampp\php\php.exe" artisan storage:link
& "C:\xampp\php\php.exe" artisan users:make-admin "your-admin-account@example.com"
& "C:\xampp\php\php.exe" artisan serve
```

Use the email of an account you control and confirm the promotion when prompted. Use a separate regular account to demonstrate the two roles. If the development server is already running, use its existing address. Open `http://127.0.0.1:8000/product` when using the default local address.

1. Sign in as a regular user. Open the catalog and a product detail page. Show that management buttons are hidden and that opening `/product/create` returns 403.
2. Sign in as the admin account. Add a product with an image and open its detail page by clicking its name. Edit it without selecting a file to demonstrate that the current image stays. Edit it again to replace the image.
3. Search for an existing name and an unmatched name. With more than ten matching products, use Next and show that the URL keeps the search term.
4. Delete a product created for the demonstration. Confirm that its image is gone from `storage/app/public/products`.
5. For server-side input checks, show the automated test results. Browser validation may prevent an empty or negative form from reaching Laravel.

## Test results

Run `& "C:\xampp\php\php.exe" artisan test` from `app-crud`. The full suite passed **39 tests and 187 assertions**. Feature tests use an in-memory SQLite database and use a fake disk for image checks. Both migrations and the storage junction were verified separately on the local app. The course's face-to-face demonstration is a separate check.

| Required case | Actual result | Status |
| --- | --- | --- |
| Submit an empty product form | Laravel returned errors for name, stock, and price. No product was saved. | Pass |
| Enter a negative price | Laravel rejected the price and preserved the submitted input in the session. | Pass |
| Upload an unsupported file | A text file presented as a PNG was rejected. The existing product image remained intact. | Pass |
| Upload a valid image | A PNG was stored, its path was saved, and the catalog and details both rendered its URL. | Pass |
| Log in as a regular user | Login succeeded. Catalog and details were available, while management controls and actions were restricted. | Pass |
| Log in as an admin | Login succeeded. The admin could open both forms, create, view, update, and delete a product. | Pass |
| Search for an existing product | Only matching names appeared. Page 2 retained the query, and a missing match showed an empty message. | Pass |
| Delete a product with an image | Both the database record and the associated file were removed. | Pass |
| Access a protected route directly as a regular user | Create, store, edit, update, and delete returned 403. The product's stock and existence were preserved. | Pass |

Additional checks verified that a registration cannot set its own role, a file above the limit is rejected, a simulated storage failure preserves the previous file and product data, and the admin command can promote an existing account.

## How the changes improve security

Validation rejects bad input before Laravel writes to the database or moves an image. Laravel checks the file content and size and generates the stored filename. The database stores the relative path. Images on the required public disk are visible to anyone with their URLs, so this disk is for public catalog images. Configure the web server to serve that directory as static files.

The `auth` middleware identifies the signed-in user. Route authorization and controller checks call the same policy to enforce admin permissions. Blade uses the policy to display management controls. Registration only assigns the approved account fields, and the database supplies the regular role.

## Guide questions and code to show

These lines match the current Lab 4 working tree. In VS Code, open the file with **Ctrl+P**, jump to the line with **Ctrl+G**, and use the method name as a search anchor if the lines move.

### 1. Role-based authorization and security

The handout uses `/products/5` as an example. Our route uses the singular path `DELETE /product/5`. The `auth` group requires a signed-in account. The route's `can` middleware checks whether that account can delete the bound product. The controller also calls the same policy before deleting. The policy permits admins, so a signed-in regular user gets **403 Forbidden** and the record stays. If only the Blade button were hidden, a direct request could still reach the server action.

- [routes/web.php line 43](../app-crud/routes/web.php#L43) begins the authenticated product route group. Line 67 declares DELETE, and line 68 calls `->can('delete', 'product')`.
- [ProductController.php line 104](../app-crud/app/Http/Controllers/ProductController.php#L104) calls `Gate::authorize('delete', $product)`.
- [ProductPolicy.php line 22](../app-crud/app/Policies/ProductPolicy.php#L22) defines `delete()`. Line 24 checks `$user->role === 'admin'`.
- [index.blade.php line 170](../app-crud/resources/views/products/index.blade.php#L170) uses `@can` to decide whether to show the Delete form.

### 2. Server-side validation and data integrity

`required|numeric|min:0` rejects a price of `-500`. `required|integer|min:0` rejects stock of `2.5`. Stock is called `qty` in this project. `required|string|max:255` rejects an overlong name. The actual price rule also limits the maximum and decimal places to fit the database column. HTML constraints provide immediate feedback in the browser. Requests can arrive without using that form, so both server actions validate before saving.

- [ProductController.php line 46](../app-crud/app/Http/Controllers/ProductController.php#L46) calls validation when creating. Line 70 calls the same validation when updating.
- [ProductController.php line 125](../app-crud/app/Http/Controllers/ProductController.php#L125) checks name, line 127 checks price, and line 128 checks stock.
- [create.blade.php line 31](../app-crud/resources/views/products/create.blade.php#L31) displays errors. Line 76 uses `old('name')` to restore input. [edit.blade.php line 82](../app-crud/resources/views/products/edit.blade.php#L82) falls back to the saved name.

### 3. Secure file uploads and storage

The last part of a filename is supplied by the uploader. Renaming a file to `.jpg` cannot establish that its contents are JPEG. The server uses `image` and `mimes:jpg,jpeg,png,webp` to check the upload and `max:2048` to enforce the 2 MB limit. Laravel generates the saved filename. The database saves its relative path. Only admins can upload or replace product images. The lab uses public storage for catalog photos, so anyone with an image URL can retrieve it. Configure the web server to serve this directory as static files. If images need access restrictions, put them on a private disk and serve them through an authorized controller.

- [create.blade.php line 54](../app-crud/resources/views/products/create.blade.php#L54) declares `multipart/form-data`. [image-field.blade.php line 12](../app-crud/resources/views/products/image-field.blade.php#L12) declares the shared file input. Its `accept` attribute guides the picker.
- [ProductController.php line 129](../app-crud/app/Http/Controllers/ProductController.php#L129) validates the uploaded file. Line 144 saves it to the public disk. Line 50 saves the path when creating the product.
- [config/filesystems.php line 22](../app-crud/config/filesystems.php#L22) identifies the public disk directory. Line 45 defines its public link.
- [Product.php line 33](../app-crud/app/Models/Product.php#L33) checks whether the image exists before building its display URL.

### 4. File replacement and database consistency

Deleting the old image first leaves a broken reference if the replacement fails. We store the replacement first, update the product inside a database transaction, and delete the old file after the transaction returns successfully. If the upload fails, the database update never starts. If the database update fails, the controller attempts to remove the newly uploaded file. If no replacement is selected, the existing image stays. Cleanup failures are logged so a leftover file can be investigated.

- [ProductController.php line 71](../app-crud/app/Http/Controllers/ProductController.php#L71) stores the replacement.
- [ProductController.php line 76](../app-crud/app/Http/Controllers/ProductController.php#L76) begins the transaction. Line 77 locks the row and line 84 saves the update.
- [ProductController.php line 89](../app-crud/app/Http/Controllers/ProductController.php#L89) attempts to remove the new file on a database failure. Line 95 removes the old file after success. Line 155 checks for a failed file deletion.
- [LabFourTest.php line 135](../app-crud/tests/Feature/LabFourTest.php#L135) simulates a storage failure and checks that the original file and product data survive.

### 5. Middleware versus policies

Use both. `auth` middleware establishes who is signed in, and `can` middleware calls the policy for the specific action. The policy decides whether a given user can update that particular product. If the assignment later allows users to edit their own products, add a `user_id` ownership field, set it from the authenticated user when creating a product, and update `ProductPolicy::update()` to allow an admin or the owner. The existing Lab 4 version allows admin updates. Ownership is a proposed extension.

- [routes/web.php line 43](../app-crud/routes/web.php#L43) begins the authenticated group. Lines 60 and 64 enforce the update policy on the edit page and its submission.
- [ProductPolicy.php line 17](../app-crud/app/Policies/ProductPolicy.php#L17) is where the ownership rule would go. [ProductController.php line 69](../app-crud/app/Http/Controllers/ProductController.php#L69) checks it before updating.
- [Role migration line 13](../app-crud/database/migrations/2026_11_24_000001_add_role_to_users_table.php#L13) defaults accounts to `user`. [routes/console.php line 18](../app-crud/routes/console.php#L18) defines the admin promotion command.

### 6. Search, pagination, and performance

`Product::all()` would transfer and load all 100,000 rows into PHP before filtering. Our query applies the name condition in the database, orders by ID for consistent pages, and requests ten product records for the current page. Laravel's paginator also gets a count for page navigation. `appends()` carries the search term into Next and Previous links. A substring search can still scan many rows, so pagination mainly limits how many product rows are returned to PHP.

- [ProductController.php line 22](../app-crud/app/Http/Controllers/ProductController.php#L22) starts the query. Line 23 adds `where('name', 'like', ...)`, line 24 orders, line 25 paginates, and line 26 preserves the term.
- [index.blade.php line 49](../app-crud/resources/views/products/index.blade.php#L49) submits the search as a GET query. Line 236 renders the Next link.

### 7. Integrated security testing and troubleshooting

Investigate one problem at a time. Use `artisan route:list --path=product -v` to inspect middleware, then compare the product row and stored files before and after each request.

| Symptom | Where to inspect and what to correct | Test that proves the fix |
| --- | --- | --- |
| Regular users can delete | Check the DELETE route's `can` rule, the controller's `Gate::authorize`, and the policy's role check. | A regular user receives 403 and the record remains. See [LabFourTest.php line 49](../app-crud/tests/Feature/LabFourTest.php#L49). |
| Invalid submissions save incomplete data | Check that both write actions call `validatedProduct()` before writing and that they save validated fields. | An invalid create makes no row, and an invalid update leaves the existing product and image intact. See [LabFourTest.php line 68](../app-crud/tests/Feature/LabFourTest.php#L68) and line 104. |
| Old images remain | Check the path captured before the update, confirm cleanup occurs after the database write, and inspect storage warnings if deletion fails. | On success, the old file disappears and the new one exists. On upload failure, the original survives. Product deletion also removes its file. See [LabFourTest.php line 80](../app-crud/tests/Feature/LabFourTest.php#L80) and line 135. |

## Files added and changed since Lab 3

Compared against the `lab3-complete` Git tag at `b69edb4`, including new files in the working tree. Paths in the following tables are relative to the repository root.

### Added (9 files)

| File | Why it was added |
| --- | --- |
| `app-crud/app/Policies/ProductPolicy.php` | Defines the create, update, and delete rules. |
| `app-crud/database/migrations/2026_11_24_000001_add_role_to_users_table.php` | Adds the role column with the regular-user default. |
| `app-crud/database/migrations/2026_11_24_000002_add_image_path_to_products_table.php` | Adds the optional image path column. |
| `app-crud/resources/views/products/show.blade.php` | Displays a product's details and full image. |
| `app-crud/resources/views/products/image-field.blade.php` | Shares the upload picker and preview between the add and edit forms. |
| `app-crud/public/js/product-image-preview.js` | Updates the local preview when the admin selects a file. |
| `app-crud/resources/views/errors/419.blade.php` | Gives users recovery links when a form expires. |
| `app-crud/tests/Feature/LabFourTest.php` | Tests authorization, validation, image handling, and pagination. |
| `docs/LAB4_REPORT.md` | Records the demo, test results, answers, and code locations. |

### Changed (16 files)

| File | What changed |
| --- | --- |
| `app-crud/routes/web.php` | Added the detail route and authorization middleware on management routes. |
| `app-crud/routes/console.php` | Added the `users:make-admin` command. |
| `app-crud/app/Http/Controllers/ProductController.php` | Added search, pagination, shared rules, authorization checks, and image storage and cleanup. |
| `app-crud/app/Http/Controllers/Auth/RegisteredUserController.php` | Sends new accounts to Login with their email prefilled. |
| `app-crud/app/Models/Product.php` | Accepts `image_path` and builds the public image URL. |
| `app-crud/resources/views/products/index.blade.php` | Added search, thumbnails, detail links, pagination, and policy checks for management controls. |
| `app-crud/resources/views/products/create.blade.php` | Added multipart upload and the shared image field to the add form. |
| `app-crud/resources/views/products/edit.blade.php` | Added multipart upload and the shared image field to the edit form. |
| `app-crud/resources/views/layouts/product.blade.php` | Updated Lab 4 branding and added a place for page-specific scripts. |
| `app-crud/resources/views/layouts/guest.blade.php` | Updated the guest pages to Lab 4 branding. |
| `app-crud/public/css/style.css` | Styled the catalog, forms, and product details in both themes. |
| `app-crud/public/css/auth.css` | Kept recovery button colors readable in both themes. |
| `app-crud/tests/Feature/Auth/RegistrationTest.php` | Checks that registration opens Login and signing in opens Products. |
| `app-crud/tests/Feature/LabThreeFlowTest.php` | Covers guest access to the new detail route and uses an admin for the protected creation-form redirect test. |
| `README.md` | Updated project setup and linked the Lab 4 report. |
| `docs/START_HERE.md` | Updated the registration flow description. |
