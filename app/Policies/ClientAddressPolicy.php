<?php

namespace App\Policies;

use App\Models\ClientAddress;
use App\Models\User;

class ClientAddressPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->client !== null;
    }

    public function create(User $user): bool
    {
        return $user->client !== null;
    }

    public function update(User $user, ClientAddress $address): bool
    {
        return $this->owns($user, $address);
    }

    public function delete(User $user, ClientAddress $address): bool
    {
        return $this->owns($user, $address) && ! $address->is_default;
    }

    private function owns(User $user, ClientAddress $address): bool
    {
        return $address->client->user_id === $user->id;
    }
}
