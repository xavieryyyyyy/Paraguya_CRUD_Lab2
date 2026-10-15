# Understanding Product Manager from request to response

This is a guide to the code in this project, including the language features it
uses. Examples marked as exercises are illustrations, not code already added to
the application. Follow the chapters in order on your first pass.

If terms such as middleware, POST, session, or model are unfamiliar, start with
[Terms and concepts](TERMS_AND_CONCEPTS.md). It explains what they mean, why the
application uses them, and the code/file where each one appears.

## 1. The separate programs involved

Your browser displays HTML, uses CSS for appearance, and executes JavaScript for
interactions. It does not execute PHP. PHP runs on the server, where Laravel
decides which code handles a request and how data is loaded or saved.

XAMPP supplies PHP and a MySQL-compatible MariaDB database. Composer installs PHP
packages. Node.js/npm run frontend tools such as Vite; they are not the database
server. VS Code edits files. Git saves source history. phpMyAdmin is a web interface
for examining the database. Each program has a different job.

| Technology | Job in this project | Example |
|---|---|---|
| PHP | Server-side programming | ProductController validates and saves input |
| Laravel | Web framework and services | Routing, sessions, Eloquent, validation |
| Blade | Server-side HTML templates | Loop through products and display errors |
| HTML | Page structure and form semantics | A labelled input and submit button |
| CSS | Appearance and responsive layout | Glass panels and form spacing |
| JavaScript | Browser interaction | Show/Hide password and theme switch |
| SQL database | Persistent tables and records | Products remain after browser refresh |
| Breeze | Generated authentication scaffold | Login, registration, recovery, profile |
| Tailwind/Alpine | Generated Breeze UI tools | Dashboard utilities and account menus |
| Vite | Frontend asset build | Compiles resources CSS/JS into public/build |

The customized authentication/product pages use Inter. Dashboard/Profile retain
generated Breeze styling. Selecting Inter in plain CSS does not automatically
rewrite the font configuration of every generated page.

### A URL is a request address

`http://127.0.0.1:8000/product` contains a protocol (`http`), host (`127.0.0.1`,
this computer), port (`8000`), and path (`/product`). The HTTP method is also part
of the request. GET and POST can use the same path and trigger different code.

The application uses 8000 for `artisan serve`, 3307 for the XAMPP database, and
8080 as the recommended alternate Apache port for phpMyAdmin. A port identifies
the service being contacted. The database port is not a page URL.

## 2. The full request journey

Opening a PHP application does not execute every file from top to bottom.
Laravel loads its startup files, matches one route, runs middleware, and calls
the method selected for that request. Other controller methods wait for other
requests. JavaScript timers/events run later in the browser.

```text
Browser sends method + URL + form data + cookies
    -> public/index.php
    -> vendor/autoload.php loads needed PHP classes
    -> bootstrap/app.php builds Laravel and loads route definitions
    -> web middleware: cookies, session, CSRF, shared validation errors
    -> route middleware: guest or auth, as configured
    -> matching controller / request class
    -> model, password broker, or other service when needed
    -> view HTML or redirect response
    -> browser displays HTML, loads CSS, runs JavaScript
```

The exact middleware stack is managed by Laravel. The source notes describe the
steps relevant to our requests rather than claiming every internal framework
method appears in this simplified diagram.

`public/index.php` calls `Request::capture()` to construct a request object and
`$app->handleRequest(...)` to process it. `bootstrap/app.php` identifies `web.php`
as the browser-route file. Composer's autoloader finds classes such as Product
when code references them; loading a class does not itself insert a row.

### Example: a guest opens Products

The route exists, but its `auth` middleware runs before `ProductController::index`.
The visitor has no authenticated session, so Laravel returns a redirect to
named `login`. The browser follows that redirect with a new GET request. It gets
the Login view instead of receiving the product records.

### Example: a signed-in user opens Products

The session identifies a user, middleware allows the request, and `index()` calls
`Product::all()`. Eloquent reads rows. The controller passes `$products` to
`products.index`, which extends `layouts.product`. Blade renders HTML; the browser
then applies `public/css/style.css` and loads the custom public scripts.

### A redirect creates a second request

