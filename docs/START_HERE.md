# Product Manager learning pack

Start with this page. The application is in `app-crud/`; paths in the guides are
relative to that folder unless a command shows otherwise.

## Reading order

1. [Terms and concepts](TERMS_AND_CONCEPTS.md): plain definitions, reasons,
   project examples, and short checks before reading unfamiliar code.
2. [Complete beginner guide](COMPLETE_STUDY_GUIDE.md): web requests, PHP syntax,
   MVC, CRUD, authentication, database, Blade, CSS, JavaScript, and tools.
3. [File map](CODE_MAP.md): every application source file and how it connects.
4. [Lab 3 answers](LAB3_ANSWERS.md): all eight PDF guide questions, with examples
   and the code to point to during a demonstration.
5. [Practice and demonstration](PRACTICE_AND_DEMO.md): exercises, answer checks,
   troubleshooting, and the required submission evidence.
6. [Syntax and API reference](SYNTAX_AND_API_REFERENCE.md): lookup tables for the
   operators, helpers, model/request methods, Blade directives, and browser APIs.
7. [Instructor code locator](INSTRUCTOR_CODE_LOCATOR.md): exact file names,
   current lines, excerpts, and spoken explanations for each PDF question.

Read one chapter, open its files, and trace one example. Close the guide before
explaining the example aloud. Revisit anything you could only repeat from memory.

## Which files should be edited?

The Product controller/model/routes, custom views, and public CSS/JavaScript are
the main learning files. Generated Breeze controllers and request classes are
also part of the application and are commented. Configuration/build files have
short connection notes; their values are explained in the file map.

Leave `vendor/`, `node_modules/`, runtime caches, compiled builds, and lockfile
contents to their tools. JSON does not allow comments, so package/lock definitions
are explained in the guide rather than made invalid by inserting `//`.
Keep the real `.env` private. Documentation uses placeholders, never actual mail
passwords, application keys, session cookies, or reset tokens.

## What currently happens?

| Action | Current result |
|---|---|
| Open `/` as a guest | Redirect to Login |
| Open `/` while signed in | Redirect to Products |
| Successful login | Intended protected page, otherwise Products |
| Logout | Session cleared; redirect to Login |
| Successful registration | Automatically signed in; Dashboard |
| Successful password reset | Login with reset-success status |
| Guest requests a product route | Login required |
| Signed-in user requests products | Shared product data is accessible |

Registration and login have different default redirects in the current code.
Email verification routes are generated, but verification is not enforced by the
User model. There is no administrator role or per-user product ownership policy.

## Version history and documentation

`lab2-complete` is the GitHub checkpoint before Breeze installation. The older
`LAB2_STUDY_GUIDE.md` and PDF describe that stage. This learning pack describes
the current combined Lab 2/3 application. The comments and guides are local changes
until a later reviewed Git commit/push; nothing is silently published by writing them.

Humanizer is installed in the Windows user's global `.agents/skills/humanizer`
folder. It was used to keep these explanations plain and precise. New Codex turns
can discover it; installing a skill does not run it continuously in the background.
