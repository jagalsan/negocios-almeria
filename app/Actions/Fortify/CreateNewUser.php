<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'terms' => ['accepted'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create(['name' => $input['name'], 'email' => Str::lower($input['email']), 'phone' => $input['phone'] ?? null, 'terms_accepted_at' => now(), 'password' => Hash::make($input['password'])]);
    }
}