A redirect says where the browser should go next. It does not render that next
page in the same controller return statement. After saving a product, the POST
response redirects to the GET list. This helps prevent ordinary refresh from
resubmitting the original POST.

## 3. PHP syntax that appears in the project

### Variables, values, and statements

```php
$name = 'Apple';      // string: text
$quantity = 12;       // integer: whole number
$enabled = true;      // boolean: true or false
$description = null;  // no value
$price = '25.00';     // string representation of a decimal value
```

`$` marks a PHP variable. `=` assigns a value. A semicolon ends a statement.
Single/double quotes enclose strings; double-quoted strings can interpolate
variables. `null` is different from an empty string, zero, and false.

```php
$data = ['name' => 'Apple', 'qty' => 12];
echo $data['name']; // Apple
```

This is an associative array: names are keys, and values are stored beside them.
`=>` separates a key from its value. Square brackets construct/access arrays.
A collection returned by Eloquent is an object with collection methods, not just
the plain array above.

### Functions and methods

A function is a named unit of work. A method is a function defined on a class.
`view()` and `route()` are helper functions. `index()` is a controller method;
`update()` is also an inherited Eloquent method used on a Product object.
The same method name in two classes can mean different things.

```php
function doubleQuantity($quantity) {
    return $quantity * 2;
}
$answer = doubleQuantity(3); // 6
```

`$quantity` in the definition is a parameter. `3` in the call is an argument.
`return` sends the result back and ends that function call. It does not necessarily
print anything. A controller returns a response object Laravel can send.

### Classes, objects, namespaces, and imports

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['name', 'qty', 'price', 'description'];
}
```

A class describes data and behavior. An object is an instance of a class, such
as one loaded Product record. A namespace organizes class names; it is not a
database name. `use` imports a class name for convenient reference.

`extends Model` inherits Eloquent behavior. `public` members are accessible to
calling code. `protected` members are accessible within the class/its subclasses.
`private` would restrict access to the declaring class. A protected `$fillable`
is read by Eloquent internally; the browser does not submit a new fillable list.

```php
Product::all();                 // class-level Eloquent call
$product->name;                 // object attribute
$product->update(['qty' => 15]); // method on this loaded object
ProductController::class;       // the fully qualified class name
```

`::` refers to a class-level member; `->` accesses an object. Eloquent implements
some convenient calls through its internal forwarding/magic behavior. The `new`
keyword constructs objects, for example `new Registered($user)`.

### Type declarations and named arguments

```php
public function store(LoginRequest $request): RedirectResponse
```

`LoginRequest` declares the expected parameter type. Laravel resolves that request
object for the controller. `: RedirectResponse` declares the returned type.
`: void` means no return value is supplied. `RedirectResponse|View` permits either
type. `array<string, ...>` in PHPDoc is documentation for tooling, not runtime PHP
syntax. A type declaration can help catch mistakes but does not replace validation.

```php
route('product.index', absolute: false)
```

`absolute: false` is a named argument: it names the parameter instead of relying
only on its position. The route helper returns a relative path here rather than
a full URL. `__DIR__` is the current file directory; `dirname(__DIR__)` moves up
one level. They avoid relying on the terminal's current folder.

### Conditions, comparison, and boolean logic

```php
if (auth()->check()) {
    return redirect()->route('product.index');
}
return redirect()->route('login');
```

Only the selected branch returns. `==` compares values with PHP's conversions;
`===` compares value and type. `!` means NOT, `&&` means AND, and `||` means OR.
`$this` refers to the current object, as in `$this->ensureIsNotRateLimited()`.

```php
$word = $count === 1 ? 'product' : 'products';
```

The ternary operator selects the expression after `?` if the condition is true,
otherwise the expression after `:`. `??` supplies a fallback for a missing/null
value. In PHP, `.` concatenates strings; `+` is arithmetic addition.

### Closures, callbacks, and chaining

```php
Route::middleware('auth')->group(function () {
    // Routes in this callback receive auth middleware.
});
```

`function () { ... }` is an anonymous function, also called a closure. The route
service calls it to register the grouped routes. Another closure runs after a
password token is verified:

```php
function (User $user) use ($request) {
    // use() makes the outer variable available inside this closure.
}
```

This closure's `use` is variable capture, unlike the class import at the top of
a PHP file. PHP arrow functions such as `fn () => ...` are shorter callbacks with
automatic by-value capture. JavaScript arrows have their own rules.

```php
return redirect()->route('login')->with('status', 'Message');
```

Each `->` calls a method on the object returned by the previous expression.
This is method chaining. Read it left to right: create a redirect, set its route,
then flash feedback to the session.

### Exceptions, interfaces, traits, and documentation comments

An exception interrupts normal execution. `throw ValidationException...` stops
login and lets Laravel produce the validation redirect. JavaScript `try/catch`
in the theme script catches blocked browser storage; it is a separate runtime.

An interface declares a contract a class implements. `MustVerifyEmail` is an
interface Laravel checks before enforcing email verification. Our User does
not implement it. A trait is reusable class behavior included with `use`, such
as `HasFactory` and `Notifiable` inside User.

`//` explains PHP/JavaScript code. `/* ... */` is a block comment, needed in CSS.
PHPDoc `/** ... */` may contain `@return`, `@var`, or `@throws` information for
tools. JSON does not support either comment form. Comments explain the program;
the program still runs when those comments are removed.

