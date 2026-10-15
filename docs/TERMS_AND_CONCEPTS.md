# Terms and concepts before reading the code

Read the explanations of requests, methods, routes, and middleware first. The
later sections define the other terms you meet in this project. Each example
uses Product Manager or is clearly labelled as a learning example.

## 1. Browser, server, and request

The browser is the program you use to view the website. The server is the program
that receives the browser's requests and returns a response. They can run on
the same laptop and still have separate responsibilities. PHP runs in the server;
HTML, CSS, and browser JavaScript are used by the browser.

A request is a message asking the server to do something. It contains a method,
an address/path, headers, and sometimes submitted data. A response is the server's
reply, containing a status, headers, and often a body such as HTML.

When you open `/product`, the browser asks for the product list. When you press
Save Product, it submits the entered values. Those are different requests even
though the list and save route both use `/product`.

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Client | Program requesting a service | The browser is the web client |
| Browser | Program rendering pages and running browser JavaScript | Displays the table; Show/Hide runs here |
| Server | Program handling requests and providing responses | artisan serve runs the local PHP web server |
| Frontend | Interface and interactions the user sees | Labels, buttons, CSS, theme controls |
| Backend | Server-side processing and services | Controllers, validation, authentication, database queries |
| Request | Message sent to ask for a resource/action | POST with product name/quantity/price |
| Response | Reply to a request | HTML list or a redirect to Login |
| HTTP | The web's request/response protocol | Method and URL identify the intended request |
| Protocol | Rules for communication | HTTP handles pages; SMTP handles outgoing mail |
| HTTPS | HTTP protected by TLS in transit | Needed for secure public deployment; local lab uses HTTP |
| URL | Address of a resource | http://127.0.0.1:8000/product |
| Host | Machine/service address part of a URL | 127.0.0.1 is this computer |
| localhost / loopback | Addresses for the machine itself | Local browser contacts local server |
| Port | Number identifying a service endpoint | 8000 web server; 3307 database |
| Path | Part selecting a resource/action | /login, /product/create |
| Query string | Extra URL values after question mark | The reset link supplies an email query value |
| Header | Request/response metadata | Content-Type describes how to interpret the body |
| Body | Main message content | Form fields in a POST; HTML in a response |
| Payload | Data carried in a message/storage field | Session payload or submitted form values |
| Endpoint | Address and method handled by the application | POST /product reaches store() |
| Redirect | Response instructing the browser to visit another address | After save, visit GET /product |
| Status code | Number describing response outcome | 200 succeeds; 302 redirects; 404 is not found |

An endpoint isn't just a visible button. A caller can send a request without
clicking your page, so server rules still matter when the UI looks correct.

## 2. GET, POST, PUT, PATCH, and DELETE

An HTTP method, also called a verb, tells the server the purpose of a request.
GET retrieves a representation, POST submits data, PUT replaces a representation,
PATCH applies a partial change, and DELETE requests removal. HEAD returns
GET-like response headers without its body. OPTIONS reports communication options.
These meanings come from the HTTP method definitions; a program must implement
them sensibly. [HTTP methods reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods)

Here is how our actual routes use them:

| Method and path | Meaning here | Why this choice is used |
|---|---|---|
| GET /product | Display saved products | Viewing should not insert/delete records |
| GET /product/create | Display a blank form | Opening the form is not saving it |
| POST /product | Submit a new product | The request asks the server to create a record |
| GET /product/{product}/edit | Display the selected record's form | Reading values before an update |
| PUT /product/{product} | Submit the complete editable product fields | Our form sends the values for that record |
| DELETE /product/{product} | Remove the selected record | The request explicitly changes stored data |
| POST /login | Submit account credentials | Process a sign-in attempt, not merely show the form |
| POST /logout | End the session authentication | Logout changes state and needs CSRF protection |
| PATCH /profile | Submit profile information changes | The generated route uses partial-update semantics |

GET does not mean "run an SQL SELECT" in every application. It is an HTTP request
method. In our list route the controller happens to perform a database read.
POST does not mean "insert one row" every time either: login, logout, and reset
requests also use it. The route decides which controller processes it.

```php
Route::get('/product', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);
```

Read the first line as: a GET request to this path should call index. Read the
second as: a POST to that same path should call store. Method plus path chooses
the route. The uppercase methods describe HTTP; Laravel's lowercase get/post
are PHP method names registering those routes.

### HTML forms and method spoofing

