<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\CompanyPresenter;
use App\Data\RankingPresenter;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyStat;
use App\Models\Municipality;
use App\Models\Ranking;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function rankings()
    {
        return response()->json(['data' => Ranking::published()->get()->map(fn ($ranking) => ['uuid' => $ranking->uuid, 'slug' => $ranking->slug, 'title' => $ranking->title, 'url' => route('rankings.show', $ranking->slug), 'updatedAt' => $ranking->updated_at->toIso8601String()])]);
    }

    public function ranking(string $slug)
    {
        return response()->json(RankingPresenter::make(Ranking::published()->where('slug', $slug)->firstOrFail()));
    }

    public function companies(Request $request)
    {
        $query = Company::published()->with(['municipality', 'categories'])->orderBy('name');
        $query->when($request->query('q'), fn ($query, $term) => $query->where('name', 'like', "%{$term}%"));

        return response()->json($query->paginate(20)->through(fn ($company) => CompanyPresenter::make($company)));
    }

    public function company(string $slug)
    {
        return response()->json(CompanyPresenter::make(Company::published()->where('slug', $slug)->firstOrFail()));
    }

    public function search(Request $request)
    {
        $term = $request->validate(['q' => ['required', 'string', 'min:2']])['q'];

        return response()->json([
            'companies' => Company::published()->where('name', 'like', "%{$term}%")->take(10)->get()->map(fn ($company) => CompanyPresenter::make($company)),
            'rankings' => Ranking::published()->where('title', 'like', "%{$term}%")->take(5)->get(['uuid', 'slug', 'title']),
        ]);
    }

    public function event(Request $request)
    {
        $data = $request->validate(['company' => ['required', 'uuid', 'exists:companies,uuid'], 'type' => ['required', 'in:view,website_click,phone_click']]);
        $company = Company::published()->where('uuid', $data['company'])->firstOrFail();
        $column = ['view' => 'views', 'website_click' => 'website_clicks', 'phone_click' => 'phone_clicks'][$data['type']];
        $stat = CompanyStat::firstOrCreate(['company_id' => $company->id, 'date' => today()], ['views' => 0, 'website_clicks' => 0, 'phone_clicks' => 0]);
        $stat->increment($column);

        return response()->json(['recorded' => true], 202);
    }

    public function categories()
    {
        return response()->json(['data' => Category::where('is_published', true)->withCount(['companies' => fn ($query) => $query->published()])->get(['id', 'name', 'name_plural', 'slug'])]);
    }

    public function municipalities()
    {
        return response()->json(['data' => Municipality::withCount(['companies' => fn ($query) => $query->published()])->get(['id', 'name', 'slug'])]);
    }
}