## 4. MVC and the responsibilities of each layer

The Product model is a PHP representation of database records. The Product
controller coordinates requests. Views display values and collect input. Routes
connect addresses/methods to the controller. Migrations define table structure.

Keep these questions beside the code:

| Question | File to read |
|---|---|
| Which URL runs this method? | routes/web.php or routes/auth.php |
| What happens to submitted input? | Controller or FormRequest |
| Which table holds this record? | Model convention and migration |
| Where is the HTML produced? | Matching Blade view and layout |
| Why does this element look this way? | CSS class selector |
| What happens on click in the browser? | JavaScript listener or form submission |

`Product::all()` does not enforce ownership. It retrieves all products. Auth
middleware ensures a visitor has logged in, but currently every logged-in user
can operate on shared product records. Adding owner/admin rules would require
additional relationships/policies and is outside the implemented lab scope.

## 5. Product CRUD, with one concrete record

### Read the product list

GET `/product` passes auth and calls `ProductController::index()`. Eloquent reads
the products table and returns a collection. `compact('products')` creates
`['products' => $products]`; the view receives the variable under that key.
`@forelse` renders each Product, or an empty row when none exist.

### Create Apple

GET `/product/create` calls `create()`, returning the form. The form's `action`
is the URL generated by `route('product.store')`. POST includes name, qty, price,
description, and CSRF token. `store()` validates before calling `Product::create`.
If validation fails, execution never reaches the insert.

```php
$data = $request->validate([
    'qty' => 'required|integer|min:0',
]);
```

Input quantity `12` passes. `-1` fails min, `1.5` fails integer, and an absent
quantity fails required. HTML attributes also check these values, but the server
must enforce its rules because a caller can modify browser HTML.

The real controller validates all four fields. `$fillable` permits their mass
assignment. Eloquent inserts a row, the database assigns its ID, and timestamps
are managed. The response redirects to the list with a flash success message.
JavaScript removes that banner after five seconds; it does not undo the insert.

### Update Apple quantity from 12 to 15

The Edit URL includes the record ID. `{product}` matches `Product $product`, so
route model binding loads the record. A missing ID normally returns 404. The
edit view displays `old('qty', $product->qty)`: previous submitted input wins
after an error; otherwise use the stored quantity.

The form submits POST with hidden `_method=PUT`. Laravel matches the update
route, validates, and calls `$product->update($data)`. The record ID is preserved;
`updated_at` changes. Opening `edit()` alone does not save anything.

### Delete a disposable product

The form targets `product.destroy`, contains CSRF, and uses `_method=DELETE`.
Browser `confirm()` returns true for OK and false for Cancel. That return value
determines whether this submission proceeds. The controller permanently removes
the selected row through `$product->delete()` and redirects.

Confirmation helps prevent accidental clicks. It is not authorization. The
server still requires auth, but it has no per-product ownership policy.

## 6. Database structure and persistent values

