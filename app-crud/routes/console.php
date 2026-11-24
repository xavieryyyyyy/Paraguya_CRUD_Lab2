<?php

// Artisan command routes, not browser routes. The sample inspire command is generated scaffold
// and does not participate in the product or login flows.


// Laravel scaffold for terminal (Artisan) commands, not browser URLs. This default inspire
// command is unrelated to the CRUD. Browser routes are in web.php.
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Only someone with terminal access can promote an existing account.
Artisan::command('users:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if ($user === null) {
        $this->error('No account found for that email.');

        return 1;
    }

    if (! $this->confirm('Grant admin access to '.$user->email.'?')) {
        return 1;
    }

    $user->role = 'admin';
    $user->saveOrFail();
    $this->info('Admin access granted.');

    return 0;
})->purpose('Promote an existing user to product administrator');
