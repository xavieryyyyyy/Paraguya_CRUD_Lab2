# Laboratory 3 guide-question answers

The questions below follow Section V of the Laboratory 3 PDF. Use the short
answers when speaking, then point to the example/code when asked to explain more.
They describe the actual application; they do not claim admin roles or required
email verification have been implemented.

Use [Instructor code locator](INSTRUCTOR_CODE_LOCATOR.md) for exact source files,
current line numbers, code excerpts, and likely follow-up questions. That appendix
is the final part of the printable handbook.

## 1. What is Laravel Breeze, and what problem does it solve?

Breeze is a Laravel authentication starter package that generates routes,
controllers, request validation, views, and tests for common account features.
It provides registration, login, logout, password recovery, and profile-related
code, so the developer can inspect/customize these flows rather than write every
piece from an empty project.

In this application, Composer installed Breeze, then `breeze:install` generated
the Blade/Alpine scaffold. npm installed frontend tools and the build compiled
their assets. Breeze did not automatically protect our existing Product routes;
we added the auth middleware group separately.

Point to `routes/auth.php`, `app/Http/Controllers/Auth/`, and the auth views.
Explain the difference between installing the package and generating its scaffold.
Keep expected form names/routes when changing its appearance.

Short answer: "Breeze generates the account forms and authentication code we
need, including login, registration, and password recovery. We customized the
views and separately protected the existing product routes."

## 2. What is the difference between registration and authentication?

Registration creates an account. Authentication verifies that someone using an
existing account supplied valid credentials and establishes their signed-in
state. Our registration handler also signs in the newly created user, but those
remain two different operations in its code.

Example: entering a new email/password on Register creates a users row with a
password hash. Later, submitting Login checks that saved account and hash; it
does not create another users row. An email already registered is rejected by
the unique-email rule on Register, while the same email is expected on Login.

Point to `RegisteredUserController::store()` and `LoginRequest::authenticate()`.

Short answer: "Registration creates the account; authentication checks the
account credentials when the user signs in. Registration also logs the new user
in in our Breeze flow."

## 3. What is the purpose of the auth middleware?

auth middleware checks the session's authentication before allowing the request
to reach its protected controller. For our HTML product routes, a guest is
redirected to named login. A valid signed-in session lets the request continue.

The group in web.php contains all six product routes, including POST store,
PUT update, and DELETE destroy. Protecting only the list page would leave those
other actions accessible through direct requests.

Point to `Route::middleware('auth')->group(...)` and show route:list with `-v`.
Then demonstrate requesting `/product` while logged out.

Short answer: "auth middleware requires a signed-in session before the product
controller runs. Guests are redirected to Login, and all six CRUD routes are
inside the protected group."

## 4. Why should Product CRUD routes be protected from unauthenticated users?

Product records are application data. If visitors could call store/update/destroy
without authenticating, they could create or change records without an account.
Protecting every action establishes the lab's login boundary, including direct
requests that do not use the visible buttons.

This boundary does not decide ownership or administrator privileges. The current
Product model has no user_id, and there are no policies checking who owns a
product. All authenticated users can still manage the shared list.

Point to the auth group and the lack of an owner relationship/policy. Explain
that hiding a Delete button alone is not server-side protection.

Short answer: "Login protection prevents guests from reading or changing product
records through our CRUD endpoints. We would need authorization rules as well
to restrict particular logged-in users or owners."

## 5. What is the role of SMTP configuration in password recovery?

SMTP configuration tells the mailer which email server to contact, on which port,
and which credentials/sender identity to use. Laravel's password broker creates
the reset request and notification; the SMTP transport delivers that email.
SMTP itself does not validate the reset token or save the new password.

We tested Mailtrap's sandbox and Gmail one at a time. Mailtrap captured the email
in a test inbox; Gmail delivered it to the registered account's inbox. The email
link opened the form with a token, which NewPasswordController passed to the
broker for verification before updating the account password.

Point to `config/mail.php`, `PasswordResetLinkController`, and
`NewPasswordController`. Show the delivered email while hiding its reset URL.

Short answer: "SMTP makes the reset email deliverable. Laravel creates and
verifies the reset token, while the configured mail server sends the link to the user."

## 6. Why should email credentials be kept in the .env file?

They vary by developer/deployment and should not be hard-coded into a controller
or committed to a public repository. The private `.env` supplies credentials to
`config/mail.php`; a safe `.env.example` can show field names without secrets.
The repository ignore rules exclude the real environment file.

An `.env` is not magic encryption. A leaked file or printed configuration still
exposes its values. Do not include it in screenshots, paste it into chats, or
serve the project root as a public web directory. Application keys and database
passwords need the same care. If a credential is published, remove/revoke/rotate
it through its provider; deleting it from the current Git file does not erase history.

Point to `config/mail.php` and `.gitignore`, without displaying actual credentials.

Short answer: "The private environment file separates sensitive deployment values
from source code. We commit only an example with no secrets, and config/mail.php
reads the real values locally."

## 7. What is the difference between authentication and authorization?

Authentication verifies identity or signed-in state. Authorization decides
whether that identified user is allowed to do a particular action. A user may
be authenticated yet lack permission to delete someone else's record.

Example: both a clerk and manager may log in successfully. A separate policy
could allow only the manager to delete products. That clerk/manager rule is an
example of a possible extension; our code currently has neither role nor policy.

Point to `auth` in web.php for implemented authentication. Explain that policies,
gates, or explicit ownership checks would enforce additional authorization.
`$fillable`, CSS themes, and the Remember me checkbox are not permission checks.

Short answer: "Authentication checks who is signed in. Authorization checks
what that user may do. Our auth group supplies the first check; role/ownership
rules would be additional work."

## 8. Does placing a route inside the auth middleware group automatically make a user an administrator? Explain your answer.

No. The group checks that the visitor is authenticated using the configured
guard. It does not add an administrator flag, assign a role, or compare the user
against a permission policy. Any signed-in user can reach our Product routes.

To make certain actions administrator-only, the application would need a real
role/permission representation and a server-side authorization check, tested
with users who are allowed and users who are denied. Merely naming a route
admin or making its button invisible does not implement this check.

Point to User's fields and the auth-only product group. State the current limit
without claiming a role feature was added by Breeze.

Short answer: "No. auth only requires login. Administrator privileges need
separate data and authorization rules, which our current application does not have."

## Required deliverables from the PDF

1. Screenshot of the Breeze login page (our customized Breeze view is acceptable evidence of its UI).
2. Screenshot of the registration page.
3. Screenshot showing a successfully registered user.
4. Screenshot of Product CRUD after login.
5. Screenshot of the login redirect when a logged-out user requests `/product`.
6. Screenshot of a password-reset email or a successful email test.
7. Copy of modified `routes/web.php`.
8. Short explanation of how auth middleware protects Product CRUD.

For item 8, an example explanation is: "All six product routes are in the auth
middleware group. Laravel checks the web session before calling ProductController.
A guest requesting a product route is redirected to Login; a signed-in user may
continue. This protects the endpoints from unauthenticated access, while additional
role/ownership authorization is outside our implemented scope."

The PDF states that face-to-face checking includes questions. Practice pointing
to code and tracing a request rather than only reciting the short answers.