| Table | Purpose | Main connection |
|---|---|---|
| products | Product records | Product model and ProductController |
| users | Accounts and password hashes | User, registration, login |
| password_reset_tokens | Reset token hashes and creation time | Password broker |
| sessions | Session payload and activity | Session web guard |
| cache / cache_locks | Database-backed cache and locks | Configured cache services |
| jobs / job_batches / failed_jobs | Optional queued work | Queue services |
| migrations | Which schema migrations ran | Artisan migrate |

`products` contains id, name, qty, price, optional description, created_at, and
updated_at. `decimal(8,2)` allows eight total digits, two after the decimal:
up to 999999.99 in the positive range. It stores a fixed decimal rather than an
approximate floating-point value. Display formatting and validation are separate.

`users.email` has a database unique constraint as well as registration validation.
`password` contains a hash. `remember_token` supports remembered authentication.
`email_verified_at` can hold a timestamp, but a column alone does not enforce
verification. The sessions `user_id` is nullable for guest sessions and indexed;
this migration does not add a foreign-key constraint to that field.

`up()` creates schema and `down()` reverses it. Laravel records successful
migrations, so another `migrate` says Nothing to migrate. Editing a migration
already run does not automatically alter the database. A schema change should
normally be a new migration. Avoid `migrate:fresh` on your real database because
it drops tables and their data.

SQL conceptual examples:

```sql
SELECT * FROM products;
UPDATE products SET qty = 15 WHERE id = 6;
```

These explain what reading/updating means. Your controller uses Eloquent rather
than manually interpolating form input into SQL. Eloquent/query-builder value
bindings reduce SQL injection risk for these calls. Raw SQL expressions and
unvalidated column names would need their own care.

Database files/exports and the real `.env` are excluded from Git. Migrations in
Git can recreate table structure, not your current MySQL records. Back up the
database separately before changes.

## 7. Registration, step by step

GET `/register` is a guest route. `RegisteredUserController::create()` returns
`auth.register` inside `x-guest-layout`. POST `/register` calls `store()`.

The rules require name, lowercase valid email, unique email, and confirmed
password. `Rules\Password::defaults()` uses the configured default rule; without
customization its minimum is eight characters. Name/email limits are 255.
`confirmed` compares password against `password_confirmation`.

```php
'password' => ['required', 'confirmed', Rules\Password::defaults()]
```

The rule list is an array rather than a pipe-separated string because it also
contains a Password rule object. `unique:'.User::class` concatenates the unique
rule prefix with the model class name. The model identifies the users table.

After validation, `User::create` saves name, email, and `Hash::make(password)`.
A password hash is a one-way representation checked by a hashing service. It
is not encryption to be decrypted later. The User `hashed` cast recognizes a
valid already-hashed value, so this code does not intentionally double-hash it.

`event(new Registered($user))` lets listeners react to registration. `Auth::login`
signs in that new account. This controller still redirects to Dashboard. Your
normal login controller defaults to Products. The guide documents the actual
two flows rather than assuming they share a redirect.

## 8. Login, remembered authentication, and logout

### Login validation and credential checking

The form posts email/password/remember to `/login`. Laravel resolves LoginRequest
and checks its rules before calling the controller's store method. Required
email/password rules ensure suitable input; they do not prove the password is
correct. `$request->authenticate()` performs that credential check.

```php
Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))
```

`only` selects credential keys. `boolean` interprets the checkbox. The web guard
uses the configured Eloquent User provider to find the account and verify its
password hash. A failed attempt increments the limiter and throws an email
validation error. Five failures for the normalized email/IP combination can
cause a wait; success clears that counter. This is distinct from reset throttling.

After success, the controller regenerates the session ID, reducing session
fixation risk. `redirect()->intended(...)` returns to a protected page attempted
before login, otherwise `/product`. Merely rendering Login does not authenticate.

### Remember me

Checking it asks Laravel to issue remembered authentication using its cookie/token
mechanism. It does not store the readable password, automatically check every
future form, or add administrator rights. `old('remember')` only keeps the checkbox
choice when a form fails. Removing/clearing cookies affects remembered login;
logging out ends the current authentication flow.

### Logout

The product layout has a POST logout form with CSRF. The controller calls:

```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
return redirect()->route('login');
```

