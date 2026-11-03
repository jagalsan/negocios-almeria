<?php

use App\Support\DomainNormalizer;
use App\Support\NameNormalizer;

test('normaliza nombres de empresa para detectar duplicados', function () {
    expect(NameNormalizer::normalize('Estudio Digital Almería, S.L.'))->toBe('estudio digital almeria')
        ->and(NameNormalizer::normalize('estudio digital almeria'))->toBe('estudio digital almeria');
});

test('normaliza dominios sin protocolo ruta ni www', function () {
    expect(DomainNormalizer::normalize('https://www.EstudioDigitalAlmeria.es/contacto?x=1'))->toBe('estudiodigitalalmeria.es')
        ->and(DomainNormalizer::normalize('https://instagram.com/empresa'))->toBeNull();
});
