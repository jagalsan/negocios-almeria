<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\CompanyChange;
use App\Models\User;
use Illuminate\Support\Arr;

class UpdateOwnedCompany
{
    private const ALLOWED = ['logo_path', 'cover_path', 'gallery', 'short_description', 'description', 'services', 'sectors', 'website_url', 'phone_public', 'email_public', 'address', 'postal_code', 'opening_hours', 'social', 'founded_year', 'team_size'];

    public function handle(User $user, Company $company, array $data): Company
    {
        $company->getConnection()->transaction(function () use ($user, $company, $data) {
            $changes = Arr::only($data, self::ALLOWED);
            CompanyChange::create(['company_id' => $company->id, 'user_id' => $user->id, 'before' => $company->only(array_keys($changes)), 'after' => $changes, 'status' => 'applied']);
            $company->update($changes);
        });

        return $company->refresh();
    }
}
