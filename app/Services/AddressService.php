<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientAddress;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function save(Client $client, array $data, ?ClientAddress $address = null): ClientAddress
    {
        return DB::transaction(function () use ($client, $data, $address) {
            $address ??= new ClientAddress(['client_id' => $client->id]);
            $address->fill($data)->save();

            $hasDefault = $client->addresses()->where('is_default', true)->exists();

            if ($address->is_default || ! $hasDefault) {
                $address->markAsDefault();
            }

            return $address;
        });
    }
}
