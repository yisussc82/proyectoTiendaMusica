<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::SUPPORT);
    }

    public function view(User $user, Client $client): bool
    {
        return $client->user_id === $user->id || $this->viewAny($user);
    }

    public function update(User $user, Client $client): bool
    {
        return $client->user_id === $user->id || $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
