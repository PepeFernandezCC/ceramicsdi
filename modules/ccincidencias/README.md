# ccincidencias — Formulario de incidencias

Implementa la especificacion `Formulario - CC.pdf` (Nodex Project, sistema de
incidencias), con una diferencia importante sobre el PDF original: **el
formulario solo esta disponible para clientes logueados** (decision del
negocio, no del PDF). Multi-idioma (ES/FR/EN/DE/PT/NL), envia un correo con
un bloque de datos a `incidencias@ceramicconnection.es` (o al email propio
del tipo de incidencia, ver mas abajo). **No** escribe en ninguna base de
datos de negocio, no llama a ninguna API del sistema de incidencias, no
consulta Odoo/ERP. El correo ES la entrega.

## Acceso y pedido asociado

- **Requiere estar logueado.** Si un visitante sin sesion abre el enlace, se
  le manda a login y, tras entrar, vuelve automaticamente al formulario (con
  el pedido preseleccionado si venia de uno).
- **Desde el historial de pedidos** (`Mi cuenta > Pedidos`): cada pedido
  tiene un boton ("Comunicar incidencia") que abre el formulario con
  `?id_order=X`. En ese caso el campo de referencia no se muestra: se usa la
  referencia del pedido directamente (verificada server-side contra
  `id_customer` en cada carga y de nuevo al enviar).
- **Acceso directo** (sin `id_order`, p. ej. desde el enlace publico): se
  muestra el campo de referencia de toda la vida, pero al enviar se valida
  contra la tabla `orders`:
  1. Si la referencia no corresponde a ningun pedido → error "la referencia
     no corresponde a ningun pedido".
  2. Si corresponde a un pedido pero de otro cliente → error "inicia sesion
     con la cuenta que hizo ese pedido".
  Solo si pasa las dos comprobaciones se envia el correo. Esto es una
  validacion **adicional** a la del formato de 9 letras del PDF (que sigue
  siendo informativa/no bloqueante, ver mas abajo): aqui se bloquea el envio
  de verdad si la referencia no es un pedido real del cliente logueado.

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

## Esquema de base de datos

Las tablas se crean desde `sql/install.sql` (y se borran desde
`sql/uninstall.sql`) mediante `installDb()`/`uninstallDb()` en
`ccincidencias.php`, que cargan el fichero, sustituyen el placeholder
`PREFIX_` por el prefijo real de la tienda y ejecutan cada sentencia. Mismo
patron que usa el modulo `inspiration` de este proyecto. Para cambiar el
esquema, edita el `.sql`, no el PHP.

## Tipos de incidencia (CRUD, sin tocar codigo)

Admin > Clientes > **Tipos de incidencia** (`AdminCcIncidenciasTipos`, tambien
enlazado desde la configuracion del modulo). Cada tipo tiene:

- **Codigo**: el valor EXACTO que viaja en el correo como `tipo:` (se guarda
  siempre en mayusculas). Si cambias el codigo de un tipo que el sistema de
  incidencias ya reconoce, avisa antes al responsable de ese sistema — es la
  misma regla del apartado 11 del PDF, ahora aplicada a datos en vez de a
  codigo.
- **Descripcion**: traducible, una por idioma de la tienda — es lo que ve el
  cliente en el desplegable del formulario.
- **Email destino**: a donde se envia el correo de las incidencias de *ese*
  tipo. Vacio = usa el email general del modulo (`CCINCIDENCIAS_TO_EMAIL`).
  Esto es lo que permite enrutar, p. ej., las incidencias de "El pedido no ha
  llegado" a logistica y las de "Material equivocado" a otro buzon.
- **Posicion** / **Activo**: orden y visibilidad en el formulario publico.

En la instalacion se siembran los 6 tipos originales del PDF (`ROTURA`,
`FALTA MAT`, `MAT ERRONEO`, `PERDIDO?`, `TTE`, `SIN CLASIFICAR`), todos con el
email general por defecto. A partir de ahi el equipo web puede crear, editar,
reordenar, activar/desactivar o borrar tipos libremente desde ese listado.

## Formato del correo

Ver `ccincidencias.php` (metodo `buildDataBlock` en
`controllers/front/form.php`) y `Formulario_CC_v2.pdf` apartados 3 a 6.
Resumen:

- Asunto exacto: `[TICKET] {REFERENCIA} - {TIPO}`.
- Remitente fijo, destinatario `incidencias@ceramicconnection.es`,
  `Reply-To` = email del cliente.
- Cuerpo `text/plain`: bloque `---DATOS-TICKET-INICIO---` ... `---DATOS-TICKET-FIN---`
  sin indentar, seguido del texto legible para personas.
- `version: 2` siempre (desde 07-09-2026). **Cualquier cambio de formato
  exige acordar antes una nueva version con el responsable del sistema de
  incidencias** (no tocar `BLOCK_VERSION`, las claves del bloque, ni el
  email remitente sin avisar).
- Fotos como adjuntos reales del correo (nunca enlaces). Si superan 20 MB en
  total se descartan y se avisa al cliente en pantalla, pero el correo se
  envia igual.
