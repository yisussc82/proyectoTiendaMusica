<?php

namespace App\Actions\Fortify;

use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        $request = new RegisterRequest;
        Validator::make($input, $request->rules(), [], $request->attributes())->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => "{$input['first_name']} {$input['last_name']}",
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->client()->create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
            ]);

            $customerRole = Role::firstOrCreate(['name' => Role::CUSTOMER]);
            $user->roles()->attach($customerRole);

            return $user;
        });
    }
}
