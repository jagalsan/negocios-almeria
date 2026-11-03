# Reglas del proyecto

- `docs/SPEC.md` resume la especificación adjunta y es la fuente de verdad del repositorio.
- Ante una duda, usa la solución más simple compatible y anótala en `docs/DECISIONS.md`.
- Lógica de negocio en `app/Actions`, salidas públicas en `app/Data`, permisos en Policies.
- Nunca serialices un modelo completo en una salida pública ni expongas propietario, creador o CIF.
- El Top 10 es editorial. Patrocinados separados, etiquetados y fuera del ItemList.
- No introduzcas Node, Redis, Docker ni procesos permanentes como requisito del servidor.
- Usa los copies de la especificación. No inventes cifras, premios o empresas reales.
- Antes de cerrar cambios ejecuta `vendor/bin/pint`, `php artisan test` y `npm run build`.
