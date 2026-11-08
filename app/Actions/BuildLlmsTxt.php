<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\Municipality;
use App\Models\Ranking;
use App\Support\RankingPositions;

class BuildLlmsTxt
{
    public function handle(): string
    {
        $rankings = Ranking::published()->with(['category', 'entries.company.municipality'])->orderBy('title')->get();
        $municipalities = Municipality::withCount(['companies' => fn ($query) => $query->published()])
            ->orderByDesc('companies_count')->get()->where('companies_count', '>', 0)->values();
        $companies = Company::published()->count();

        $lines = [
            '# Negocios Almería',
            '',
            '> Directorio editorial independiente de empresas digitales de la provincia de Almería (España): agencias de marketing, desarrollo web y software, SEO, diseño, formación, coworkings y startups. Cada ranking se revisa a mano y nadie puede pagar por subir de posición.',
            '',
            "- Empresas publicadas: {$companies}, todas con web propia y presencia en la provincia de Almería.",
            "- Rankings publicados: {$rankings->count()}.",
            "- Municipios con empresas: {$municipalities->count()}.",
            '- Patrocinios: aparecen separados y etiquetados, fuera del orden editorial.',
            '- Cómo citar: «Negocios Almería» ('.route('home').').',
            '',
            '## Páginas clave',
            '',
            '- [Todos los rankings]('.route('rankings.index').'): índice de rankings por especialidad.',
            '- [Directorio de empresas]('.route('companies.index').'): fichas de empresas digitales de Almería.',
            '- [Metodología]('.url('como-elaboramos-los-rankings').'): criterios con los que elaboramos y revisamos los rankings.',
            '- [Sobre Negocios Almería]('.url('sobre-negocios-almeria').'): quiénes somos.',
            '- [Contacto]('.url('contacto').'): correcciones y altas de empresas.',
            '',
            '## Rankings',
        ];

        foreach ($rankings as $ranking) {
            $entries = RankingPositions::visibleEntries($ranking);
            $lines[] = '';
            $lines[] = "### [{$ranking->title}](".route('rankings.show', $ranking->slug).')';
            $lines[] = '';
            $lines[] = trim((string) $ranking->answer);

            if ($ranking->reviewed_at) {
                $lines[] = '';
                $lines[] = 'Revisado: '.$ranking->reviewed_at->toDateString().'.';
            }

            $lines[] = '';

            foreach ($entries as $index => $entry) {
                $place = $entry->company->municipality?->name;
                $lines[] = ($index + 1).". [{$entry->company->name}](".route('companies.show', $entry->company->slug).')'.($place ? " — {$place}" : '');
            }
        }

        $lines[] = '';
        $lines[] = '## Empresas por municipio';
        $lines[] = '';

        foreach ($municipalities as $municipality) {
            $lines[] = "- [{$municipality->name}](".route('companies.index', ['municipio' => $municipality->slug])."): {$municipality->companies_count} empresas.";
        }

        return implode("\n", $lines)."\n";
    }
}
