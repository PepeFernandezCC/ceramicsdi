# cc_claritycookiefirst (PrestaShop 1.7)

Módulo para cargar Microsoft Clarity **solo** cuando el usuario ha dado consentimiento mediante **CookieFirst** (modo por categorías),
con retraso (delay) solo en la primera carga (persistido en localStorage). Excluye empleados (BO) y filtra bots por User-Agent.

## Requisitos
- PrestaShop 1.7.x
- CookieFirst instalado y funcionando (su script debe cargarse en <head> antes que tags de terceros, según su guía).

## Configuración
1) Instala el módulo.
2) Ve a: Módulos -> Gestor de módulos -> Microsoft Clarity (deferred) + CookieFirst -> Configurar
3) Introduce tu **Clarity Project ID** (la parte final de https://www.clarity.ms/tag/XXXX)
4) Selecciona la categoría CookieFirst requerida (por defecto: performance).
5) Ajusta el delay inicial (ms).

## Consentimiento (CookieFirst)
Este módulo escucha eventos oficiales:
- cf_init
- cf_consent_loaded
- cf_consent

Y consulta `CookieFirst.consent` para comprobar la categoría (performance/functional/advertising).