Read the sequence: log out the guard, discard session data, rotate the CSRF
token, redirect to Login. A GET link alone should not perform logout. If you
then request Products, auth middleware asks for login again. It does not delete
your user account or product records.

## 9. Password recovery and SMTP

Forgot Password requests an email. Reset Password changes the password using a
valid token. Profile password updates require the current password. Password
confirmation records recent proof without changing it. These are separate flows.

### Request the email

POST `/forgot-password` validates an email and calls
`Password::sendResetLink($request->only('email'))`. The password broker finds the
User, checks its throttle, creates a reset token, stores its hash, and notifies
the user. The email link contains the token needed by the reset form.

`config/auth.php` has expire 60 (minutes) and throttle 60 (seconds). A quick
second request can show Please wait before retrying. This protects against
repeated mail requests; it is not a broken form. Generating a new token replaces
the previous stored request for that email.

The controller compares a status to `Password::RESET_LINK_SENT`. Success flashes
a translated status; failure returns old email with an error. `__($status)` is
Laravel's translation helper, not the JavaScript underscore convention.

### Send through the configured mailer

`config/mail.php` reads `MAIL_MAILER`, `MAIL_SCHEME`, host, port, username,
password, and sender settings from `.env`. This app's SMTP config uses
`MAIL_SCHEME`; adding MAIL_ENCRYPTION alone would not change this configured key.

| Setting | Mailtrap sandbox example | Gmail example |
|---|---|---|
| MAIL_MAILER | smtp | smtp |
| MAIL_SCHEME | smtp | smtp |
| MAIL_HOST | sandbox.smtp.mailtrap.io | smtp.gmail.com |
| MAIL_PORT | 2525 | 587 |
| MAIL_USERNAME | Sandbox credential | Full sender Gmail address |
| MAIL_PASSWORD | Sandbox credential | Google App Password |

These are configuration examples without secrets. SMTP on these ports can upgrade
through STARTTLS; keep TLS certificate verification enabled. Gmail's App Password
is separate from the Laravel user's login password and from the normal Google
account password. Mailtrap captures messages in its sandbox; Gmail sends to the
registered recipient's actual inbox. One chosen SMTP configuration is active
at a time in the current application.

### Submit a new password

GET `/reset-password/{token}` passes Request to the view. The hidden token field
uses `$request->route('token')`; the email starts from the query string. The
guest middleware may redirect a signed-in browser away, which explains why you
needed to log out to open this form.

POST `/reset-password` validates token/email/confirmed password and calls
`Password::reset`. Only a valid, unexpired token reaches its callback. The callback
hashes the new password, rotates `remember_token`, saves the User, and fires
PasswordReset. Successful reset returns to Login with a status; it does not by
itself take the user through a normal login in this controller. The new password
then works through the standard login flow.

The callback uses `forceFill` with explicitly selected keys. This bypasses
mass-assignment restrictions deliberately; passing every browser key to forceFill
would be unsafe. A token is not a permanent login credential and should not
be included in public screenshots.

## 10. Authentication, authorization, and other protections

Authentication checks who the visitor is. Authorization checks whether that
visitor may perform a particular action. Our product routes require login, but
there are no admin roles or owner-only policies. User A can currently manage
the same products as User B. A signed-in session alone does not make either admin.

| Mechanism | Checks/protects | Does not provide |
|---|---|---|
| auth middleware | Signed-in session | Owner/admin permission rules |
| guest middleware | Signed-out access to auth forms | Product authorization |
| validation | Submitted values | Identity or SQL permissions |
| fillable | Allowed mass-assignment keys | Value validation |
| CSRF token | Forged session-based form requests | Login or user roles |
| password hash | Password storage/checking | Hiding visible browser inputs |
| Show/Hide button | Browser display of typed password | Encryption or server security |
| escaped Blade output | HTML interpretation of displayed text | Database validation |
| bound database values | These queries resist SQL injection | Safety of arbitrary raw SQL |
| .env exclusion | Keep local secrets out of Git | Protection if files are leaked elsewhere |

Email-verification handlers exist, but User does not implement MustVerifyEmail.
The product group applies auth only. The generated Dashboard verified middleware
does not establish that verification is enforced for this current model.

