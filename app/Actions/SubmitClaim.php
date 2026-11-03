<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Company;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitClaim
{
    public function handle(User $user, Company $company, array $data): Claim
    {
        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(['email' => 'Confirma tu email para poder reclamar empresas.']);
        }
        if ($company->ownerships()->whereNull('revoked_at')->exists()) {
            throw ValidationException::withMessages(['company' => 'Esta empresa ya está gestionada. Si crees que es un error, escríbenos.']);
        }

        return Claim::firstOrCreate(
            ['company_id' => $company->id, 'user_id' => $user->id, 'status' => 'pending'],
            ['role_in_company' => $data['role_in_company'], 'phone' => $data['phone'], 'message' => $data['message'] ?? null],
        );
    }
}
