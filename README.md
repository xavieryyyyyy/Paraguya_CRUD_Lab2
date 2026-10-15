# Laboratories 2 and 3 - Laravel Product Manager

A student Laravel MVC application for creating, listing, updating, and deleting
products, with Breeze registration, login/logout, and password recovery.

## Study this project

Open [docs/START_HERE.md](docs/START_HERE.md) for the current beginner learning pack.
It links to the complete syntax/request-flow guide, a file-by-file map, all eight
Lab 3 answers, practice exercises, and a verification report. The older Lab 2
guide remains available as checkpoint documentation.

## Project structure

- `app-crud/` - the Laravel application; run Artisan and Composer from this folder.
- `output/LAB2_STUDY_GUIDE.md` - explanation of MVC, database fields, routes, validation,
  setup, demonstration steps, and practice questions.
- `output/pdf/LAB2_STUDY_GUIDE.pdf` - printable study guide.
- `PRODUCT.md` - confirmed project scope and student-code requirements.

## Lab 2 features

- Product list, create/edit forms, and confirmed deletion.
- Eloquent Product model and products-table migration.
- Name, quantity, price, and optional description validation.
- CSRF-protected forms and PUT/DELETE method fields.
- Shared Blade layout, responsive table/forms, inline SVG icons, Inter typography,
  and a light/dark theme switch using plain CSS and JavaScript.

The historical `lab2-complete` checkpoint has no authentication. The current
working application adds Breeze and protects all six product routes with auth.
It is for local coursework; no admin roles or product ownership policies are implemented.

## Local requirements

PHP 8.2 or compatible, Composer, and a MySQL-compatible database. The development
machine uses XAMPP PHP 8.2.12, MariaDB, and database port 3307. Other machines may
use a different database port. Node.js/npm are needed for the Breeze
frontend build; custom product/auth CSS and JavaScript are also loaded directly.

## Run a cloned copy (Windows PowerShell)

```powershell
cd app-crud
composer install
npm ci
npm run build
Copy-Item .env.example .env
& "C:\xampp\php\php.exe" artisan key:generate
```

Run Composer with a compatible PHP version. If another PHP installation is first
in PATH, configure Composer to use XAMPP PHP as described in the study guide.
Do not overwrite an existing local `.env` when repeating setup.

Start XAMPP MySQL. Create an empty database named `paraguya_crud_lab2`, then set
the local `.env` database host, port, username, and password to match your setup.
The committed `.env.example` contains defaults only, with an empty application key.

```powershell
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan migrate
& "C:\xampp\php\php.exe" artisan serve
```

Open http://127.0.0.1:8000/product. Keep the server terminal running. VS Code does
not need to remain open when the server runs in a separate PowerShell window.
Apache is only needed separately when using XAMPP's phpMyAdmin interface.

## Version control

The initial commit and `lab2-complete` tag preserve the application before Breeze
installation. Future Lab 3 changes can be compared with this checkpoint.

Environment secrets, installed dependencies, generated caches/logs, local
databases, agent settings, and temporary preparation tools are excluded from Git.
Git stores the code and migrations, not the live MySQL product records. Back up
the database separately before database-changing work.

## Validation already demonstrated

Create, Read, Update, Delete, browser/server decimal validation, and narrow-screen
forms/table were demonstrated during Lab 2. Later visual adjustments passed
compilation checks; final visual comparison remains a browser review task.
