<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;

class ProductPolicy
{
    public function view(?User $user, Product $product): bool
    {
        return $product->is_active || ($user !== null && $this->managesCatalog($user));
    }

    public function viewAny(User $user): bool
    {
        return $this->managesCatalog($user);
    }

    public function create(User $user): bool
    {
        return $this->managesCatalog($user);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->managesCatalog($user);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->managesCatalog($user);
    }

    public function restore(User $user, Product $product): bool
    {
        return $this->managesCatalog($user);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->hasRole(Role::ADMIN) && ! $product->orderItems()->exists();
    }

    private function managesCatalog(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::CATALOG);
    }
}