- Se envia siempre, aunque la referencia no haya validado
  (`referencia_valida: no`).

### Claves nuevas de la v2 (van justo antes de `comentario`)

Sacadas del pedido resuelto (`buildOrderDetails()` en
`controllers/front/form.php`), nunca inventadas: si no hay pedido que
consultar (referencia invalida o sin pedido asociado), las 6 se mandan
vacias (excepto `lineas`, que en ese caso tambien va vacia, no `[]`: `[]`
es solo para "hay pedido pero sin lineas").

| Clave | Como se calcula |
|---|---|
| `fecha_entrega` | `AAAA-MM-DD` de la primera fecha en que el pedido paso a un estado con `delivery = 1` en `order_history`. Vacia si no ha llegado. |
| `importe_total` | `order.total_paid` (IVA y portes incluidos) formateado con `number_format(...,2,'.','')`: punto decimal, sin simbolo, sin miles. |
| `transportista` | Nombre de `Carrier` del pedido normalizado a uno de los 9 valores cerrados via `CcIncidencias::normalizeCarrierValue()`. Ver tabla de mapeo abajo. |
| `pais_entrega` | ISO de 2 letras del pais de la **direccion de entrega** del pedido (`id_address_delivery`, no facturacion). |
| `marketplace` | `si` si `order.module === 'manomanoimportpayment'` (unica via de pedidos de marketplace en la tienda hoy, modulo `manomanoorders`); `no` en cualquier otro caso. |
| `lineas` | JSON de una sola linea (`json_encode` con `JSON_UNESCAPED_UNICODE`) por cada fila de `order_detail`: `ref`, `desc`, `uds`, `precio`, `total`. |

**`uds` con decimales**: `order_detail.product_quantity` es siempre un
entero (cajas/piezas), pero muchos articulos se venden por m². `uds` se
recupera como `total_price_tax_incl / unit_price_tax_incl` (redondeado a 3
decimales), que da el mismo `12.5` que el ejemplo del PDF (`177.50 / 14.20`).
Si el precio unitario es 0 (articulo gratis), cae a `product_quantity`.

**Mapeo de `transportista`** (`CcIncidencias::normalizeCarrierValue()`,
case/tilde-insensitive sobre `Carrier::$name`):

| Si el nombre contiene... | Se manda |
|---|---|
| "recogi" + "almac" | `Recogido en almacén` |
| "correos" | `Correos` |
| "transaher" | `Transaher` |
| "seur" | `SEUR` |
| "dsv" | `DSV` |
| "xpo" | `XPO` |
| "ucx" | `UCX` |
| (nada de lo anterior) | `Otro` |
| pedido sin transportista asignado | `Desconocido` |

**Ojo con esto**: varios de los transportistas activos hoy en la tienda
tienen nombres genericos de zona/tarifa (p. ej. "Envío en camión con
entrega concertada", "Paquetería postal internacional") en vez del nombre
de la marca real. Esos caen en `Otro`, que es exactamente el valor que el
PDF define para "un transportista que no está en la lista" — no es un bug,
es la opcion segura hasta que el equipo de incidencias confirme a que
marca corresponde cada uno. Si se identifica cual es cual, añadir el
nombre real de esos `Carrier` (o un alias reconocible) al mapeo de arriba
en vez de renombrar el `Carrier` en el admin (rompería otras cosas que sí
dependen de ese nombre).

## Antibot

- Honeypot (`cc_web`, campo oculto por CSS).
- Envio no instantaneo: `cc_ts` server-side, rechazo silencioso si el envio
  llega antes de `CCINCIDENCIAS_MIN_SECONDS` (3s por defecto).
- Limite de envios por IP y hora (`CCINCIDENCIAS_MAX_PER_HOUR`, 5 por
  defecto), tabla `ps_ccincidencias_log` (solo guarda IP+fecha, no datos de
  la incidencia).

## Prueba de aceptación (apartado 12 del PDF v1 + apartado 9 del PDF v2)

Antes de publicar el enlace, enviar estos correos de prueba y comprobar que
llegan a la bandeja de entrada (no a spam) de `incidencias@ceramicconnection.es`:

1. Caso completo valido, con 2 fotos y todos los campos rellenos.
2. Referencia invalida (p. ej. un numero de factura) → debe llegar con
   `referencia_valida: no`.
3. Referencia tecleada en minusculas y con un espacio en medio → debe llegar
   normalizada a 9 mayusculas y `referencia_valida: si`.
4. Campos opcionales vacios y comentario de varias lineas con acentos y
   caracteres especiales (ñ, ß, ç, ã).
5. Una foto en formato HEIC desde un iPhone.
6. **(v2)** Un pedido ya entregado, con importe y una linea → `fecha_entrega`
   debe llegar con la fecha real (`AAAA-MM-DD`) y no vacia.
7. **(v2)** Un pedido con tres lineas, una de ellas por m² (con decimales) y
   con una tilde o una comilla en el nombre del articulo → las tres lineas
   deben llegar en `lineas`, con los decimales y el nombre intactos (JSON
   valido, una sola linea fisica).
