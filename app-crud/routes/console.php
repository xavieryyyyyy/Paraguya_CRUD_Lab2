<?php

// Laravel scaffold for terminal (Artisan) commands, not browser URLs. This default inspire
// command is unrelated to the CRUD. Browser routes are in web.php.
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
