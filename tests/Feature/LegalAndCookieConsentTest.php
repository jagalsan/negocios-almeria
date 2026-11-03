<?php

use Database\Seeders\LegalPagesSeeder;

beforeEach(function () {
    $this->seed(LegalPagesSeeder::class);
});

it('muestra las cuatro páginas legales con la plantilla común', function () {
    foreach (['aviso-legal', 'privacidad', 'cookies', 'condiciones'] as $slug) {
        $this->get('/'.$slug)->assertOk()->assertSee('Información legal')->assertSee('Configurar cookies');
    }
});

it('deniega el seguimiento antes de recibir consentimiento', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-cookie-consent', false)
        ->assertSee("analytics_storage: 'denied'", false)
        ->assertSee('Rechazar opcionales')
        ->assertDontSee('googletagmanager.com/gtm.js', false)
        ->assertDontSee('googletagmanager.com/gtag/js', false);
});

it('expone la configuración de Google sin cargar scripts anticipadamente', function () {
    config()->set('services.google.tag_manager_id', 'GTM-TEST123');
    config()->set('services.google.analytics_id', 'G-TEST123');
    config()->set('services.google.site_verification', 'verification-code');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-gtm-id="GTM-TEST123"', false)
        ->assertSee('data-ga-id="G-TEST123"', false)
        ->assertSee('name="google-site-verification" content="verification-code"', false)
        ->assertDontSee('src="https://www.googletagmanager.com', false);
});
