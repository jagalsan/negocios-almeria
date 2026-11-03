<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Company $company): bool
    {
        return $company->status->value === 'published' || $this->update($user, $company);
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail() && $user->status !== 'blocked';
    }

    public function update(User $user, Company $company): bool
    {
        return $user->role?->canAccessAdmin() === true || $company->ownerships()->where('user_id', $user->id)->whereNull('revoked_at')->exists();
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->role === Role::Admin;
    }
}
