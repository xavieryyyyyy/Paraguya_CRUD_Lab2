# Syntax and API reference used by this application

Use this reference after reading a request walkthrough. API means the names and
contracts code calls, such as Laravel methods or browser DOM methods. The examples
illustrate expressions; they are not extra features added to the project.

For the meaning of a term such as middleware, request, model, or POST, read
[Terms and concepts](TERMS_AND_CONCEPTS.md) first. This reference explains the
written code expressions used to work with those concepts.

## PHP language expressions

| Expression | Meaning and example |
|---|---|
| `<?php` | Start PHP code in a PHP source file |
| `$name` | Variable; `$name = 'Apple';` assigns a string |
| `;` | End a statement; block braces group several statements |
| `[]` | Construct/index arrays; `['qty' => 12]` or `$data['qty']` |
| `=>` | Map a key to a value inside an array; also appears in arrow functions |
| `->` | Read a property or call a method on an object: `$product->name` |
| `::` | Class-level member/reference: `Product::all()` or `Product::class` |
| `.` | Concatenate strings: `'unique:'.User::class` |
| `=` | Assignment; different from comparison |
| `==` / `===` | Value comparison with conversions / strict value-and-type comparison |
| `!` | Negate a boolean result, such as failed `Auth::attempt` |
| `&&` / `||` | Both conditions / either condition |
| `? :` | Ternary choice: `$count === 1 ? 'product' : 'products'` |
| `??` | Missing/null fallback: `$name ?? 'Unknown'` |
| `if` / `return` | Choose a branch / end the call and return its result |
| `function` | Declare a function/method or anonymous closure |
| `use ($request)` | Capture an outer variable inside a closure |
| `namespace` / top-level `use` | Organize and import class names |
| `class` / `new` | Define a class / construct an object |
| `extends` | Inherit a parent class, such as Product extending Model |
| `implements` | Declare an interface contract, needed for enforced MustVerifyEmail |
| trait `use` | Include reusable behavior such as Notifiable inside a class |
| `public` / `protected` / `private` | External / class-and-subclass / declaring-class access |
| `$this` | Current instance inside a method |
| `: View` / `: void` / `A|B` | Return type / no return value / allowed union types |
| named argument | `absolute: false` selects an argument by parameter name |
| `throw` | Raise an exception and interrupt normal execution |
| `match` | Strict expression selection with a default arm; used for component sizing/alignment |
| `__DIR__` | Current source file directory, independent of terminal folder |
| `require` / `require_once` | Load and execute another PHP file / ensure it is loaded once |
| `//` / `/* */` / `/** */` | Line comment / block comment / tooling documentation comment |

`match` example from the component idea:

```php
$widthClass = match ($width) {
    '48' => 'w-48',
    default => $width,
};
```

The expression compares strictly, chooses one result, and has no switch-style
fallthrough. `default` handles inputs not listed. Component props should be
understood before changing their allowed values.

## Laravel helpers and request/response methods

| Call | Purpose in the application |
|---|---|
| `view(name, data)` | Render a Blade view with supplied variables |
| `compact('products')` | PHP helper making an array from named local variables |
| `route(name, parameters)` | Generate a named route URL, including product IDs |
| `url('/')` | Generate the application's root URL |
| `asset('css/style.css')` | Generate an asset URL under public/ |
| `redirect()` | Build a redirect response |
| `redirect()->route(...)` | Redirect to a named route |
| `redirect()->intended(fallback)` | Return to saved intended destination, otherwise fallback |
| `back()` | Redirect to the previous location |
| `->with('status', value)` | Flash feedback to the next request/session |
| `->withInput(...)` | Flash selected old form input for the next view |
| `->withErrors(...)` | Place validation/broker messages into error bags |
| `old(field, fallback)` | Read previous input; use fallback if absent |
| `session('status')` | Read a session value such as temporary feedback |
| `auth()->check()` | Check authenticated state for the current guard |
| `$request->user()` | Current authenticated User, not a submitted user ID |
| `$request->validate(rules)` | Check rules or throw; returns validated values on success |
| `validateWithBag(name, rules)` | Validate using a named error bag for a specific form |
| `$request->validated()` | Read accepted fields from a resolved FormRequest |
| `$request->only(keys)` | Select keys; by itself it does not validate their values |
| `$request->boolean('remember')` | Interpret a submitted checkbox as a boolean |
| `$request->route('token')` | Read a matched route parameter |
| `$request->ip()` | Request IP used as part of the login limiter key |
| `$request->session()` | Access session operations |
| `session()->regenerate()` | Rotate session ID after successful login |
| `session()->invalidate()` | Discard session data and regenerate its ID |
| `session()->regenerateToken()` | Rotate the CSRF token |
| `session()->put(key, value)` | Persist a session value such as confirmation time |
| `config(key)` | Read resolved Laravel configuration |
| `env(key, default)` | Read environment values, mainly in config definitions |
| `__()` | Translate a key/text into a display message |
| `event(new Event(...))` | Dispatch an event to registered listeners |
| `number_format(value, 2)` | PHP display formatting for two-decimal prices |
| `dirname(path)` | PHP path parent helper for startup file locations |
| `file_exists(path)` | Check whether a startup/maintenance file exists |
| `microtime(true)` | Float timestamp used to record Laravel startup time |
| `time()` | Current Unix timestamp for password-confirmation session state |

