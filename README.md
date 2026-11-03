# Negocios Almería

Directorio editorial de empresas digitales de Almería con rankings Top 10, fichas de empresa, reclamación de propiedad, backoffice Filament y API pública.

## Requisitos

- PHP 8.3 con `bcmath`, `ctype`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml` y `zip`.
- Composer 2.
- MySQL 8 o MariaDB 10.6 en producción.
- Node 22 solo para compilar assets en desarrollo o CI; no es necesario en el servidor.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

La configuración local incluida usa SQLite. El seeder crea 30 empresas ficticias con «ejemplo» en el nombre, tres rankings y un usuario de demostración para `/admin`:

- Email: `admin@negociosalmeria.test`
- Contraseña: `Cambiar12345!`

Estas credenciales son solo para desarrollo. No ejecutes el seeder en producción y crea el primer admin con `php artisan make:filament-user`.

## Verificación

```bash
vendor/bin/pint --test
php artisan test
npm run build
```

## Producción en Hostinger (hosting compartido)

### Cómo funciona el despliegue

Cada `git push` a `main` lanza `.github/workflows/deploy.yml`:

1. Ejecuta los tests (`ci.yml`: Pint, Pest y build de Vite). Si fallan, no se despliega.
2. Instala `vendor/` sin dependencias de desarrollo y compila los assets en GitHub Actions.
3. Sube la aplicación por `rsync` + SSH a `DEPLOY_PATH`. Nunca toca `.env`, `storage/app` (subidas y backups), `storage/logs` ni la base de datos.
4. En el servidor ejecuta `migrate --force`, crea el enlace `public/storage` (Hostinger deshabilita `exec` y `symlink` en PHP, así que `storage:link` falla y se usa `ln -s`), `optimize`, `filament:optimize` y `rankings:snapshot`.

También se puede lanzar a mano desde GitHub → Actions → Desplegar → Run workflow. El servidor no necesita Node, Composer, Redis, Docker ni procesos permanentes.

### Estructura en el servidor

```text
/home/<usuario>/
├── negocios-almeria/                  ← DEPLOY_PATH: el proyecto Laravel y su .env
└── domains/<dominio>/public_html      ← enlace simbólico a negocios-almeria/public (document root)
```

En la cuenta conviven otros sitios: no toques otras carpetas de `domains/`. El PHP por defecto por SSH es 8.2; usa siempre `/opt/alt/php83/usr/bin/php`.

### Puesta en marcha (una sola vez)

1. **hPanel → Avanzado → Configuración PHP**: PHP 8.3 con `intl`, `gd`, `zip`, `bcmath`, `fileinfo` y `pdo_mysql`.
2. **hPanel → Bases de datos → Gestión**: crea una base MySQL y su usuario.
3. **hPanel → Avanzado → Acceso SSH**: activa SSH y añade la clave pública de despliegue. Para crearla en tu equipo:
   ```bash
   ssh-keygen -t ed25519 -N "" -C "github-deploy-negocios-almeria" -f ~/.ssh/negocios_almeria_deploy
   cat ~/.ssh/negocios_almeria_deploy.pub
   ```
4. **Prepara el servidor** por SSH (`ssh -p 65002 -i ~/.ssh/negocios_almeria_deploy <usuario>@<ip>`):
   ```bash
   mkdir -p ~/negocios-almeria/public
   cd ~/domains/<dominio>
   mv public_html public_html.bak && ln -s ~/negocios-almeria/public public_html
   nano ~/negocios-almeria/.env   # ver bloque siguiente
   ```
   `.env` de producción:
   ```dotenv
   APP_NAME="Negocios Almería"
   APP_ENV=production
   APP_KEY=base64:...        # php artisan key:generate --show
   APP_DEBUG=false
   APP_URL=https://<dominio>
   APP_LOCALE=es
   APP_FALLBACK_LOCALE=es
   APP_FAKER_LOCALE=es_ES
   LOG_LEVEL=warning
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u000000000_nombre
   DB_USERNAME=u000000000_nombre
   DB_PASSWORD=...
   CACHE_STORE=file
   SESSION_DRIVER=database
   QUEUE_CONNECTION=database
   FILESYSTEM_DISK=public
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=465
   MAIL_SCHEME=smtps
   MAIL_USERNAME=contacto@<dominio>
   MAIL_PASSWORD=...
   MAIL_FROM_ADDRESS=contacto@<dominio>
   MAIL_FROM_NAME="${APP_NAME}"
   ```
   Añade también los bloques `LEGAL_*` y `GOOGLE_*` de la sección «Datos legales y Google».
5. **Secretos en GitHub** (repo → Settings → Secrets and variables → Actions → New repository secret):

   | Secreto | Valor |
   |---|---|
   | `DEPLOY_HOST` | IP SSH de Hostinger |
   | `DEPLOY_PORT` | `65002` |
   | `DEPLOY_USER` | usuario SSH (`u000000000`) |
   | `DEPLOY_PATH` | `/home/<usuario>/negocios-almeria` |
   | `DEPLOY_SSH_KEY` | contenido de `~/.ssh/negocios_almeria_deploy` (`pbcopy < ~/.ssh/negocios_almeria_deploy`) |
   | `DEPLOY_KNOWN_HOSTS` | salida de `ssh-keyscan -p 65002 <ip>` |

   Y en la pestaña *Variables*, `DEPLOY_PHP` = `/opt/alt/php83/usr/bin/php` (en Hostinger `php` por SSH es 8.2).
6. **Primer push** a `main`. El workflow crea las tablas.
7. **Datos iniciales**: importa los datos o crea el primer admin con `php artisan make:filament-user`. No ejecutes `db:seed` en producción salvo `--class=LegalPagesSeeder`.
8. **Cron** (hPanel → Avanzado → Trabajos cron → Personalizado, «Once Per Minute»):
   ```text
   /opt/alt/php83/usr/bin/php /home/<usuario>/negocios-almeria/artisan schedule:run
   ```
9. **SSL**: hPanel → Seguridad → SSL, activa el certificado gratuito del dominio.

### Desplegar a partir de ahora

```bash
git add -A && git commit -m "Cambio" && git push origin main
```

El estado y los logs de cada despliegue se ven en la pestaña **Actions** de GitHub.

El cron lanza el scheduler (`routes/console.php`): procesa la cola con `queue:work --stop-when-empty` cada minuto, hace copias a las 02:00–02:15 y el snapshot de rankings a las 03:00.

### Copias

Configura `spatie/laravel-backup` con un disco externo y programa una copia diaria de base de datos y `storage/app/public`:

```bash
php artisan backup:run
php artisan backup:clean
```

## Backoffice

`/admin` incluye empresas, rankings con entradas ordenables, categorías, municipios, reclamaciones, patrocinios, páginas y usuarios. Solo los roles `admin` y `editor` pueden entrar. La cuenta de propietarios vive en `/mi-cuenta`; nunca accede a Filament.

## API

Base: `/api/v1`. Todas las respuestas públicas proceden de los mismos Presenters que usa la web y tienen un límite de 60 solicitudes por minuto.

```text
GET /api/v1/rankings
GET /api/v1/rankings/{slug}
GET /api/v1/companies?q=
GET /api/v1/companies/{slug}
GET /api/v1/categories
GET /api/v1/municipalities
```

Ejemplo:

```js
const response = await fetch('https://negociosalmeria.es/api/v1/rankings/agencias-de-marketing', {
  headers: { Accept: 'application/json' },
});
const ranking = await response.json();
```

Nunca se incluyen CIF, creador, propietario, reclamaciones ni historial privado.

## Importar el Excel editorial

El comando reutilizable valida las hojas `Rankings` y `Fichas n.º 1`, importa empresas, categorías y posiciones, y deja todo en borrador:

```bash
php artisan rankings:import "/ruta/rankings-negocios-almeria.xlsx"
```

Para sustituir las entradas de demostración y archivar sus empresas:

```bash
php artisan rankings:import "/ruta/rankings-negocios-almeria.xlsx" --replace-examples
```

Las webs ausentes, sedes pendientes, fuentes y dominios compartidos quedan identificados en los datos importados para su revisión desde el backoffice.

## Datos legales y Google

Antes de producción, completa la identidad jurídica y los identificadores reales en `.env`:

```dotenv
LEGAL_OWNER_NAME="Nombre o razón social"
LEGAL_TRADE_NAME="Negocios Almería"
LEGAL_OWNER_NIF="NIF o CIF"
LEGAL_OWNER_ADDRESS="Domicilio completo"
LEGAL_OWNER_EMAIL="contacto@negociosalmeria.es"
LEGAL_REGISTRY="Datos registrales, si corresponden"
LEGAL_JURISDICTION="Almería"

GOOGLE_TAG_MANAGER_ID="GTM-XXXXXXX"
GOOGLE_ANALYTICS_ID="G-XXXXXXXXXX"
GOOGLE_SITE_VERIFICATION="código-de-search-console"
COOKIE_CONSENT_VERSION=1
```

Si Analytics se configura dentro de GTM, el identificador directo de Analytics puede quedar vacío para evitar medición duplicada. Cada cambio material en categorías o proveedores de cookies debe incrementar `COOKIE_CONSENT_VERSION` para solicitar de nuevo el consentimiento.

Actualiza las páginas legales ejecutando:

```bash
php artisan db:seed --class=LegalPagesSeeder
```

## Documentación

- `docs/SPEC.md`: requisitos de producto.
- `docs/DESIGN.md`: sistema visual y patrones prohibidos.
- `docs/DECISIONS.md`: decisiones técnicas y compatibilidad.
