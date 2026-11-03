<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Ownership;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApproveClaim
{
    public function handle(Claim $claim, User $reviewer): void
    {
        DB::transaction(function () use ($claim, $reviewer) {
            $company = $claim->company()->lockForUpdate()->firstOrFail();
            if ($company->ownerships()->whereNull('revoked_at')->exists()) {
                abort(409, 'Esta empresa ya tiene propietario.');
            }
            Ownership::create(['company_id' => $company->id, 'user_id' => $claim->user_id, 'approved_by' => $reviewer->id, 'approved_at' => now()]);
            $claim->update(['status' => 'approved', 'resolved_by' => $reviewer->id, 'resolved_at' => now()]);
            Claim::where('company_id', $company->id)->where('id', '!=', $claim->id)->where('status', 'pending')->update(['status' => 'rejected', 'rejection_reason' => 'Otra persona ha acreditado la propiedad', 'resolved_by' => $reviewer->id, 'resolved_at' => now()]);
            $company->update(['is_verified' => true]);
        });
    }
}