## Authentication and model methods

| Call/setting | Purpose and boundary |
|---|---|
| `Auth::attempt(credentials, remember)` | Check a saved account/password hash and authenticate if valid |
| `Auth::login($user)` | Sign in an already-selected User, used after registration |
| `Auth::guard('web')` | Select the browser session guard |
| `guard()->validate(credentials)` | Check credentials without creating a new login session |
| `logout()` | End authenticated state; session invalidation follows separately |
| `Hash::make(password)` | Produce a password hash for storage |
| `Password::sendResetLink(...)` | Broker creates/sends a reset request, not a new password |
| `Password::reset(data, callback)` | Verify reset credentials/token, then invoke the password-change callback |
| `Rules\Password::defaults()` | Configured Password rule object; default minimum is eight without customization |
| `Str::random(60)` | New random remember token when resetting a password |
| `Str::lower` / `transliterate` | Normalize login limiter key text |
| `RateLimiter::hit` / `clear` | Record a failed attempt / remove its counter after success |
| `tooManyAttempts` / `availableIn` | Check the limit / remaining wait seconds |
| `ValidationException::withMessages` | Raise field-specific errors for Laravel to handle |
| `Product::all()` | Read a collection of all product rows |
| `Model::create(data)` | Insert a model using allowed mass-assignment keys |
| `$model->fill(data)` | Assign allowed attributes in memory; saving is a separate call |
| `$model->update(data)` | Assign and save changes to that existing record |
| `$model->save()` | Persist the model's current attribute state |
| `$model->delete()` | Delete this record; no Product soft deletes are configured |
| `$model->forceFill(data)` | Assign despite fillable; use only explicit trusted keys |
| `isDirty('email')` | Detect an unsaved change from the originally loaded value |
| `$collection->count()` | Number of loaded values/records |
| `$fillable` | Keys allowed in mass assignment |
| `$hidden` | Attributes omitted when serializing; does not alter DB storage |
| `casts()` | Attribute conversions, including User dates/password hashes |
| `hasVerifiedEmail` / `markEmailAsVerified` | Inspect/set verification status; routes alone do not enforce it |
| `sendEmailVerificationNotification()` | Dispatch verification email, separate from password reset |

## Schema and validation terms

| Name | Meaning |
|---|---|
| `Schema::create` | Create table structure described by Blueprint |
| `$table->id()` | Auto-incrementing primary key |
| `string` / `text` / `integer` | Short text / longer text / whole-number column |
| `decimal(8, 2)` | Eight total digits, two decimal places |
| `nullable()` | SQL NULL allowed |
| `unique()` / `primary()` | Unique constraint / primary key |
| `index()` | Database index supporting lookups; not automatically an authorization rule |
| `timestamps()` | created_at and updated_at columns |
| `rememberToken()` | User remember_token column |
| `foreignId()` | ID-shaped column; a foreign key still needs a constraint definition |
| `dropIfExists()` | Drop an existing table during rollback |
| `required` / `nullable` | Value required / null allowed |
| `string` / `integer` / `numeric` rules | Validate value type/representation |
| `min` / `max` | Bounds interpreted with the other rules/type |
| `decimal:0,2` | Zero through two decimal places accepted |
| `email` / `lowercase` / `unique` | Email shape / lowercase / unused saved email |
| `confirmed` | Match the corresponding _confirmation field |
| `current_password` | Verify the authenticated user's current password |

## Blade and generated component expressions

