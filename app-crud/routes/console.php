<?php

// Artisan command routes, not browser routes. The sample inspire command is generated scaffold
// and does not participate in the product or login flows.


// Laravel scaffold for terminal (Artisan) commands, not browser URLs. This default inspire
// command is unrelated to the CRUD. Browser routes are in web.php.
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