Ordinary HTML forms support GET/POST submission. Our update form uses POST and
`@method('PUT')`, which generates a hidden `_method` field Laravel interprets.
"Spoofing" here means representing the intended method through that hidden field,
not bypassing authentication. Delete uses the same mechanism for DELETE.

```blade
<form method="POST" action="{{ route('product.update', $product) }}">
    @csrf
    @method('PUT')
</form>
```

Keep the named route, CSRF token, and method field connected. Changing one word
without understanding the receiving route can produce a Method Not Allowed error.

## 3. A route and middleware are different jobs

A route associates a method/path with code. Middleware surrounds request handling
with checks or supporting behavior. It can allow the request to continue, redirect
it, or reject it before the controller. Some middleware also processes the response.
For this application, auth checks login; web middleware supports sessions and
CSRF validation. [Laravel middleware reference](https://laravel.com/framework/docs/12.x/middleware)

The practical order for a protected request is:

```text
Request -> matched route and applicable middleware -> controller
               |
               +-> guest? auth redirects to Login
```

You can imagine a check at a doorway, but the real implementation runs on each
request. It is not a person checking once when the browser opens. Hiding a link
does not prevent someone requesting that endpoint directly.

```php
Route::middleware('auth')->group(function () {
    Route::get('/product', [ProductController::class, 'index']);
});
```

`middleware('auth')` selects a named check. `group(...)` gives it to the routes
registered inside the callback. The closure's braces group PHP statements;
the middleware is not the controller's own validation rules.

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Route | Map from request method/path to handling code | web.php connects /product to index |
| Named route | Reusable label for generating URLs | product.index is a label, not a file path |
| Route parameter | Variable segment of a path | {product} takes the selected product ID |
| Route model binding | Laravel loads the model matching the parameter | Product $product receives a saved row object |
| Middleware | Request/response processing around the handler | auth stops guest access before CRUD runs |
| Middleware group | A reusable collection of middleware | web provides sessions/cookies/CSRF behavior |
| Route group | Routes sharing selected attributes/checks | Six product routes share auth |
| Alias | Short name mapped to middleware | auth and guest select Laravel's checks |
| Guest | Visitor without an authenticated session | Can open Login but is redirected from Products |
| Authenticated user | User whose session identifies a signed-in account | May enter the current shared product routes |
| Intended destination | Protected address requested before login | Visiting Add Product as guest returns there after login |
| Fallback | Result used when the preferred value/destination is absent | Products is login's default destination |

### Middleware compared with validation

auth asks whether the session is signed in. Validation asks whether submitted
values satisfy rules. A guest should be stopped before attempting a write even
if every submitted value is valid. A signed-in user still gets validation errors
for an invalid price. These checks solve separate problems.

## 4. MVC, framework, and application structure

MVC means Model, View, Controller. A controller coordinates one action; a model
works with stored records; a view presents values and form controls. Laravel is
a framework providing structure and services so these parts can work together.

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Application | Program solving a user's task | Product Manager combines CRUD and accounts |
| Framework | Reusable structure/services for building applications | Laravel supplies routing, sessions, validation |
| Library/package | Reusable code installed as a dependency | Symfony Mailer is used by Laravel's mail services |
| Dependency | Code/tool required by other code | Composer installs Laravel's dependencies |
| MVC | Responsibilities split into model/view/controller | Product, products.index, ProductController |
| Model | Class representing/interacting with records | Product connects by convention to products |
| View | Template producing the displayed interface | login.blade.php produces a login form |
| Controller | Request-handling class coordinating actions | store validates, saves, and returns a redirect |
| Request class | Object describing the incoming request, optionally with rules | LoginRequest validates and authenticates login input |
| FormRequest | Laravel request class with validation hooks | ProfileUpdateRequest provides profile rules |
| Service | Object/functionality performing a focused application job | Password broker handles token verification |
| Facade | Laravel class-style access to a service | Auth::attempt uses the authentication service |
| Helper | Convenient function offered by PHP/Laravel | view(), route(), compact() |
| Service container | Laravel's mechanism for constructing/resolving dependencies | Supplies LoginRequest to the controller method |
| Dependency injection | Receive a needed object as an argument | Request $request is provided rather than manually built |
| Provider | Startup class registering/booting application services | AppServiceProvider is listed in bootstrap/providers.php |
| Bootstrap | Startup work preparing an application | bootstrap/app.php registers routes and configuration |
| Entry point | First application file processing that kind of input | public/index.php for HTTP; artisan for commands |
| Autoloader | Finds class files when referenced | Composer's vendor/autoload.php |
| Scaffold | Generated starting code meant to inspect/customize | Breeze's controllers, views, tests |
| Convention | Expected naming/structure that avoids extra configuration | Product maps to products unless configured otherwise |
| Configuration | Values choosing how services operate | Database connection, mail transport, session driver |
| Environment | Local/deployment settings outside source logic | .env provides host/port/private credentials |

Why separate layers? To change how a button looks, edit a view/style without
changing database queries. To adjust accepted prices, edit server validation
without drawing a new page. The file map tells you which responsibility belongs
to the file you are opening.

## 5. Database and CRUD language

A database stores organized data. A table is one record type, a row is one
record, and a column is one attribute. A model is PHP code that interacts with
this storage; it is not the table itself.

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| CRUD | Create, Read, Update, Delete operations | Four ways to work with products |
| Database | Persistent organized data store | paraguya_crud_lab2 on MariaDB |
| Table | Records with a shared structure | products or users |
| Row/record | One stored item | One Apple product |
| Column/field | Named attribute within that structure | name, qty, price |
| Schema | Definition of table/column structure | Products migration describes it |
| Migration | Versioned program for schema changes | up creates; down reverses |
| SQL | Language for database operations | SELECT reads rows |
| Query | Instruction asking the database for work/data | Product::all produces a read query |
| ORM | Maps database data to program objects | Eloquent represents rows as Product/User objects |
| Eloquent | Laravel's ORM implementation | create/update/delete methods used here |
| Primary key | Identifier uniquely distinguishing a row | products.id |
| Auto-increment | Database assigns the next numeric identifier | New product gets an ID automatically |
| Unique constraint | Database requires a value to be unique | users.email cannot duplicate another row |
| Index | Data structure helping database lookups | Sessions activity/email lookup indexes |
| Foreign key | Database-enforced relationship to another key | A possible products.user_id constraint is not implemented here |
| Relationship | Defined connection between records/models | Product ownership would need extra model/schema work |
| Data type | Permitted representation of a value/column | Integer qty; decimal price; text description |
| Decimal precision/scale | Total digits / digits after decimal point | decimal(8,2) allows six before, two after |
| NULL/nullable | Absence of a value / allowed absence | Optional description may be NULL |
| Timestamp | Stored date/time value | created_at and updated_at |
| Persistence | Data remains after the request/program state ends | A saved product survives a refresh |
| Collection | Object holding multiple values/models | products is the collection used by the list |
| Mass assignment | Set multiple model attributes from an array | Product::create($data) |
| Fillable | List of allowed mass-assignment keys | name/qty/price/description |
| Cast | Conversion for a model attribute | User date or hashed password assignment |
| Serialization | Convert an object into a transport/storage representation | User converted to array/JSON omits hidden fields |
| Seed | Insert optional initial/sample records | DatabaseSeeder, not normal user registration |
| Factory | Program generating record fixtures | UserFactory creates test users |
| Fixture | Known sample data used in a test | Disposable Test Apple record |
| Rollback | Reverse a recorded migration | Dropping the products table also removes its rows |
| Backup | Separate recoverable copy of data/source | SQL export before Breeze changes |

Why use validation if a column has a data type? A numeric column can still accept
values your application does not want. Quantity should not be negative here, so
the controller has min:0. A fixed decimal column may round input in ways you do
not intend, so the controller rejects three-decimal prices before saving.

## 6. Accounts, security, and email terms

Authentication asks which user/session is signed in. Authorization asks whether
that user may do a particular action. Our auth group implements a login boundary;
it does not implement administrators or product ownership.

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Registration | Create a saved account | Register inserts a User |
| Authentication | Verify credentials/establish signed-in identity | Login checks the password hash |
| Authorization | Decide permissions for that user/action | Owner/admin-only deletion would need additional rules |
| Credential | Value used to prove/access an identity/service | Login password or SMTP credential |
| Session | Server-managed state connecting a user's requests | Session identifies the signed-in account |
| Cookie | Small value browser stores/sends for a site | Session cookie connects requests to session data |
| Guard | Laravel mechanism checking authentication | web guard uses browser sessions |
| User provider | How Laravel retrieves account records | Eloquent provider loads User |
| Remember token | Value supporting remembered authentication | users.remember_token; not readable password |
| CSRF | Cross-site request forgery, a forged action using a victim's session | Laravel checks form token before accepting writes |
| CSRF token | Session-related value required by protected forms | @csrf creates hidden _token |
| Hash | One-way password representation for checking | Hash::make before storage |
| Encryption | Reversible protection using a key | Laravel encryption uses APP_KEY; password hashes are different |
| Session fixation | Attacker tries to reuse a known session identity | Regenerate session ID after login |
| Rate limiting/throttling | Restrict repeated actions over time | Login failures and reset-email cooldown |
| Validation | Check input against acceptance rules | required, email, confirmed, decimal |
| Sanitization | Transform/clean input for a particular purpose | Different concept from checking a value against rules |
| Escaping | Encode text for safe display in a context | Blade double braces escape HTML |
| XSS | Cross-site scripting, unwanted script in page content | Escaped user product names help prevent HTML/script insertion |
| SQL injection | Input changes SQL instructions instead of being data | Bound values in these Eloquent calls prevent interpolation mistakes |
| Parameter binding | Send query values separately from SQL structure | The product create/update values are bound |
| Role | Named category of user permissions | Admin would be a new role feature, not supplied by auth alone |
| Policy/gate | Laravel authorization decision code | Possible owner-only update extension |
| Password broker | Laravel service managing reset requests/tokens | sendResetLink and reset calls |
| Reset token | Temporary proof required to change a password | Token in the newest emailed reset URL |
| Expiry | Time after which a value/request stops being valid | Reset expiry is 60 minutes here |
| Email verification | Prove control of an account email | Requires extra model configuration in this app |
| SMTP | Protocol used to submit outgoing email | Mailtrap/Gmail transport |
| Mailer | Component selecting/building/sending email | Laravel mailer uses smtp here |
| Transport | Mechanism carrying a message to delivery | Symfony SMTP transport |
| TLS | Protection for data while transmitted | SMTP can upgrade through STARTTLS |
| STARTTLS | Upgrade an existing connection to TLS | Used with our smtp schemes/provider ports |
| App Password | Provider-generated credential for an application | Gmail sender credential, separate from Laravel login |
| Sandbox | Isolated test environment | Mailtrap captures email in a test inbox |
| Notification | Application message sent through a channel | ResetPassword notification uses email |
| Event/listener | Occurrence and code responding to it | Registered event allows account-created handling |
| Exception | Failure interrupting normal execution | ValidationException returns field errors through Laravel |
| Error bag | Named collection of form errors | Profile password/delete bags stay separate |
| Flash message | Session feedback intended for the next request | Product saved successfully after redirect |

A password input's dots only change its display. Show/Hide does not alter hashing.
Similarly, a hidden token input is still inspectable and editable by the browser
user. The server verifies it instead of trusting it just because it is hidden.

## 7. Programming-language terms

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Syntax | Rules for writing valid code | PHP variable prefix, braces, semicolons |
| Statement | One executable instruction | Assign a value or return a response |
| Expression | Code producing a value | auth()->check() produces a boolean |
| Variable | Named storage/reference for a value | $products |
| Literal | Value written directly in code | 60, 'Apple', true |
| String | Text value | Product name |
| Integer | Whole number | Quantity |
| Boolean | true/false value | Remember me selected or not |
| Array | Collection indexed by keys | Validated field values |
| Key/value | Array identifier / its associated data | 'qty' maps to 12 |
| Function | Callable unit of work | route() helper |
| Method | Function belonging to a class/object | Controller index() or model update() |
| Parameter | Variable declared to receive a call's input | Request $request |
| Argument | Actual value passed to a call | 'product.index' passed to route() |
| Return value | Result sent back to the caller | View or redirect |
| Class | Definition of object data/behavior | ProductController or User |
| Object/instance | A concrete class value | Selected Product record |
| Property/attribute | Data associated with an object | $product->name |
| Inheritance | Reuse/extend a parent class | Product extends Model |
| Visibility | Which code may access a class member | public method; protected fillable |
| Namespace | Organized class-name scope | App\Models |
| Import | Bring a class/module name into this file | use Product or JS import Alpine |
| Interface | Contract a class declares it meets | MustVerifyEmail controls enforcement eligibility |
| Trait | Shared class behavior included in a class | Notifiable |
| Closure | Anonymous function, sometimes capturing outer values | Route group callback |
| Callback | Function supplied to be called at a later processing step | Reset broker invokes password-change callback |
| Operator | Symbol performing a language operation | = assigns; === compares strictly |
| Conditional/branch | Select execution based on a condition | Home guest vs signed-in redirect |
| Loop/iteration | Repeat work for each item | @forelse product rows |
| Scope | Where a variable/name is accessible | Closure captures Request with use |
| Chain | Calls using the previous call's returned object | redirect()->route()->with() |
| Constant | Named fixed value | Password::PASSWORD_RESET status |
| Type hint | Declared expected parameter/return type | LoginRequest and RedirectResponse |
| Null | No value present | Optional description |
| Fallback/default | Value used when none was supplied/found | old(field, savedValue) |
| Comment | Explanation ignored by execution | // describes a route connection |

One method name can appear in unrelated classes. Controller `update()` handles
an HTTP action; model `update()` saves a record. Identify the object/class before
assuming two calls do the same work.

## 8. HTML, Blade, CSS, and browser interaction terms

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| HTML | Markup describing document structure | Forms, headings, table |
| Element/tag | One markup object / its written delimiter | input element or opening form tag |
| Attribute | Additional element setting | name, type, class, required |
| Form | Controls submitted together to a server action | Registration fields |
| Label | Field name linked to its input | for=email matches id=email |
| Submit | Send the form values | Save Product POST |
| Hidden input | Submitted control not drawn visibly | Reset token and _method |
| Placeholder | Hint when a text field is empty | you@example.com; not a field label |
| Autocomplete | Hint to browser/password managers | new-password vs current-password |
| Blade | Laravel server template language | Directives and escaped expressions |
| Template | Source for producing repeated page structure | products.index |
| Layout | Shared page shell | Navbar and content area |
| Section/yield | Provided/included inheritance-layout content | Product content section |
| Component | Reusable view/class with props/content | x-guest-layout |
| Slot | Content inserted into a component | Login form becomes $slot |
| Prop | Input/default to a component | Dropdown alignment |
| Partial/include | Smaller view inserted in another | Profile forms |
| Directive | Blade template instruction | @csrf, @if, @extends |
| Rendering | Convert template/data into displayed output | Blade becomes HTML |
| DOM | Browser's structured representation of the page | JS finds and updates input elements |
| Selector | Pattern finding/styling matching elements | .auth-input input[type=password] |
| CSS | Rules controlling appearance/layout | Glass panel background and spacing |
| Class | Markup token selected by CSS/JS | auth-field |
| Cascade/specificity | Rules deciding which CSS declaration wins | Password padding must beat generic input padding |
| Box model | Content, padding, border, margin sizing | border-box includes padding in declared width |
| Padding/margin | Internal/external spacing | Space within/between panels |
| Flexbox | Layout method arranging children | Header/action alignment |
| Responsive design | Layout adapts to available screen space | Mobile form padding and table scrolling |
| Viewport | Browser's available page display area | 600px media query threshold |
| Media query | Conditional CSS rules | Smaller screens or reduced motion |
| Transparency/alpha | How much backdrop shows through | rgba glass fill |
| Gradient | Gradual color/light variation | Neutral background and button sheen |
| Backdrop blur | Blur pixels behind an element | Frosted navbar |
| Pseudo-class/element | State selector / generated decorative part | :hover and navbar ::after divider |
| Event/listener | Browser action and callback responding | Theme button click |
| Timer | Scheduled future browser callback | Five-second success-banner dismissal |
| LocalStorage | Browser site storage | Theme choice, not login password |
| SVG | Vector image markup | Envelope and lock icons |
| Focus | Element receiving keyboard input | Tab to checkbox or Show button |
| Accessibility | Usability for different input/assistive needs | Labels, focus rings, readable error text |
| ARIA | Extra accessibility semantics | pressed state on theme/password toggle |
| Utility class | Single-purpose styling class | Generated Tailwind flex/padding classes |

## 9. Tools, builds, and version-control terms

| Term | Plain meaning | Example / reason in this app |
|---|---|---|
| Terminal/CLI | Text interface for commands | PowerShell runs Artisan |
| Working directory | Folder a command runs from | app-crud contains artisan |
| Artisan | Laravel command-line entry/tool | serve, migrate, route:list, test |
| Composer | PHP dependency manager | Installs Breeze and Laravel packages |
| npm | Node package manager/script runner | Installs/builds frontend dependencies |
| Node.js | JavaScript runtime outside the browser | Runs Vite build tooling |
| Vite | Frontend build/development tool | Produces compiled CSS/JS |
| Build | Transform source into served artifacts | npm run build |
| Manifest | File describing produced build artifacts | Vite manifest maps entry assets |
| Lockfile | Exact resolved dependency versions | composer.lock / package-lock.json |
| Cache | Stored reusable work/data | Compiled Blade or config; not source to edit |
| Clear cache | Remove that stored work so it is rebuilt/read again | view:clear / config:clear |
| Runtime artifact | File produced while the app/tools run | Compiled views and logs |
| Log | Recorded events/errors | storage/logs; avoid exposing secrets |
| Git | Source-history tool | Tracks the project versions |
| Repository | Files and version history under Git | The public coursework repository |
| Commit | Saved snapshot in history | Lab 2 checkpoint commit |
| Branch | Named line of commits | main |
| Tag | Named reference to a checkpoint | lab2-complete |
| Remote | Repository location elsewhere | origin on GitHub |
| Push | Send local commits/references to a remote | Publish a reviewed Lab 3 checkpoint |
| Diff | Comparison of versions | Review changed routes/comments |
| Ignore rule | Pattern excluding local files from tracking | .env and dependencies |
| Test/assertion | Executed check / expected result it verifies | assertRedirect verifies login destination |
| Unit/feature test | Small behavior / app request-flow test | Sample assertion vs authentication request |
| Fake/mock | Controlled replacement during tests | Notification::fake avoids real email |
| Test isolation | Checks separated from real data/services | SQLite :memory: and array mail |

Git tracks code and migrations. It does not automatically back up MySQL rows.
Clearing a view cache does not mean deleting tables. Knowing which kind of stored
thing you are changing prevents destructive "cleanup" guesses.

## 10. Connect the terms to one action

When Save Product is clicked, the HTML form submits a POST request containing
field values. Laravel matches the route. web middleware checks the session/CSRF
and auth requires a signed-in user. The controller validates values, then calls
the Eloquent model to insert a database row. It returns a redirect with a flash
message. The browser requests the list; Blade renders it and CSS styles it.
JavaScript later removes the banner after five seconds.

That explanation connects the terms to a sequence. A standalone definition
becomes easier to remember when you can point to its role in this action.

## 11. Short checks to strengthen understanding

Try each prediction before reading its answer.

### GET versus POST

Opening /product/create does what? Pressing Save Product does what?

Answer: GET displays a form. POST submits its values to store, which validates
and inserts. Opening the form should not insert a row.

### Route versus middleware

If a guest sends a valid POST directly to /product, which check stops the request?

Answer: auth middleware stops guest access before store runs. Valid input cannot
replace the required authenticated session.

### Middleware versus validation

A logged-in user submits price 1.999. Why is it rejected?

Answer: login passes auth, but controller decimal validation rejects the value.
Different checks evaluate identity and submitted data.

### Class versus object

What is Product, and what is $product after route model binding?

Answer: Product is the model class. $product is the loaded object representing
the selected record. Its ->name reads that object's attribute.

### Cookie versus localStorage

Does changing the saved theme preference change authentication?

Answer: no. Our localStorage value controls appearance. Laravel authentication
uses its session/cookie mechanism.

### Hash versus password visibility

Does pressing Show reveal a password decrypted from the users table?

Answer: no. It displays the currently typed field value. The database stores a
hash; the Show button does not decrypt or read it.

### Migration versus model

Which creates the products columns, and which saves a new Apple row?

Answer: the migration creates schema; Product::create saves the row using it.

### Redirect versus view

Does redirect()->route('product.index') render the table by itself?

Answer: no. The browser follows a new request to the list route, whose controller
returns the rendered view.

### Backend versus frontend

Does changing CSS remove a product from MySQL?

Answer: no. CSS changes presentation; the server-side delete action changes data.

### Authentication versus authorization

Does being logged in prove a user is an administrator?

Answer: no. It establishes authentication. Admin/ownership permissions need
additional data and authorization checks.

## 12. Learn to explain rather than repeat

For a term, write four short lines: its meaning, the problem it solves, the file
where it appears, and one prediction about removing/changing it. Example:

```text
auth middleware: requires a signed-in session before protected code runs.
Problem: guests could otherwise call product endpoints directly.
Location: six routes inside the auth group in routes/web.php.
Prediction: removing it would remove that login check from those endpoints.
```

Then check the source and correct the explanation. In a safe test environment,
write an assertion for the predicted result instead of disabling protection on
the real app. Explain it again the next day without looking. Add a concrete
example when two terms seem similar. Use the complete guide for code walkthroughs
and the API reference when the unfamiliar thing is an expression/method name.
