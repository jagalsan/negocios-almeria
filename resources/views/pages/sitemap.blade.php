{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}/</loc><lastmod>{{ \Illuminate\Support\Carbon::parse(max($lastModified, $rankingsModified))->toAtomString() }}</lastmod></url>
    <url><loc>{{ route('rankings.index') }}</loc><lastmod>{{ \Illuminate\Support\Carbon::parse($rankingsModified)->toAtomString() }}</lastmod></url>
    <url><loc>{{ route('companies.index') }}</loc><lastmod>{{ \Illuminate\Support\Carbon::parse($lastModified)->toAtomString() }}</lastmod></url>
    @foreach($rankings as $ranking)
    <url><loc>{{ route('rankings.show', $ranking->slug) }}</loc><lastmod>{{ $ranking->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
    @foreach($categories as $category)
    <url><loc>{{ route('categories.show', $category->slug) }}</loc><lastmod>{{ \Illuminate\Support\Carbon::parse($category->last_update)->toAtomString() }}</lastmod></url>
    @endforeach
    @foreach($companies as $company)
    <url><loc>{{ route('companies.show', $company->slug) }}</loc><lastmod>{{ $company->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
    @foreach($pages as $page)
    <url><loc>{{ url($page->slug) }}</loc><lastmod>{{ $page->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
</urlset>
