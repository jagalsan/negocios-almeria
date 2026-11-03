<?php

namespace App\Http\Controllers\Account;

use App\Actions\CreateCompany;
use App\Actions\UpdateOwnedCompany;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::whereHas('ownerships', fn ($query) => $query->where('user_id', $request->user()->id)->whereNull('revoked_at'))->get();

        return view('account.index', compact('companies'));
    }

    public function create()
    {
        return view('account.company-form', ['company' => null, 'categories' => Category::where('is_published', true)->get(), 'municipalities' => Municipality::orderBy('name')->get()]);
    }

    public function store(Request $request, CreateCompany $action)
    {
        $this->authorize('create', Company::class);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'website_url' => ['nullable', 'url'], 'municipality_id' => ['required', 'exists:municipalities,id'], 'category_ids' => ['required', 'array', 'min:1', 'max:5'], 'category_ids.*' => ['exists:categories,id'], 'short_description' => ['required', 'string', 'max:160'], 'description' => ['required', 'string', 'min:120'], 'phone_public' => ['nullable', 'string', 'max:20'], 'email_public' => ['nullable', 'email']]);
        $action->handle([...$data, 'status' => 'pending_review'], $request->user()->id);

        return redirect()->route('account.index')->with('status', 'Hemos recibido tu propuesta. La revisaremos en 24–48 h.');
    }

    public function edit(Request $request, string $uuid)
    {
        $company = Company::where('uuid', $uuid)->firstOrFail();
        $this->authorize('update', $company);

        return view('account.company-form', ['company' => $company, 'categories' => collect(), 'municipalities' => collect()]);
    }

    public function update(Request $request, string $uuid, UpdateOwnedCompany $action)
    {
        $company = Company::where('uuid', $uuid)->firstOrFail();
        $this->authorize('update', $company);
        $data = $request->validate(['short_description' => ['required', 'string', 'max:160'], 'description' => ['required', 'string', 'min:120'], 'website_url' => ['nullable', 'url'], 'phone_public' => ['nullable', 'string', 'max:20'], 'email_public' => ['nullable', 'email'], 'address' => ['nullable', 'string'], 'postal_code' => ['nullable', 'starts_with:04'], 'founded_year' => ['nullable', 'integer', 'between:1900,'.date('Y')], 'team_size' => ['nullable', 'string']]);
        $action->handle($request->user(), $company, $data);

        return back()->with('status', 'Cambios guardados. Ya se ven en tu ficha.');
    }
}
