# ccincidencias — Formulario de incidencias

Implementa la especificacion `Formulario - CC.pdf` (Nodex Project, sistema de
incidencias). Formulario publico, multi-idioma (ES/FR/EN/DE/PT/NL), que envia
un correo con un bloque de datos a `incidencias@ceramicconnection.es`. **No**
escribe en ninguna base de datos de negocio, no llama a ninguna API del
sistema de incidencias, no consulta Odoo/ERP y no valida si el pedido existe.
El correo ES la entrega.

## Instalacion

1. Instalar el modulo desde el Admin (Modulos > buscar "Formulario de
   incidencias") o por CLI.
2. Ir a la configuracion del modulo y revisar:
   - **Email destino**: `incidencias@ceramicconnection.es` (ya viene por
     defecto).
   - **Email remitente (From)**: direccion fija y estable del dominio,
     p. ej. `web@ceramicconnection.es`. **No cambiarla sin avisar** al
     responsable del sistema de incidencias: la usa para reconocer estos
     correos.
   - **ID de pagina CMS de politica de privacidad**: el ID numerico de la
     pagina ya existente en la web (para el enlace del checkbox RGPD).
3. Comprobar que el envio de correo de la tienda (Preferencias > Correo) usa
   un metodo autenticado (SMTP con SPF/DKIM del dominio), igual que los
   correos de confirmacion de pedido. El formulario reutiliza esa misma
   configuracion de transporte.
4. Comprobar que `Preferencias > Correo > Tipo de correo` no esta puesto solo
   en HTML: el correo generado necesita la parte `text/plain` (la
   especificacion lo exige siempre).

## Enlaces del formulario

Un enlace fijo por idioma (registrados via `hookModuleRoutes`, funcionan sea
cual sea el idioma actual de la pagina):

| Idioma | URL |
|---|---|
| ES | `/formulario-de-incidencias` |
| FR | `/formulaire-incident` |
| EN | `/incident-form` |
| DE | `/schadensformular` |
| PT | `/formulario-de-incidencia` |
| NL | `/incidentenformulier` |

También se pueden consultar (y abrir) desde la configuración del módulo en
el admin.

## Formato del correo

Ver `ccincidencias.php` (metodo `buildDataBlock` en
`controllers/front/form.php`) y `Formulario - CC.pdf` apartados 5 y 6. Resumen:

- Asunto exacto: `[TICKET] {REFERENCIA} - {TIPO}`.
- Remitente fijo, destinatario `incidencias@ceramicconnection.es`,
  `Reply-To` = email del cliente.
- Cuerpo `text/plain`: bloque `---DATOS-TICKET-INICIO---` ... `---DATOS-TICKET-FIN---`
  sin indentar, seguido del texto legible para personas.
- `version: 1` siempre. **Cualquier cambio de formato exige acordar antes una
  nueva version con el responsable del sistema de incidencias** (no tocar
  `BLOCK_VERSION`, las claves del bloque, ni el email remitente sin avisar).
- Fotos como adjuntos reales del correo (nunca enlaces). Si superan 20 MB en
  total se descartan y se avisa al cliente en pantalla, pero el correo se
  envia igual.
- Se envia siempre, aunque la referencia no haya validado
  (`referencia_valida: no`).

## Antibot

- Honeypot (`cc_web`, campo oculto por CSS).
- Envio no instantaneo: `cc_ts` server-side, rechazo silencioso si el envio
  llega antes de `CCINCIDENCIAS_MIN_SECONDS` (3s por defecto).
- Limite de envios por IP y hora (`CCINCIDENCIAS_MAX_PER_HOUR`, 5 por
  defecto), tabla `ps_ccincidencias_log` (solo guarda IP+fecha, no datos de
  la incidencia).

## Prueba de aceptación (apartado 12 del PDF)

Antes de publicar el enlace, enviar estos 5 correos de prueba y comprobar que
llegan a la bandeja de entrada (no a spam) de `incidencias@ceramicconnection.es`:

1. Caso completo valido, con 2 fotos y todos los campos rellenos.
2. Referencia invalida (p. ej. un numero de factura) → debe llegar con
   `referencia_valida: no`.
3. Referencia tecleada en minusculas y con un espacio en medio → debe llegar
   normalizada a 9 mayusculas y `referencia_valida: si`.
4. Campos opcionales vacios y comentario de varias lineas con acentos y
   caracteres especiales (ñ, ß, ç, ã).
5. Una foto en formato HEIC desde un iPhone.