Profile account deletion is a generated feature requiring the current password.
Products have no owner relationship, so the generic deletion template's wording
about deleting all resources should not be interpreted as a product cascade.
The guide maps this boundary rather than claiming unimplemented behavior.

## 11. Blade layouts, directives, and components

### Two layout systems in this project

Product views use inheritance:

```blade
@extends('layouts.product')
@section('content')
    <h1>Products</h1>
@endsection
```

The layout uses `@yield('content')`. Dot notation maps a name to folders:
`products.index` is `resources/views/products/index.blade.php`.

Auth views use components:

```blade
<x-guest-layout>
    <h1>Welcome back</h1>
</x-guest-layout>
```

GuestLayout::render selects `layouts.guest`; the inner markup becomes `$slot`.
Dashboard/Profile use x-app-layout with `$slot` and sometimes a named `$header`.
`@extends('layouts.app')` does not supply the component slot, which caused the
earlier Undefined variable $slot error. Both approaches work when the calling
page and layout agree on how content is supplied.

### Output, loops, errors, and form protection

`{{ $product->name }}` displays escaped data. Entered `<script>` text is treated
as text rather than inserted as executable HTML through this syntax. `{!! ... !!}`
is unescaped output and is not used for our user-entered product values.

`@if`, `@else`, and `@endif` choose blocks. `@foreach` iterates; `@forelse` adds
`@empty` behavior. `@error('email')` exposes `$message` for that field. `$errors`
can contain multiple bags; profile password/account forms use named bags to
keep unrelated errors apart.

```blade
<form method="POST" action="{{ route('product.update', $product) }}">
    @csrf
    @method('PUT')
</form>
```

HTML forms submit GET or POST. `_method` lets Laravel route a POST as PUT/DELETE.
`@csrf` inserts a hidden `_token` tied to the session. Middleware verifies it.
`old()` restores safe previous input after errors. Passwords should never be
restored into HTML. `@auth` renders content for authenticated users.

`@include` inserts a partial, such as profile form markup. Anonymous components
under views/components use `@props` to declare input values and `$attributes`
to forward/merge attributes. Named slots such as trigger/content supply dropdown
markup. `@js` produces safely encoded JavaScript data. Blade `@php` blocks contain
our hidden `//` comments; bare `//` in ordinary HTML would appear as page text.

### HTML form attributes

| Attribute | Purpose |
|---|---|
| name | Submitted field key the server receives |
| id | Label/DOM target; not by itself a submitted key |
| for | Connect a label to the matching input ID |
| type | Email, password, checkbox, number, or hidden behavior |
| value | Initial displayed/submitted value |
| required/min/max/step | Browser constraints, rechecked server-side |
| autocomplete | Tell password managers/browser the field purpose |
| autofocus | Initial focus, not a validation rule |
| class | CSS/DOM selector hook, not a database field |
| aria-hidden | Hide decorative icons from assistive tools |
| aria-label/pressed/controls | Name/state/relationship for interactive controls |
| defer | Run the linked script after HTML parsing |

Use `type="button"` for Show/Hide so it does not submit the form. A normal
submit button intentionally sends it. Hidden fields are invisible in the layout
but still inspectable in the browser; never treat them as trustworthy secrets.

## 12. CSS, glass surfaces, and the font

A selector identifies elements, braces group declarations, and each declaration
has a property/value separated by `:` and ended by `;`.

```css
.auth-card {
    max-width: 460px;
    padding: 36px;
}
```

The class selector begins with `.`. A descendant selector such as
`.light-theme .auth-card` selects a card inside a light-theme ancestor. `:hover`
and `:focus-visible` are states; `::after` is a pseudo-element. Our navbar divider
is drawn separately so the theme button's width does not shift when toggled.

The cascade considers origin/layer, specificity, and order. A more specific
selector can override a later weak selector. This is why the password wrapper
padding rule repeats the input exclusions: it must beat the earlier generic
input padding. A long file of overrides is harder to read; use its section
comments and CODE_MAP to find the relevant rule before adding another duplicate.

`box-sizing:border-box` includes padding/borders inside declared widths. Padding
is internal space; margin is outside. `max-width` limits desktop width while
`width:100%` fills the available wrapper. Flexbox aligns children and can wrap
them when space is limited. `gap` separates children without manual margins.

