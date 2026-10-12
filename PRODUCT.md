# Laboratory 2: Laravel Product CRUD

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

PHP and Laravel using MVC architecture, Blade views, Eloquent ORM, routes,
controllers, and MySQL through the XAMPP local development environment.
Development tools listed in the lab are Composer, XAMPP, PHP, and Visual Studio Code.
Verified during this lab: Laravel Framework 12.69.3, XAMPP PHP 8.2.12,
Composer 2.8.12, and XAMPP MariaDB 10.4.32 using Laravel's mysql driver.
The application lives in app-crud/. Its database is paraguya_crud_lab2
at 127.0.0.1:3307. Explicit XAMPP PHP commands avoid the default Herd PHP 8.5.

## Users

The student completing Laboratory 2 and learning to build and explain a basic
Laravel CRUD application. Teach each step patiently using beginner-friendly code.

## Product Purpose

Complete Activity No. 2: Creating CRUD Modules in Laravel. Success means a
functional product module that creates, reads, updates, and deletes database
records, with the student understanding how the MVC parts work together.

## Operating Context

Follow the supplied lab PDF and screenshots as the assignment reference.
Work locally using Laravel, XAMPP/MySQL, and phpMyAdmin for database inspection.
The example browser pages use a Laravel development server at 127.0.0.1:8000.

## Capabilities and Constraints

- Use a Product model and ProductController with routes connecting requests to
  controller methods and Blade views in resources/views/products.
- Use a products migration and table with id, name, qty, price, description,
  created_at, and updated_at, following the supplied examples.
- Provide a product list, a create form, an edit/update form, and deletion to
  satisfy the PDF's complete CRUD learning outcome.
- Keep code basic, readable, and suitable for a student learning PHP and Laravel.
- Explain file locations, commands, and how models, views, controllers, routes,
  and the database connect.
- Preserve the assignment's behavior and required fields when applying design skills.
- Do not begin lab implementation solely because design skills were installed.

## Evidence on Hand

- PDF: C:/Users/Yestin Paraguya/Downloads/Documents/Activity no.2 _ Laravel CRUD.pdf
  (17 pages; Laravel fundamentals, MVC, prerequisites, and Activity No. 2 steps).
- Screenshots: C:/Users/Yestin Paraguya/Downloads/LAB 2 CCC181/Step2.png,
  Step3.png, Step4.png, Step5.png, Step6.png, and Step7.png.
- The screenshots show the products database structure, initial product page,
  create form, saved records, product list, and edit/update form.
- The PDF requires deletion as part of CRUD; the supplied screenshots do not
  demonstrate the delete interface.

## Product Principles

- Follow the laboratory instructions and provided examples.
- Make each implementation step understandable to a beginner.
- Keep Laravel MVC responsibilities clear.
- Prefer simple code that demonstrates the assignment's learning outcomes.

## Open Decisions

- Before future UI work begins, ask the user whether to build directly in code
  or create an image mockup first. The user has requested a decision each time;
  no standing build-path preference is set.
- Submission format and any instructor-specific screenshot requirements remain unconfirmed.

## Confirmed Design and Implementation

The student approved a dark charcoal background, subtle liquid glass panels,
Inter typography (updated at the student's request), blue primary/Edit actions, coral Delete actions,
and green success feedback. Inline SVG icons provide a Lucide-like outline style
without React. Plain CSS is loaded from app-crud/public/css/style.css.
The shared navbar now has a working light/dark theme button. A small commented
plain JavaScript file at app-crud/public/js/theme.js toggles a body class and
remembers the browser preference. Dark remains the default reference appearance.

Create, Read, Update, and Delete have been demonstrated by the student, along with
browser/server decimal validation and narrow-screen layouts for all three views.
Teaching comments explain the model, controller, routes, migration, Blade views,
CSS, and related scaffold files. LAB2_STUDY_GUIDE.md contains the MVC walkthrough,
demonstration plan, screenshot suggestions, and practice questions.
