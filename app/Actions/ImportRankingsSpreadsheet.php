<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Ranking;
use App\Models\RankingEntry;
use App\Models\User;
use App\Support\DomainNormalizer;
use App\Support\NameNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportRankingsSpreadsheet
{
    public function handle(string $path, bool $replaceExamples = false): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("No existe el archivo: {$path}");
        }

        $spreadsheet = IOFactory::load($path);
        $rankingsSheet = $spreadsheet->getSheetByName('Rankings');
        $profilesSheet = $spreadsheet->getSheetByName('Fichas n.º 1');

        if (! $rankingsSheet || ! $profilesSheet) {
            throw new InvalidArgumentException('El Excel debe contener las hojas Rankings y Fichas n.º 1.');
        }

        $rows = $this->rankingRows($rankingsSheet);
        $profiles = $this->profiles($profilesSheet);
        $companies = collect($rows)->groupBy('Empresa');
        $duplicateDomains = [];

        return DB::transaction(function () use ($rows, $profiles, $companies, $replaceExamples, &$duplicateDomains) {
            if ($replaceExamples) {
                Ranking::query()->update(['status' => 'draft', 'published_at' => null]);
                RankingEntry::query()->delete();
                Company::query()->where('name', 'like', 'Empresa digital ejemplo %')->update(['status' => 'archived']);
            }

            $author = User::query()->whereIn('role', ['admin', 'editor'])->first()
                ?? throw new InvalidArgumentException('Debe existir un usuario admin o editor para firmar los rankings.');
            $categories = Category::query()->get()->keyBy('name_plural');
            $importedCompanies = [];
            $usedDomains = Company::query()->whereNotNull('website_domain')->pluck('name', 'website_domain')->all();

            foreach ($companies as $name => $companyRows) {
                $profile = $profiles[$name] ?? [];
                $first = $companyRows->first();
                $municipalityName = $this->text($profile['Municipio'] ?? $first['Municipio']);
                $municipality = Municipality::firstOrCreate(
                    ['slug' => Str::slug($municipalityName)],
                    ['name' => $municipalityName, 'postal_code_prefix' => '04'],
                );
                $website = $this->firstValue([$profile['Web'] ?? null, ...$companyRows->pluck('Web')->all()]);
                $domain = DomainNormalizer::normalize($website);
                $normalized = NameNormalizer::normalize($name);
                $existing = Company::withTrashed()->where('name_normalized', $normalized)->first();

                if ($domain && isset($usedDomains[$domain]) && $usedDomains[$domain] !== $name && $existing?->website_domain !== $domain) {
                    $duplicateDomains[] = ['company' => $name, 'domain' => $domain, 'usedBy' => $usedDomains[$domain]];
                    $website = null;
                    $domain = null;
                }

                $primaryCategory = $this->text($profile['Categoría principal'] ?? $first['Categoría']);
                $highlight = $companyRows->pluck('Dato destacado (para el motivo)')->filter()->join(' ');
                $sources = $companyRows->pluck('Fuente')->filter()->unique()->values()->all();
                $shortDescription = $this->text($profile['Descripción breve (≤160)'] ?? null)
                    ?: Str::limit("{$primaryCategory} en {$municipalityName}.", 160, '');
                $description = $this->text($profile['Descripción completa (borrador)'] ?? null)
                    ?: $highlight;
                $statuses = $companyRows->pluck('Estado')->filter()->unique()->values()->all();
                $social = $this->social($profile, $sources, $statuses, $duplicateDomains, $name);

                $company = Company::withTrashed()->updateOrCreate(
                    ['name_normalized' => $normalized],
                    [
                        'name' => $name,
                        'slug' => $existing?->slug ?: Str::slug($name),
                        'website_url' => $website,
                        'website_domain' => $domain,
                        'cif' => $this->cif($profile['Pendiente / avisos'] ?? null),
                        'status' => 'draft',
                        'origin' => 'admin',
                        'is_verified' => false,
                        'short_description' => $shortDescription,
                        'description' => $description,
                        'services' => $this->list($profile['Servicios'] ?? null),
                        'sectors' => $this->list($profile['Sectores'] ?? null),
                        'municipality_id' => $municipality->id,
                        'address' => $this->nullableText($profile['Dirección'] ?? null),
                        'postal_code' => $this->postalCode($profile['Código postal'] ?? null),
                        'phone_public' => $this->phone($profile['Teléfono'] ?? null),
                        'email_public' => $this->nullableText($profile['Email'] ?? null),
                        'opening_hours' => $this->openingHours($profile['Horario'] ?? null),
                        'social' => $social,
                        'founded_year' => $this->year($profile['Año de fundación'] ?? null),
                        'team_size' => $this->teamSize($profile['Tamaño del equipo'] ?? null),
                        'google_rating' => $this->rating($profile['Valoración de Google'] ?? null),
                        'google_reviews_count' => $this->integer($profile['Nº de reseñas'] ?? null),
                        'published_at' => null,
                        'reviewed_at' => null,
                        'deleted_at' => null,
                    ],
                );

                if ($domain) {
                    $usedDomains[$domain] = $name;
                }

                $profileCategories = collect(preg_split('/;\s*/', $this->text($profile['Categorías secundarias'] ?? '')) ?: [])
                    ->map(fn ($categoryName) => trim((string) preg_replace('/\s*\(.*/u', '', $categoryName)))
                    ->filter();
                $categoryNames = $companyRows->pluck('Categoría')->merge($profileCategories)->unique()->take(5)->values();
                $categoryIds = $categoryNames->mapWithKeys(function ($categoryName) use ($categories, $primaryCategory) {
                    $category = $categories->get($categoryName);
                    if (! $category) {
                        throw new InvalidArgumentException("No existe la categoría: {$categoryName}");
                    }

                    return [$category->id => ['is_primary' => $categoryName === $primaryCategory]];
                });
                if (! $categoryIds->contains('is_primary', true) && $categoryIds->isNotEmpty()) {
                    $firstCategory = $categoryIds->keys()->first();
                    $categoryIds[$firstCategory] = ['is_primary' => true];
                }
                $company->categories()->sync($categoryIds->all());
                $importedCompanies[$name] = $company;
            }

            $importedRankings = 0;
            foreach (collect($rows)->groupBy('Categoría') as $categoryName => $rankingRows) {
                $category = $categories->get($categoryName)
                    ?? throw new InvalidArgumentException("No existe la categoría: {$categoryName}");
                $count = $rankingRows->count();
                $ranking = Ranking::updateOrCreate(
                    ['category_id' => $category->id, 'municipality_id' => null],
                    [
                        'title' => "Las {$count} mejores ".Str::lower($category->name_plural).' en Almería',
                        'slug' => $category->slug,
                        'h1' => "Las {$count} mejores ".Str::lower($category->name_plural).' de Almería',
                        'answer' => "Esta selección reúne {$count} ".Str::lower($category->name_plural).' de la provincia de Almería. Consulta el orden completo, el motivo de inclusión y los datos disponibles de cada empresa.',
                        'primary_keyword' => 'mejores '.Str::lower($category->name_plural).' en Almería',
                        'secondary_keywords' => [Str::lower($category->name_plural).' Almería', 'empresas digitales Almería', 'servicios digitales Almería'],
                        'intro' => '<h2>Cómo comparar estas empresas</h2><p>Revisa su especialización, experiencia, trabajos y datos de contacto. El orden se basa en la información editorial disponible para cada categoría.</p>',
                        'criteria' => ['Experiencia y actividad verificables', 'Sede o equipo en la provincia de Almería', 'Especialización relacionada con la categoría'],
                        'faq' => [['q' => '¿Cómo se elabora este ranking?', 'a' => 'El equipo revisa experiencia, trabajos, especialización y fuentes públicas.'], ['q' => '¿Se puede pagar por subir?', 'a' => 'No. Los patrocinios aparecen separados del orden editorial.'], ['q' => '¿Cada cuánto se revisa?', 'a' => 'Cada ranking se revisa al menos cada seis meses.'], ['q' => '¿Cómo se corrige un dato?', 'a' => 'La empresa puede reclamar su ficha o enviar una corrección.']],
                        'closing_text' => '¿Tu empresa trabaja en Almería y debería estar en esta lista? Solicita una revisión o reclama tu ficha.',
                        'author_id' => $author->id,
                        'status' => 'draft',
                        'published_at' => null,
                        'reviewed_at' => now(),
                    ],
                );
                $ranking->entries()->delete();

                foreach ($rankingRows->sortBy('Posición') as $row) {
                    $company = $importedCompanies[$row['Empresa']];
                    $reason = trim($this->text($row['Dato destacado (para el motivo)']));
                    RankingEntry::create([
                        'ranking_id' => $ranking->id,
                        'company_id' => $company->id,
                        'position' => (int) $row['Posición'],
                        'reason' => $reason ?: 'Información pendiente de completar y verificar antes de publicar.',
                    ]);
                }
                $importedRankings++;
            }

            return [
                'companies' => count($importedCompanies),
                'rankings' => $importedRankings,
                'entries' => count($rows),
                'archivedExamples' => Company::query()->where('name', 'like', 'Empresa digital ejemplo %')->where('status', 'archived')->count(),
                'duplicateDomains' => $duplicateDomains,
            ];
        });
    }

    private function rankingRows(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, false);
        $headers = array_map(fn ($value) => $this->text($value), array_shift($rows));

        return collect($rows)
            ->filter(fn ($row) => filled($row[2] ?? null))
            ->map(fn ($row) => array_combine($headers, array_pad($row, count($headers), null)))
            ->values()
            ->all();
    }

    private function profiles(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, false);
        $companies = array_slice(array_shift($rows), 1);
        $profiles = [];

        foreach ($companies as $index => $company) {
            if (! filled($company)) {
                continue;
            }
            foreach ($rows as $row) {
                if (filled($row[0] ?? null)) {
                    $profiles[$this->text($company)][$this->text($row[0])] = $row[$index + 1] ?? null;
                }
            }
        }

        return $profiles;
    }

    private function social(array $profile, array $sources, array $statuses, array $duplicateDomains, string $name): array
    {
        $social = array_filter([
            'linkedin' => $this->nullableText($profile['LinkedIn'] ?? null),
            'instagram' => $this->nullableText($profile['Instagram'] ?? null),
            'facebook' => $this->nullableText($profile['Facebook'] ?? null),
        ]);
        foreach (preg_split('/\s+·\s+/', $this->text($profile['YouTube / TikTok / X'] ?? '')) ?: [] as $url) {
            $key = str_contains($url, 'youtube') ? 'youtube' : (str_contains($url, 'tiktok') ? 'tiktok' : (str_contains($url, 'x.com') ? 'x' : 'other'));
            $social[$key] = $url;
        }
        $social['sources'] = $sources;
        $social['import_status'] = $statuses;
        $duplicate = collect($duplicateDomains)->firstWhere('company', $name);
        if ($duplicate) {
            $social['shared_website'] = $duplicate['domain'];
        }

        return $social;
    }

    private function list(mixed $value): array
    {
        return collect(preg_split('/;\s*/', $this->text($value)) ?: [])->filter()->values()->all();
    }

    private function openingHours(mixed $value): ?array
    {
        return filled($value) ? ['text' => $this->text($value)] : null;
    }

    private function firstValue(array $values): ?string
    {
        return collect($values)->map(fn ($value) => $this->nullableText($value))->first(fn ($value) => $value !== null);
    }

    private function teamSize(mixed $value): ?string
    {
        preg_match('/(1|2[–-]5|6[–-]10|11[–-]25|26[–-]50|50\+)/u', $this->text($value), $matches);

        return isset($matches[1]) ? str_replace('–', '-', $matches[1]) : null;
    }

    private function year(mixed $value): ?int
    {
        preg_match('/\b(19|20)\d{2}\b/', $this->text($value), $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function rating(mixed $value): ?float
    {
        preg_match('/\d+[,.]\d+/', $this->text($value), $matches);

        return isset($matches[0]) ? (float) str_replace(',', '.', $matches[0]) : null;
    }

    private function integer(mixed $value): ?int
    {
        preg_match('/\d+/', $this->text($value), $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function cif(mixed $value): ?string
    {
        preg_match('/\b[A-Z]\d{8}\b/', $this->text($value), $matches);

        return $matches[0] ?? null;
    }

    private function postalCode(mixed $value): ?string
    {
        preg_match('/\b04\d{3}\b/', $this->text($value), $matches);

        return $matches[0] ?? null;
    }

    private function phone(mixed $value): ?string
    {
        $phone = trim(explode('·', $this->text($value))[0]);

        return $phone !== '' ? Str::limit($phone, 20, '') : null;
    }

    private function nullableText(mixed $value): ?string
    {
        $value = $this->text($value);

        return $value !== '' ? $value : null;
    }

    private function text(mixed $value): string
    {
        return trim((string) $value);
    }
}