The table has `min-width` for readable columns, while its wrapper has
`overflow-x:auto`. Scroll inside that wrapper on small screens. The media query
at 600px reduces padding/type. `scrollbar-gutter:stable` reserves scrollbar space
to reduce theme-switch layout shifts. The form is not supposed to be squeezed
into six tiny table columns.

Glass combines transparent `rgba` values, background gradients, backdrop blur,
bright inner edges, rounded corners, and offset shadows. Alpha 0 is transparent;
1 is opaque. Stacking several pale backgrounds can make the result look solid.
`inset` draws a shadow inside the element. `saturate()` changes the backdrop color
intensity, not text saturation. This approximates glass on the web without Apple's
native refraction renderer.

The primary action is pearl white in dark mode and charcoal in light mode.
Delete remains coral. Error/success colors also carry readable messages so color
is not the only signal. Reduced-motion rules remove the button transition.

Inter loads through Google Fonts in custom layouts; fallbacks apply if the download
fails. Input/button `font:inherit` copies the parent font. Generated Dashboard/Profile
still use their Vite/Tailwind font setup. A font-family rule selects a font; the
link in the layout actually makes the hosted font available.

## 13. JavaScript and SVG, explained through the three scripts

### JavaScript syntax and browser objects

`const` prevents reassignment of a variable binding; it does not freeze every
property on an object. `let` permits reassignment. `document` is the DOM API for
this page. `querySelector` returns the first matching element or null;
`querySelectorAll` returns a list-like NodeList. `forEach` runs a callback for each
match. JavaScript methods use `.`, not PHP's `->`.

`===` is strict equality. The ternary operator chooses one expression. String
concatenation uses `+` in our JavaScript. `try/catch` handles exceptions. DOM
updates do not automatically send a request to the server or update MySQL.

### theme.js

The script finds `theme-toggle`, reads a saved value, and calls setTheme. The
function toggles light-theme on body and updates accessible button state. Its
click listener chooses the other mode and saves localStorage. A blocked storage
exception leaves the current-page toggle usable. LocalStorage is browser storage;
it is separate from Laravel sessions and should not hold our passwords.

### password-visibility.js

The selector finds password inputs inside auth-input wrappers. For each input,
the script creates a non-submit button, reserves right padding, and appends it.
Clicking switches `input.type` between password and text, changes Show/Hide text,
and updates accessible state. The field name/value/autocomplete remain intact.
Each confirmation field has its own toggle. The typed password is still sent
through the same form and is hashed on the server.

### product-messages.js

The script selects success-message. If no banner exists, it does nothing.
Otherwise `setTimeout` schedules a callback for 5000 milliseconds (five seconds).
`remove()` takes the banner out of the DOM. Validation errors use different classes
and stay visible. Removing a DOM element does not delete a database record.

### SVG outline icons

`svg` creates a vector drawing. `viewBox="0 0 24 24"` sets its coordinate space.
`path d` contains move/line/curve commands; rect/circle describe shapes.
`fill="none"` creates outlines, `stroke="currentColor"` inherits the text color,
and stroke-width controls line thickness. Icons are Lucide-like drawings, not
React components. `aria-hidden` avoids announcing decorative shapes twice.

### Alpine in generated pages

`x-data` creates local UI state; `x-show` controls visibility; `x-on:click` or
`@click` listens to events. `$dispatch` sends a custom event, `$watch` observes
state, and `$el` refers to the component element. Navigation dropdowns and the
profile deletion modal use Alpine. The custom theme/password controls use plain
JavaScript instead. These tools do not authorize database changes.

## 14. Configuration, builds, and dependencies

`.env` contains local configuration, including credentials. `config/*.php` maps
environment values into Laravel settings. `config('mail.default')` reads the
resolved setting; `env()` belongs mainly in config definitions. Cached configuration
can retain old values until `artisan config:clear` is run. Do not print the entire
resolved configuration when sharing errors.

Composer reads composer.json and composer.lock, installs PHP dependencies into
vendor, and creates autoload metadata. A lockfile records exact resolved versions.
`require-dev` includes development/test packages such as Breeze/PHPUnit. Breeze
generates source code; installing its package alone does not create all views/routes.

