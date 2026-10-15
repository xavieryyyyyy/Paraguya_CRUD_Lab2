<?php

// Lists application service providers loaded during startup. AppServiceProvider supplies
// application-wide registration and boot hooks.


use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
];
