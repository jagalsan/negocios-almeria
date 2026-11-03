<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EditorialPagesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'como-elaboramos-los-rankings'],
            [
                'title' => 'Cómo elaboramos los rankings',
                'seo_title' => 'Cómo elaboramos los rankings · Negocios Almería',
                'seo_description' => 'Metodología, criterios, revisiones e histórico de posiciones de los rankings de empresas digitales de Almería.',
                'status' => 'published',
                'body' => <<<'HTML'
<p>Los rankings de Negocios Almería son selecciones editoriales de empresas con actividad vinculada a la provincia. Analizamos evidencias públicas, trabajos, especialización y fiabilidad de la información. Nadie puede pagar por subir una posición.</p>
<h2>Requisitos para entrar</h2>
<p>La empresa debe tener sede, equipo o actividad verificable en la provincia de Almería, ofrecer un servicio relacionado con la categoría y disponer de una web oficial accesible. No publicamos una ficha si no podemos identificar su web o confirmar datos básicos.</p>
<h2>Criterios que analizamos</h2>
<ol>
<li><strong>Trabajos y casos verificables.</strong> Proyectos publicados, clientes identificables y ejemplos relacionados con la categoría.</li>
<li><strong>Resultados demostrables.</strong> Datos, mejoras, producto en uso, financiación o reconocimientos con una fuente contrastable.</li>
<li><strong>Especialización.</strong> El servicio debe ocupar un lugar claro en la actividad de la empresa, no ser una mención secundaria sin casos.</li>
<li><strong>Experiencia y continuidad.</strong> Trayectoria, estabilidad del equipo y capacidad para mantener el servicio.</li>
<li><strong>Reseñas públicas.</strong> Valoración, volumen, actualidad y coherencia de las opiniones, sin usar reseñas como único criterio.</li>
<li><strong>Conocimiento local.</strong> Experiencia con empresas, sectores o municipios de Almería, especialmente agroalimentación, turismo, logística y comercio.</li>
<li><strong>Información fiable.</strong> Web activa, contacto público, descripción precisa y fuentes que permitan revisar la ficha.</li>
<li><strong>Adecuación al cliente.</strong> Tipo de proyecto, sectores atendidos, tamaño del equipo y claridad de la propuesta.</li>
</ol>
<h2>Cómo decidimos el orden</h2>
<p>Primero comprobamos requisitos y fuentes. Después comparamos las empresas de una misma categoría con los criterios anteriores y redactamos un motivo editorial específico. El orden no es una puntuación matemática automática: el equipo revisa el conjunto para evitar que una sola métrica, como el número de reseñas, distorsione el resultado.</p>
<p>Un ranking necesita al menos cinco empresas con web y datos suficientes para publicarse. Puede contener hasta diez posiciones. Si una empresa deja de cumplir los requisitos, puede salir temporalmente hasta que se verifique de nuevo.</p>
<h2>Histórico, subidas y bajadas</h2>
<p>Guardamos una instantánea diaria de las posiciones públicas. Cuando una empresa cambia respecto a la instantánea anterior, mostramos una flecha verde si sube y una flecha roja si baja, junto al número de puestos de diferencia. Las fichas conservan su histórico reciente para que el cambio pueda entenderse en el tiempo.</p>
<p>Una posición puede cambiar por nuevos trabajos o resultados, una actualización de reseñas, mayor especialización, correcciones verificadas, cambios en la actividad o porque otra empresa entra o deja de cumplir los requisitos. Pagar un patrocinio nunca provoca una subida.</p>
<h2>Patrocinios separados</h2>
<p>Los espacios patrocinados aparecen antes del ranking, con la etiqueta «Patrocinado» y un diseño distinto. No reciben número, no forman parte del listado editorial y sus enlaces se identifican como patrocinados.</p>
<h2>Revisión y correcciones</h2>
<p>Revisamos cada ranking al menos cada seis meses y mostramos su fecha de revisión. Además, actualizamos datos cuando recibimos una fuente fiable o una corrección acreditada. Cualquier empresa puede reclamar su ficha o solicitar una revisión; el equipo comprueba la petición antes de cambiar contenido u orden.</p>
<h2>Fuentes y límites</h2>
<p>Usamos webs oficiales, casos publicados, registros y medios especializados, programas empresariales y otras fuentes públicas. Una ausencia de datos no demuestra falta de calidad, pero impide comparar con el mismo nivel de confianza. Por eso explicamos el motivo de cada inclusión y mantenemos visible la metodología.</p>
HTML,
            ],
        );
    }
}