npm reads package.json/package-lock.json and installs Node packages. Tailwind
scans templates; PostCSS/Autoprefixer help produce CSS; Vite builds entry assets.
The Laravel vite directive reads the manifest or development-server information.
`npm run build` creates a reusable build; `npm run dev` keeps a live build server.
Custom files in public/css and public/js are served directly and do not need to
be compiled by Vite after every change.

The default composer setup script includes migrations; do not run broad setup
commands blindly against an existing database. Use the documented commands
and inspect what the scripts do. Framework dependencies should be reinstalled
through tools rather than edited to hide SMTP errors.

## 15. Tests, evidence, and real limitations

PHPUnit runs assertions through artisan test. A feature test boots Laravel and
simulates HTTP requests; a unit test can examine a smaller piece. RefreshDatabase
resets the test database between relevant tests. phpunit.xml selects in-memory
SQLite, array sessions/cache/mail, so these tests do not delete your MySQL products
or send actual Gmail mail. Always verify this isolation before running unfamiliar
database-reset tests.

UserFactory supplies fake test users. `$this->get`, `post`, and `actingAs` simulate
requests/authentication. `assertRedirect`, `assertSessionHasErrors`, `assertGuest`,
and `assertAuthenticated` verify outcomes. Notification::fake captures reset
notifications so tests can inspect token flow without emailing real users.

Generated test expectations must follow customized routes. Our normal login now
defaults to Products, logout to Login, and home redirects guests. Registration
still defaults to Dashboard. Updating expected destinations does not weaken
credential checks; separate assertions still check guest/authenticated state.

Tests of email-verification handlers do not enable enforcement. Tests of generated
controllers do not certify the final mobile layout. A successful SMTP test does
not mean every provider account has the same settings. Screenshots show one
state; a recorded user action or automated assertion establishes behavior.

## 16. Run, inspect, and explain the application

```powershell
cd "C:\Users\Yestin Paraguya\Downloads\Paraguya_CRUD_Lab2\app-crud"
& "C:\xampp\php\php.exe" artisan serve
```

Start XAMPP MySQL and keep the server terminal open. Open
http://127.0.0.1:8000/. Guests reach Login; signed-in users reach Products.
VS Code may be closed if the server runs in a separate PowerShell window.

```powershell
& "C:\xampp\php\php.exe" artisan route:list -v
& "C:\xampp\php\php.exe" artisan migrate:status
& "C:\xampp\php\php.exe" artisan view:clear
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan test
```

Read each command's purpose: list routes/middleware, inspect completed migrations,
clear compiled view cache, clear resolved config cache, run tests. None of the
first four intentionally deletes product rows. Ctrl+F5 refreshes browser assets.

Open phpMyAdmin through the configured Apache port, select the lab database, then
Browse/Structure on products or users. Passwords should appear as hashes, not
their original text. Never include hashes, reset URLs, cookies, or SMTP credentials
in a public demonstration screenshot unnecessarily.

To explain code, start with an action: "This form submits POST /product, the auth
group requires a session, store validates input, Product inserts the row, and the
redirect returns the list with feedback." Then point to the files that prove
each clause. That is more useful than memorizing all methods in file order.

## 17. References for further study

- [PHP classes and objects](https://www.php.net/manual/en/language.oop5.basic.php)
- [Laravel request lifecycle](https://laravel.com/framework/docs/12.x/lifecycle)
- [Laravel routing](https://laravel.com/framework/docs/12.x/routing)
- [Laravel Blade](https://laravel.com/framework/docs/12.x/blade)
- [Laravel validation](https://laravel.com/framework/docs/12.x/validation)
- [Laravel authentication](https://laravel.com/framework/docs/12.x/authentication)
- [Laravel password reset](https://laravel.com/framework/docs/12.x/passwords)
- [Laravel email verification](https://laravel.com/framework/docs/12.x/verification)
- [Laravel Eloquent](https://laravel.com/framework/docs/12.x/eloquent)
- [MDN JavaScript guide](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide)

Examples throughout this guide are tied to the checked application source. The
course's Laboratory 3 PDF supplies the activity questions and deliverables.
