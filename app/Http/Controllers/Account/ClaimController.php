<?php

namespace App\Http\Controllers\Account;

use App\Actions\SubmitClaim;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function create(string $slug)
    {
        return view('account.claim', ['company' => Company::published()->where('slug', $slug)->firstOrFail()]);
    }

    public function store(Request $request, string $slug, SubmitClaim $action)
    {
        $company = Company::published()->where('slug', $slug)->firstOrFail();
        $data = $request->validate(['role_in_company' => ['required', 'string', 'max:100'], 'phone' => ['required', 'string', 'max:20'], 'message' => ['nullable', 'string', 'max:1000'], 'truthful' => ['accepted']]);
        $action->handle($request->user(), $company, $data);

        return redirect()->route('account.index')->with('status', 'Solicitud enviada. Estamos revisando tu solicitud.');
    }
}
