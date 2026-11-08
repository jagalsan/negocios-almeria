<?php

namespace App\Http\Controllers;

use App\Actions\BuildLlmsTxt;
use App\Data\CompanyPresenter;
use App\Data\RankingPresenter;
use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Page;
use App\Models\Ranking;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'rankings' => Ranking::published()->with(['category', 'entries' => fn ($query) => $query->whereHas('company', fn ($query) => $query->published()), 'entries.company'])->latest('reviewed_at')->get(),
            'stats' => [
                'companies' => Company::published()->count(),
                'rankings' => Ranking::published()->count(),
                'municipalities' => Municipality::whereHas('companies', fn ($query) => $query->published())->count(),
            ],
            'municipalities' => Municipality::withCount(['companies' => fn ($query) => $query->published()])->orderByDesc('companies_count')->take(7)->get(),
        ]);
    }

    public function rankings()
    {
        return view('pages.rankings', ['rankings' => Ranking::published()->with(['category', 'entries' => fn ($query) => $query->whereHas('company', fn ($query) => $query->published()), 'entries.company'])->orderBy('title')->get()]);
    }

    public function ranking(string $slug)
    {
        $ranking = Ranking::published()->where('slug', $slug)->firstOrFail();

        return view('pages.ranking', ['ranking' => RankingPresenter::make($ranking)]);
    }

    public function companies(Request $request)
    {
        $query = Company::published()->with(['municipality', 'categories'])->orderBy('name');
        $query->when($request->string('categoria')->toString(), fn ($query, $slug) => $query->whereHas('categories', fn ($query) => $query->where('slug', $slug)));
        $query->when($request->string('municipio')->toString(), fn ($query, $slug) => $query->whereHas('municipality', fn ($query) => $query->where('slug', $slug)));

        $municipalities = Municipality::orderBy('name')->get();
        $municipality = $municipalities->firstWhere('slug', $request->string('municipio')->toString());
        $companies = $query->paginate(24)->withQueryString();
        $indexable = ! $request->filled('categoria') && ($municipality || ! $request->filled('municipio')) && $companies->isNotEmpty();
        $canonicalQuery = array_filter(['municipio' => $municipality?->slug, 'page' => $companies->currentPage() > 1 ? $companies->currentPage() : null]);

        return view('pages.companies', [
            'companies' => $companies,
            'categories' => Category::where('is_published', true)->orderBy('sort_order')->get(),
            'municipalities' => $municipalities,
            'municipality' => $municipality,
            'robots' => $indexable ? null : 'noindex,follow',
            'canonical' => route('companies.index', $canonicalQuery),
        ]);
    }

    public function company(string $slug)
    {
        $company = Company::published()->where('slug', $slug)->firstOrFail();

        return view('pages.company', ['company' => CompanyPresenter::make($company)]);
    }

    public function search(Request $request)
    {
        $term = trim($request->string('q')->toString());
        $companies = strlen($term) >= 2 ? Company::published()->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('short_description', 'like', "%{$term}%"))->take(12)->get() : collect();
        $rankings = strlen($term) >= 2 ? Ranking::published()->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('answer', 'like', "%{$term}%"))->take(6)->get() : collect();

        return view('pages.search', compact('term', 'companies', 'rankings'));
    }

    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $view = in_array($slug, ['aviso-legal', 'privacidad', 'cookies', 'condiciones'], true) ? 'pages.legal' : 'pages.static';

        return view($view, compact('page'));
    }

    public function sitemap()
    {
        return response()->view('pages.sitemap', [
            'rankings' => Ranking::published()->get(['slug', 'updated_at']),
            'companies' => Company::published()->where('seo_noindex', false)->get(['slug', 'updated_at']),
            'municipalities' => Municipality::whereHas('companies', fn ($query) => $query->published())->orderBy('name')->get(['slug']),
            'pages' => Page::where('status', 'published')->get(['slug', 'updated_at']),
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function llms()
    {
        return response(app(BuildLlmsTxt::class)->handle())->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
