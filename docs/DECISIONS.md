# Decisiones

## 2026-10-09

- Desarrollo y tests usan SQLite para no exigir un servidor local. Producción conserva MySQL/MariaDB en `.env.example` y README.
- `spatie/laravel-responsecache` 8.x, única rama compatible con Laravel 13, exige PHP 8.4. Como la especificación fija PHP 8.3, se sustituye por caché HTTP nativa hasta que exista una versión compatible.
- Las empresas del seeder son ficticias, incluyen «ejemplo» en nombre y texto, y usan dominios `.test`.
- Los textos legales se marcan como pendientes de asesoría, tal como exige la especificación.
- Las fuentes públicas se cargarán desde Bunny durante desarrollo y deben autoalojarse antes del despliegue de producción.
- El límite de categorías por empresa se amplía de 3 a 5 para conservar la clasificación del Excel editorial entregado el 10 de octubre de 2026.
- El Excel se importa íntegro como borrador: ninguna empresa ni ranking se publica hasta revisar webs, sedes y textos pendientes.
- SEO: cada página publica un único grafo JSON-LD (Organization + WebSite + nodos propios) generado con `App\Support\Seo`. Si una ficha o ranking no tiene `seo_title`/`seo_description`, se generan a partir de sus datos reales (categoría principal, municipio, servicios, nº de empresas) con títulos ≤ 60 y descripciones ≤ 155 caracteres. No se añade `Review` ni `AggregateRating`.
- Las URL canónicas van sin barra final porque el servidor redirige con 301 las URL con barra a su versión sin barra.
- `/empresas?municipio=…` es indexable y entra en el sitemap como página de municipio; los filtros por especialidad son `noindex,follow` porque ya existe el ranking de cada especialidad.
