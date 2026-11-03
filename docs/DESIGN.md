# Sistema de diseño

## Dirección

«Luz de Almería»: una guía local muy luminosa y sobria, no una aplicación SaaS ni un periódico. El gesto propio son los números de ranking grandes y una composición de guía editorial.

## Tokens

| Token | Valor | Uso |
|---|---:|---|
| cal | `#FFFFFF` | fondo principal |
| cal-sombra | `#EEF2F1` | bandas secundarias |
| basalto | `#1C2B2D` | texto y logotipo |
| basalto-suave | `#4A5B5D` | texto secundario |
| mar | `#0D5E6D` | enlaces, botones, posiciones |
| mar-hondo | `#083F4A` | hover y pie |
| pita | `#C9D6D0` | bordes y fondos de logos |
| azufre | `#F0C419` | foco, patrocinio y Top 10; máximo 5 % |
| estado-ok | `#2E7D5B` | confirmaciones |
| estado-error | `#B42318` | errores |

Sin degradados. Contraste AA. Texto sobre azufre siempre basalto.

## Tipografía

Archivo variable para titulares, logotipo, botones y números; Literata variable para lectura. Logotipo: «negocios almería», minúsculas, Archivo 800 y anchura 125. Escala: 0,875; 1; 1,125; 1,375; 1,75; 2,25; 3; 4,5 rem.

## Maquetación

12 columnas en escritorio. Lectura en 1–8 y datos en 9–12. Alineación izquierda. Radio de 4 px. Sin sombras. Espaciado en múltiplos de 8. Secciones separadas 96 px en escritorio y 64 px en móvil. Pie en mar-hondo.

### Home

```text
[logo]  rankings · empresas · metodología  [buscar] [añade] [acceder]

H1 de dos líneas                        buscador ancho
Entradilla
──────────────────────────────────────────────────────────
Rankings por especialidad
01  categoría                  revisado · 10 empresas
    primeras tres empresas
02  categoría                  revisado · 10 empresas
──────────────────────────────────────────────────────────
Recién revisados (lista editorial, no tarjetas)
Municipios (enlaces en línea)
Cómo elegimos                 Para empresas
FAQ
[pie oscuro de cuatro columnas]
```

### Ranking

```text
Migas
H1                                                   revisión
Respuesta directa (medida corta)
[patrocinados: bloque frío + borde azufre, si existen]

Ranking 2026
01 | logo  Empresa / especialidad / municipio
   |       motivo editorial                        acciones
02 | ...
Comparativa
Cómo hemos elegido             datos laterales
Contenido editorial
FAQ
Cierre para empresas
```

### Ficha

```text
Migas
logo  Empresa                                      acciones
      especialidad · municipio · verificación
posición en ranking
Descripción y servicios            datos / contacto
Galería
Empresas similares (lista)
Propiedad / corrección
```

## Movimiento

Solo los números del ranking entran en cascada con 8 px de desplazamiento y 40 ms entre filas. Transiciones de 150 ms. `prefers-reduced-motion` desactiva todo.

## Prohibido

Degradados, glassmorphism, blobs, ruido, morado/índigo/violeta/rosa, tarjetas iguales con iconos decorativos, sombras grises, radios grandes, etiquetas en mayúsculas espaciadas, una palabra del titular de otro color, metadatos con puntos medios, flechas en botones, emojis, estadísticas inventadas, scroll reveal, carruseles, fotos de stock, iframes y mapas en carga inicial.

## Revisión contra lo genérico

Se descarta una cuadrícula de tarjetas para categorías. El índice usa filas numeradas y tipografía de guía. Se elimina todo hero decorativo: la identidad viene de la composición, el blanco frío y los números. Los patrocinados usan una señal funcional, no una tarjeta promocional.
