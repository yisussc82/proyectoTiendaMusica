<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->managesCatalog($user);
    }

    public function create(User $user): bool
    {
        return $this->managesCatalog($user);
    }

    public function update(User $user, Category $category): bool
    {
        return $this->managesCatalog($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->managesCatalog($user)
            && ! $category->products()->exists()
            && ! $category->children()->exists();
    }

    private function managesCatalog(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::CATALOG);
    }
}
