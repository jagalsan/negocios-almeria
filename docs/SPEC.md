# Negocios Almería — especificación de producto

Fuente de verdad: «Directorio de Negocios Digitales de Almería — Proyecto», 48 páginas, fechado el 8 de octubre de 2026. Este documento conserva sus requisitos operativos para el repositorio.

## Objetivo

Directorio editorial de empresas digitales de Almería organizado en rankings Top 10. Debe posicionarse para búsquedas locales y ser legible por buscadores e IA. Las URLs principales son `/mejores/{categoria}/` y `/empresa/{slug}/`.

## Reglas no negociables

- El equipo decide el orden editorial. Nunca se vende una posición.
- Hasta tres patrocinios por ranking, separados, con etiqueta «Patrocinado» y `rel="sponsored noopener"`; nunca forman parte del `ItemList`.
- Los usuarios se registran sin empresa. Proponer o reclamar exige sesión y email verificado.
- Toda propuesta y reclamación pasa por moderación. Solo una propiedad activa por empresa.
- El propietario solo edita campos permitidos. Nombre, slug, categorías, municipio y rating son del equipo.
- El propietario, creador, CIF y relaciones privadas nunca aparecen en HTML, sitemap, JSON-LD ni API pública.
- No se publica un ranking con menos de 5 entradas, 3 criterios, 4 FAQ, respuesta directa e introducción.
- Empresas archivadas responden 410 y no salen en el sitemap.

## Stack

Laravel 13 y PHP 8.3; Filament 5 y Livewire 4 en `/admin`; Blade y Tailwind CSS 4; Fortify; MySQL 8/MariaDB 10.6; Pest y Pint. Producción en hosting compartido, colas database, cron y assets compilados en CI. Sin Redis, Docker ni procesos permanentes.

Paquetes: `spatie/schema-org`, `spatie/laravel-sitemap`, `spatie/laravel-backup`, Intervention Image y Cloudflare Turnstile. La caché de respuesta se implementa sin `spatie/laravel-responsecache` mientras su versión para Laravel 13 exija PHP 8.4.

## Arquitectura

- Reglas de negocio en `app/Actions`.
- Salidas públicas con lista blanca en `app/Data`.
- Permisos en `app/Policies`.
- SEO y JSON-LD en `app/Support`.
- Web y API consumen los mismos Presenters.
- Backoffice en `app/Filament`; cuenta privada fuera de Filament.

## Datos

Entidades: users, categories, municipalities, companies, category_company, rankings, ranking_entries, sponsorships, ownerships, claims, company_changes, company_stats, reports, pages, redirects y settings. Las empresas tienen UUID público, nombre y dominio normalizados, estado, origen, clasificación, contenido, contacto, imágenes, SEO y fechas de publicación/revisión.

Nombre normalizado: minúsculas, sin acentos, forma jurídica, puntuación ni espacios duplicados. Dominio: host en minúsculas, sin `www`, puerto, ruta o parámetros; dominios sociales/genéricos no cuentan como web.

## Sitio público

- Home: apertura, buscador, índice de rankings, recién revisados, municipios, metodología, bloque para empresas y FAQ.
- Ranking: migas, H1, revisión, respuesta directa, patrocinados, Top 10, comparativa, criterios, contenido, FAQ, cierre y relacionados.
- Empresa: migas, identidad, verificación, rankings, descripción, servicios, datos, galería, similares, propiedad y actualización.
- Índices `/mejores/` y `/empresas/`, búsqueda no indexable, páginas editoriales y legales, 404 y 410.
- Cabecera con rankings, empresas, metodología, búsqueda, alta y acceso. Pie con transparencia y enlaces internos.

## Cuenta

Fortify ofrece registro, login, verificación y recuperación. `/mi-cuenta` muestra empresas, solicitudes y acciones. Alta en tres pasos con detección de duplicados. Reclamación con cargo, teléfono y declaración. Cada edición genera `company_changes`.

## API

Prefijo `/api/v1`, JSON UTF-8, fechas ISO 8601, 60 peticiones/minuto. Endpoints de rankings, empresas, categorías, municipios, búsqueda y eventos; endpoints privados `/me`. Respuestas públicas desde Presenters y sin datos privados.

## SEO

Title y description únicos, canonical absoluto con barra final, robots, Open Graph y un único grafo JSON-LD. Ranking: `CollectionPage`, `ItemList`, `BreadcrumbList`, `FAQPage`. Empresa: `ProfilePage`, `ProfessionalService` y migas. Sitemap por tipo, robots, `/llms.txt`, IndexNow y redirecciones 301.

## Contenido

Tono directo, profesional y local; respuesta primero. Frases cortas y párrafos autocontenidos. Sin superlativos sin fuente, relleno, keyword forzada, emojis ni datos inventados. Los seeders usan empresas ficticias marcadas como ejemplo.

## Aceptación

El admin crea y publica empresa y ranking; web y API conservan el orden. Las reglas de publicación bloquean contenido incompleto. Registro, propuesta, reclamación, aprobación, edición e historial funcionan con permisos. No se filtran propietarios. Patrocinios caducan por fecha. JSON-LD valida y el README explica despliegue, cron, backups y API.