| Expression | Purpose |
|---|---|
| `@extends` / `@section` / `@yield` | Inheritance layout, provided section, insertion point |
| `x-guest-layout` / `x-app-layout` | Component layouts with inner markup as slot |
| `$slot` / named `x-slot` | Default/named component content |
| `@include` | Insert a reusable view partial |
| `{{ ... }}` | Escaped output |
| `@php` | Embedded PHP; our comment-only blocks produce no visible text |
| `@if` / `@isset` / `@auth` | Condition / existence / authentication-dependent blocks |
| `@foreach` / `@forelse` / `@empty` | Loops with optional empty behavior |
| `@error` / `$message` | Selected field error and its text |
| `@csrf` / `@method` | Hidden session token / spoofed PUT-DELETE form method |
| `@checked` | Render checked attribute when its condition is true |
| `@props` | Anonymous component input/default declarations |
| `$attributes->merge` | Combine caller attributes with component defaults |
| `$attributes->has` | Check whether caller supplied an attribute |
| `@js` | Encode server data safely for a JavaScript context |
| `@vite` | Link Vite's development or compiled entry assets |
| `$errors->get/any/all` | Field messages / whether errors exist / all messages |
| named error bag | Separate profile deletion/password errors from other forms |

## JavaScript and browser methods

| Expression | Purpose and example |
|---|---|
| `const` / `let` | Stable variable binding / reassigned binding |
| `document.getElementById` | Find theme button by ID |
| `querySelector` / `querySelectorAll` | First match or null / matching NodeList |
| `forEach(function (...) {...})` | Process every matched password input |
| `parentElement` | Password input's wrapper element |
| `classList.add/toggle/contains` | Add/select/check CSS classes |
| `createElement('button')` | Build a DOM button without submitting a form |
| `setAttribute` | Set accessible labels, pressed state, or controlled input ID |
| `textContent` | Display a string as text, such as Show/Hide |
| `appendChild` | Add the Show/Hide button to the wrapper |
| `addEventListener('click', callback)` | Run code when that control is clicked |
| `input.type` | Switch browser presentation between password and text |
| `trim()` / `toLowerCase()` | Remove edge whitespace / normalize label case |
| `String(boolean)` | Convert accessible pressed value to true/false text |
| `localStorage.getItem/setItem` | Read/store theme preference in this browser |
| `try` / `catch` | Handle blocked browser storage without breaking the toggle |
| `setTimeout(callback, 5000)` | Schedule banner removal after five seconds |
| `remove()` | Remove the selected DOM banner, not a DB row |
| `confirm()` | Browser OK/Cancel prompt returning a boolean |
| Alpine `x-data/x-show/x-on` | UI state, visibility, and events in generated components |
| Alpine `$dispatch/$watch/$el` | Custom events, state observation, component element |

## CSS/build/test terms

| Expression | Purpose |
|---|---|
| `rgba(...)` | Color with alpha transparency |
| `linear-gradient/radial-gradient` | Directional or elliptical backdrop/color variation |
| `backdrop-filter` | Blur/saturate the surface behind the element |
| `box-shadow: inset ...` | Highlight/shadow inside the surface |
| `display:flex` / `gap` / `flex-wrap` | Child alignment, spacing, wrapping |
| `position:relative/absolute` | Wrapper context and positioned icon/control |
| `transform:translateY(-50%)` | Center an icon relative to its own height |
| `white-space:nowrap` | Keep a button label together |
| `:focus-visible` / `:hover` | Keyboard-focus/pointer states |
| `@media` | Conditional rules for size/motion preference |
| `font:inherit` | Controls inherit the surrounding font settings |
| `npm run build` | Vite builds the configured resource assets |
| `Notification::fake()` | Capture test notifications without sending real mail |
| `User::factory()->create()` | Generate a test User record |
| `RefreshDatabase` | Reset configured test DB between relevant tests |
| `actingAs($user)` | Authenticate a test request as a fixture user |
| `get/post/put/delete` test methods | Simulate requests with specified HTTP methods |
| `assertOk/assertRedirect` | Verify response success/destination |
| `assertAuthenticated/assertGuest` | Verify authentication state |
| `assertSessionHasErrors` | Verify rejected input produces the named errors |
| `assertDatabaseCount/Has` | Check number/content of test records |
| `assertCookie` | Verify remembered login issued the expected cookie |

Find the real calling file in CODE_MAP.md, then use the complete guide for the
surrounding request flow. A method name alone does not tell you whether it changes
the database, changes only an object in memory, or updates browser markup.
