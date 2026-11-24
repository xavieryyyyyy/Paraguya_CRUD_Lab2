<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    // Laravel finds this policy by its name. Routes, the controller, and Blade
    // use the same permission rules.
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }
}
