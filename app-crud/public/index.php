<?php

// HTTP entry point. The browser request reaches this file, which loads Composer classes and
// bootstrap/app.php, then gives the captured request to Laravel. Routes select the next
// controller; this file does not perform CRUD itself.


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// capture() packages URL, method, headers, and submitted fields into a Request. handleRequest()
// runs middleware/routing and sends the HTTP response back to the browser.
$app->handleRequest(Request::capture());
