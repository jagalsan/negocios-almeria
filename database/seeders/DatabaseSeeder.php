<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Municipality;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Equipo Negocios Almería', 'email' => 'admin@negociosalmeria.test', 'email_verified_at' => now(), 'password' => Hash::make('Cambiar12345!'), 'role' => 'admin', 'status' => 'active', 'terms_accepted_at' => now()]);

        $municipalities = collect(['Almería', 'Roquetas de Mar', 'El Ejido', 'Vícar', 'Níjar', 'Huércal de Almería', 'Adra', 'Vera', 'Huércal-Overa', 'Garrucha', 'Mojácar', 'Berja', 'Pulpí'])->mapWithKeys(function ($name) {
            $municipality = Municipality::create(['name' => $name, 'slug' => Str::slug($name), 'postal_code_prefix' => '04']);

            return [$name => $municipality];
        });

        $categoryData = [
            ['Agencia de marketing', 'Agencias de marketing', 'agencias-de-marketing'],
            ['Empresa de desarrollo web', 'Empresas de desarrollo web', 'empresas-de-desarrollo-web'],
            ['Startup', 'Startups', 'startups'],
            ['Agencia SEO', 'Agencias SEO', 'agencias-seo'],
            ['Empresa de software y apps', 'Empresas de software y apps', 'desarrollo-de-software'],
            ['Agencia de redes sociales', 'Agencias de redes sociales', 'gestion-de-redes-sociales'],
            ['Estudio de diseño y branding', 'Estudios de diseño y branding', 'diseno-grafico-y-branding'],
            ['Productora de foto y vídeo', 'Productoras de foto y vídeo', 'fotografia-y-video'],
            ['Agencia de marketing agroalimentario', 'Agencias de marketing agroalimentario', 'marketing-agroalimentario'],
            ['Agencia de ecommerce', 'Agencias de ecommerce', 'tiendas-online'],
            ['Consultora IT', 'Consultoras IT', 'consultoria-it'],
            ['Empresa de ciberseguridad', 'Empresas de ciberseguridad', 'ciberseguridad'],
            ['Centro de formación digital', 'Centros de formación digital', 'formacion-digital'],
            ['Coworking', 'Coworkings', 'coworkings'],
        ];
        $categories = collect($categoryData)->mapWithKeys(function ($data, $index) {
            $category = Category::create(['name' => $data[0], 'name_plural' => $data[1], 'slug' => $data[2], 'description' => "Directorio editorial de {$data[1]} de la provincia de Almería.", 'is_published' => true, 'sort_order' => $index]);

            return [$data[2] => $category];
        });

        $pages = [
            'como-elaboramos-los-rankings' => ['Cómo elaboramos los rankings', '<p>Analizamos trabajos reales, resultados verificables, reseñas, experiencia local, especialización y calidad de la ficha. Nadie puede pagar por subir una posición. Los patrocinios aparecen separados y etiquetados.</p><h2>Revisión y correcciones</h2><p>Revisamos cada ranking al menos cada seis meses. Cualquier empresa puede proponer una corrección y el equipo la comprueba.</p>'],
            'sobre-negocios-almeria' => ['Sobre Negocios Almería', '<p>Negocios Almería nace para ayudar a encontrar proveedores digitales de la provincia con criterios claros. Publicamos la metodología, separamos los patrocinios y revisamos cada selección de forma periódica.</p>'],
            'anunciate' => ['Anúnciate', '<p>Los patrocinios dan visibilidad por encima del ranking, siempre con la etiqueta Patrocinado. No alteran la selección editorial ni permiten comprar una posición.</p>'],
            'anade-tu-empresa' => ['Añade tu empresa', '<p>Aparecer en Negocios Almería es gratis. Solo pedimos que tu empresa tenga sede o equipo en la provincia de Almería y ofrezca servicios digitales.</p>'],
            'contacto' => ['Contacto', '<p>Escríbenos para corregir o retirar una ficha, proponer una empresa o consultar un patrocinio. Respondemos en un máximo de 72 horas hábiles.</p>'],
            'aviso-legal' => ['Aviso legal', '<p>Contenido legal pendiente de revisión por una asesoría antes del lanzamiento.</p>'],
            'privacidad' => ['Privacidad', '<p>Los datos de las personas que gestionan una ficha se usan solo para verificar su relación con la empresa y para comunicarnos con ellas. Nunca se publican ni se ceden.</p>'],
            'cookies' => ['Cookies', '<p>Este sitio no instala cookies publicitarias. El texto definitivo debe revisarse antes del lanzamiento.</p>'],
            'condiciones' => ['Condiciones de uso', '<p>Condiciones pendientes de revisión por una asesoría antes del lanzamiento.</p>'],
        ];
        foreach ($pages as $slug => [$title, $body]) {
            Page::create(compact('slug', 'title', 'body') + ['status' => 'published', 'seo_title' => $title.' · Negocios Almería', 'seo_description' => strip_tags($body)]);
        }

        $this->call([
            LegalPagesSeeder::class,
            EditorialPagesSeeder::class,
        ]);
    }
}
