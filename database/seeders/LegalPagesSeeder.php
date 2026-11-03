<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = e(config('legal.owner_name'));
        $tradeName = e(config('legal.trade_name'));
        $email = e(config('legal.email'));
        $taxId = config('legal.tax_id') ? e(config('legal.tax_id')) : 'Pendiente de configurar';
        $address = config('legal.address') ? e(config('legal.address')) : 'Pendiente de configurar';
        $registry = config('legal.registry') ? e(config('legal.registry')) : 'No aplicable o pendiente de configurar';
        $jurisdiction = e(config('legal.jurisdiction'));

        $pages = [
            'aviso-legal' => [
                'title' => 'Aviso legal',
                'description' => 'Información legal, condiciones de acceso y responsabilidades de Negocios Almería.',
                'body' => <<<HTML
<p>Este aviso regula el acceso y uso de <strong>{$tradeName}</strong>, disponible en negociosalmeria.com (el «sitio web»). Al navegar por el sitio aceptas estas condiciones.</p>
<h2>1. Titular del sitio</h2>
<dl class="legal-data"><div><dt>Responsable</dt><dd>{$owner}</dd></div><div><dt>NIF/CIF</dt><dd>{$taxId}</dd></div><div><dt>Domicilio</dt><dd>{$address}</dd></div><div><dt>Email</dt><dd><a href="mailto:{$email}">{$email}</a></dd></div><div><dt>Registro</dt><dd>{$registry}</dd></div></dl>
<h2>2. Finalidad</h2>
<p>El sitio publica un directorio editorial de negocios digitales de la provincia de Almería, rankings por especialidad, fichas informativas, contenidos relacionados y espacios publicitarios claramente identificados.</p>
<h2>3. Condiciones de uso</h2>
<p>Debes utilizar el sitio de forma lícita, diligente y respetuosa. No puedes intentar acceder a zonas restringidas, alterar datos, introducir código malicioso, automatizar consultas abusivas, suplantar identidades ni utilizar el contenido para actividades ilícitas.</p>
<h2>4. Contenido editorial y fichas</h2>
<p>Las fichas se elaboran con información pública, datos aportados por las empresas y revisión editorial. Aunque procuramos mantenerlas actualizadas, no garantizamos que toda información sea completa o esté libre de errores. Las empresas pueden reclamar su ficha y cualquier persona puede solicitar una corrección o retirada mediante la página de contacto.</p>
<p>La posición en los rankings se decide con criterios editoriales públicos. Los patrocinios se muestran separados, identificados y no modifican el orden editorial.</p>
<h2>5. Propiedad intelectual</h2>
<p>El diseño, código, textos editoriales, selección, estructura y elementos propios del sitio pertenecen a su titular o se usan con autorización. Las marcas, nombres y logotipos de terceros pertenecen a sus respectivos titulares. No se autoriza la reproducción o explotación del contenido más allá de los usos permitidos por la ley sin autorización previa.</p>
<h2>6. Enlaces externos</h2>
<p>Los enlaces a sitios de empresas y terceros se facilitan para ampliar información. No controlamos sus contenidos, disponibilidad, seguridad ni políticas de privacidad. La inclusión de un enlace no implica aprobación de todos sus contenidos o servicios.</p>
<h2>7. Responsabilidad</h2>
<p>No respondemos de decisiones comerciales tomadas exclusivamente a partir del directorio, interrupciones ajenas a nuestro control, daños causados por terceros, contenidos externos ni usos contrarios a estas condiciones. Nada de lo publicado sustituye el análisis profesional o contractual que corresponda.</p>
<h2>8. Legislación y jurisdicción</h2>
<p>Este aviso se rige por la legislación española. Cuando la normativa lo permita, las partes se someten a los juzgados y tribunales de {$jurisdiction}, sin perjuicio de los derechos de consumidores y usuarios.</p>
<h2>9. Contacto</h2>
<p>Para cuestiones legales, correcciones o retirada de contenido, escribe a <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],
            'privacidad' => [
                'title' => 'Política de privacidad',
                'description' => 'Cómo trata Negocios Almería los datos personales, durante cuánto tiempo y qué derechos puedes ejercer.',
                'body' => <<<HTML
<p>Esta política explica cómo tratamos tus datos personales conforme al Reglamento (UE) 2016/679 (RGPD) y a la Ley Orgánica 3/2018 (LOPDGDD).</p>
<h2>1. Responsable del tratamiento</h2>
<dl class="legal-data"><div><dt>Responsable</dt><dd>{$owner}</dd></div><div><dt>NIF/CIF</dt><dd>{$taxId}</dd></div><div><dt>Domicilio</dt><dd>{$address}</dd></div><div><dt>Contacto</dt><dd><a href="mailto:{$email}">{$email}</a></dd></div></dl>
<h2>2. Datos que tratamos</h2>
<ul><li>Datos de cuenta: nombre, email, teléfono, contraseña cifrada, verificaciones y actividad de acceso.</li><li>Datos de representantes de empresas: cargo, teléfono, acreditaciones, reclamaciones e historial de cambios.</li><li>Datos enviados en formularios de contacto, corrección, retirada o patrocinio.</li><li>Datos públicos de empresas: nombre comercial, web, contacto corporativo, dirección, servicios y perfiles sociales.</li><li>Datos técnicos y de uso: páginas visitadas, eventos agregados y cookies analíticas, solo cuando prestas consentimiento.</li></ul>
<h2>3. Finalidades y bases jurídicas</h2>
<table class="legal-table"><thead><tr><th>Finalidad</th><th>Base jurídica</th></tr></thead><tbody><tr><td>Crear y proteger tu cuenta</td><td>Ejecución de las condiciones y medidas precontractuales</td></tr><tr><td>Verificar representantes y gestionar fichas</td><td>Ejecución de la solicitud e interés legítimo en evitar fraudes</td></tr><tr><td>Atender consultas, correcciones y bajas</td><td>Consentimiento, obligación legal e interés legítimo</td></tr><tr><td>Enviar comunicaciones operativas</td><td>Ejecución del servicio</td></tr><tr><td>Medir el uso con Google Analytics</td><td>Consentimiento revocable</td></tr><tr><td>Prevenir abusos y proteger el sitio</td><td>Interés legítimo y seguridad de la información</td></tr></tbody></table>
<h2>4. Publicación y privacidad del propietario</h2>
<p>Los datos de las personas que gestionan una ficha se usan exclusivamente para verificar su relación con la empresa y comunicarnos con ellas. Nunca se publican, se incluyen en datos estructurados ni se entregan mediante la API pública.</p>
<h2>5. Destinatarios y proveedores</h2>
<p>No vendemos datos personales. Pueden acceder proveedores que prestan alojamiento, correo, copias de seguridad, seguridad, soporte o analítica bajo contratos de encargo. También comunicaremos datos cuando exista una obligación legal o una solicitud válida de una autoridad.</p>
<h2>6. Transferencias internacionales</h2>
<p>Si aceptas las cookies analíticas, Google puede tratar identificadores y datos técnicos fuera del Espacio Económico Europeo. Se aplicarán los mecanismos reconocidos por el RGPD, como decisiones de adecuación, el Marco de Privacidad de Datos UE–EE. UU. cuando corresponda o cláusulas contractuales tipo. Consulta los detalles en la <a href="/cookies/">política de cookies</a>.</p>
<h2>7. Conservación</h2>
<ul><li>Cuenta: mientras permanezca activa y durante los plazos necesarios para atender responsabilidades.</li><li>Reclamaciones y verificaciones: mientras dure la relación y durante los plazos legales aplicables.</li><li>Consultas: el tiempo necesario para responder y, como máximo, durante los plazos de responsabilidad.</li><li>Analítica: según la duración indicada para cada cookie y hasta que retires el consentimiento.</li></ul>
<h2>8. Derechos</h2>
<p>Puedes solicitar acceso, rectificación, supresión, oposición, limitación y portabilidad, así como retirar el consentimiento sin efectos retroactivos. Escribe a <a href="mailto:{$email}">{$email}</a> indicando el derecho que deseas ejercer. Podremos pedir información para verificar tu identidad.</p>
<p>También puedes reclamar ante la Agencia Española de Protección de Datos en aepd.es.</p>
<h2>9. Seguridad y menores</h2>
<p>Aplicamos medidas técnicas y organizativas proporcionadas al riesgo. El servicio no se dirige a menores de 14 años y no solicitamos conscientemente sus datos.</p>
<h2>10. Cambios</h2>
<p>Actualizaremos esta política cuando cambien los tratamientos o la normativa. La fecha de actualización aparece al inicio de la página.</p>
HTML,
            ],
            'cookies' => [
                'title' => 'Política de cookies',
                'description' => 'Cookies necesarias y opcionales de Negocios Almería, Google Tag Manager y Google Analytics, y cómo cambiar el consentimiento.',
                'body' => <<<HTML
<p>Esta política explica qué tecnologías usamos, para qué sirven y cómo puedes aceptar, rechazar o retirar tu consentimiento en cualquier momento.</p>
<h2>1. Qué son las cookies</h2>
<p>Las cookies son pequeños archivos que un sitio o un tercero guarda en el navegador. También usamos almacenamiento local para recordar tu elección de privacidad. Algunas tecnologías son necesarias para que el sitio funcione; otras permiten medir su uso o ejecutar etiquetas de marketing.</p>
<h2>2. Cómo funciona el consentimiento</h2>
<p>Las categorías opcionales están desactivadas por defecto. Google Tag Manager y Google Analytics no se cargan hasta que aceptas la categoría «Analítica». Las señales de Google Consent Mode v2 se inicializan como denegadas y se actualizan únicamente después de tu elección.</p>
<p>Puedes aceptar todas, rechazarlas o configurar categorías por separado. La elección se conserva en tu navegador y puedes cambiarla mediante «Configurar cookies» en el pie de cualquier página.</p>
<h2>3. Tecnologías necesarias</h2>
<table class="legal-table"><thead><tr><th>Nombre</th><th>Proveedor</th><th>Finalidad</th><th>Duración</th></tr></thead><tbody><tr><td><code>laravel-session</code></td><td>Negocios Almería</td><td>Mantener la sesión y proteger las zonas privadas</td><td>Sesión o hasta 2 horas</td></tr><tr><td><code>XSRF-TOKEN</code></td><td>Negocios Almería</td><td>Proteger formularios frente a solicitudes maliciosas</td><td>Hasta 2 horas</td></tr><tr><td><code>na_cookie_consent</code></td><td>Negocios Almería</td><td>Guardar en almacenamiento local las categorías aceptadas y la versión de la política</td><td>Hasta que borres los datos o cambie la versión</td></tr></tbody></table>
<h2>4. Analítica opcional</h2>
<table class="legal-table"><thead><tr><th>Nombre</th><th>Proveedor</th><th>Finalidad</th><th>Duración habitual</th></tr></thead><tbody><tr><td><code>_ga</code></td><td>Google Analytics</td><td>Distinguir navegadores y generar estadísticas agregadas</td><td>Hasta 2 años</td></tr><tr><td><code>_ga_*</code></td><td>Google Analytics</td><td>Mantener el estado de medición de una propiedad</td><td>Hasta 2 años</td></tr></tbody></table>
<p>Usamos Google Tag Manager para gestionar etiquetas. Tag Manager no crea por sí mismo perfiles publicitarios, pero las etiquetas activadas desde el contenedor pueden utilizar cookies según su categoría. La configuración del contenedor debe respetar en todo momento el consentimiento enviado por este sitio.</p>
<h2>5. Marketing opcional</h2>
<p>La categoría de marketing permanece desactivada salvo aceptación expresa. Actualmente el sitio no necesita publicidad comportamental para funcionar. Si en el futuro se incorporan etiquetas publicitarias mediante GTM, deberán respetar las señales <code>ad_storage</code>, <code>ad_user_data</code> y <code>ad_personalization</code> y esta tabla se actualizará antes de activarlas.</p>
<h2>6. Search Console</h2>
<p>Google Search Console se utiliza para verificar la propiedad del dominio y consultar su rendimiento en buscadores. La metaetiqueta de verificación no instala cookies ni realiza seguimiento del visitante.</p>
<h2>7. Cómo retirar el consentimiento</h2>
<p>Abre «Configurar cookies» en el pie, desmarca las categorías y guarda. Al retirar Analítica intentaremos borrar las cookies de Google accesibles desde este dominio y dejaremos de cargar sus scripts. También puedes eliminar cookies y almacenamiento local desde la configuración de tu navegador.</p>
<h2>8. Más información</h2>
<p>Google puede actuar como proveedor independiente para determinados tratamientos. Puedes consultar sus políticas en policies.google.com/privacy. Para cualquier duda, escribe a <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],
            'condiciones' => [
                'title' => 'Condiciones de uso',
                'description' => 'Condiciones para consultar el directorio, crear una cuenta, proponer empresas y gestionar fichas.',
                'body' => <<<HTML
<p>Estas condiciones regulan el uso de {$tradeName}, incluido el directorio público, las cuentas, las propuestas de empresas, las reclamaciones y la gestión de fichas.</p>
<h2>1. Aceptación y capacidad</h2>
<p>Al usar el sitio aceptas estas condiciones y las políticas enlazadas. Para crear una cuenta debes tener capacidad legal suficiente y facilitar información veraz y actualizada.</p>
<h2>2. Cuenta de usuario</h2>
<p>Eres responsable de proteger tus credenciales y de las acciones realizadas con tu cuenta. Debes avisarnos si detectas un acceso no autorizado. Podemos bloquear cuentas utilizadas para fraude, suplantación, abuso o incumplimiento.</p>
<h2>3. Propuestas y reclamaciones</h2>
<p>Proponer una empresa o reclamar una ficha no garantiza su publicación o aprobación. Podemos comprobar identidad, relación con la empresa, sede, web, actividad y fuentes. No debes reclamar empresas que no representas ni aportar datos falsos.</p>
<h2>4. Edición de fichas</h2>
<p>Un propietario aprobado puede editar únicamente los campos habilitados. Los cambios pueden quedar sujetos a moderación. El nombre, clasificación, municipio, valoración y posiciones editoriales corresponden al equipo del directorio.</p>
<h2>5. Rankings y patrocinios</h2>
<p>El orden de los rankings es editorial y no se vende. Los patrocinios aparecen en espacios separados, identificados y con enlaces etiquetados como patrocinados. La contratación de publicidad no garantiza inclusión ni posición en un ranking.</p>
<h2>6. Contenido aportado</h2>
<p>Al enviar textos, imágenes o logotipos declaras que puedes utilizarlos y autorizas su reproducción en la ficha para prestar el servicio. No debes aportar datos personales innecesarios, material ilícito, engañoso o que infrinja derechos de terceros.</p>
<h2>7. Correcciones y retirada</h2>
<p>Las empresas y titulares de derechos pueden solicitar la corrección o retirada de contenido. Evaluaremos cada petición y podremos conservar información cuando exista una base legítima u obligación legal.</p>
<h2>8. Disponibilidad y cambios</h2>
<p>Podemos modificar, suspender o actualizar funcionalidades, categorías y contenidos. Procuraremos evitar pérdidas y comunicar cambios relevantes, pero no garantizamos disponibilidad ininterrumpida.</p>
<h2>9. Baja de la cuenta</h2>
<p>Puedes solicitar la eliminación de tu cuenta. Revocaremos tus propiedades activas y eliminaremos o anonimizaremos los datos personales cuando proceda. Las fichas empresariales pueden continuar publicadas con información corporativa pública.</p>
<h2>10. Ley aplicable</h2>
<p>Las condiciones se rigen por la legislación española. Cuando la normativa lo permita, cualquier controversia se someterá a los juzgados y tribunales de {$jurisdiction}.</p>
<h2>11. Contacto</h2>
<p>Para consultas sobre estas condiciones escribe a <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],
        ];

        foreach ($pages as $slug => $page) {
            Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'seo_title' => $page['title'].' · Negocios Almería',
                    'seo_description' => $page['description'],
                    'status' => 'published',
                ],
            );
        }
    }
}
