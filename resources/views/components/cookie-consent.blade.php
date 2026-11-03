<div
    class="cookie-consent"
    data-cookie-consent
    data-version="{{ config('cookies.consent_version') }}"
    data-gtm-id="{{ config('services.google.tag_manager_id') }}"
    data-ga-id="{{ config('services.google.analytics_id') }}"
    hidden
>
    <section class="cookie-banner" data-cookie-banner role="region" aria-label="Preferencias de cookies">
        <div class="cookie-banner-copy">
            <h2>Tu privacidad, a tu elección</h2>
            <p>Usamos cookies necesarias para que la web funcione. Solo cargaremos Google Tag Manager y Analytics si aceptas las cookies analíticas. Puedes aceptar, rechazar o configurar tus preferencias.</p>
            <a href="{{ route('pages.cookies') }}">Leer la política de cookies</a>
        </div>
        <div class="cookie-banner-actions">
            <button type="button" class="button" data-cookie-accept>Aceptar todas</button>
            <button type="button" class="button button-secondary" data-cookie-reject>Rechazar opcionales</button>
            <button type="button" class="button button-link" data-cookie-customize>Configurar</button>
        </div>
    </section>

    <dialog class="cookie-dialog" data-cookie-dialog aria-labelledby="cookie-dialog-title">
        <form method="dialog">
            <div class="cookie-dialog-header">
                <div><h2 id="cookie-dialog-title">Configurar cookies</h2><p>Elige qué categorías permites. Puedes cambiar esta decisión en cualquier momento.</p></div>
                <button type="button" class="cookie-dialog-close" data-cookie-close aria-label="Cerrar">Cerrar</button>
            </div>
            <div class="cookie-category">
                <div><strong>Necesarias</strong><p>Sesión, seguridad, formularios y almacenamiento de tu elección. No pueden desactivarse.</p></div>
                <input type="checkbox" checked disabled aria-label="Cookies necesarias activadas">
            </div>
            <label class="cookie-category">
                <span><strong>Analítica</strong><span>Google Analytics, cargado directamente o mediante GTM, para obtener estadísticas agregadas.</span></span>
                <input type="checkbox" data-cookie-analytics>
            </label>
            <label class="cookie-category">
                <span><strong>Marketing</strong><span>Etiquetas publicitarias que pudieran configurarse en GTM. Actualmente no son necesarias para usar la web.</span></span>
                <input type="checkbox" data-cookie-marketing>
            </label>
            <div class="cookie-dialog-actions">
                <button type="button" class="button" data-cookie-save>Guardar preferencias</button>
                <button type="button" class="button button-secondary" data-cookie-reject>Rechazar opcionales</button>
            </div>
        </form>
    </dialog>
</div>
