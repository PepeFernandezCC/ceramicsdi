# Correos Ecommerce para Prestashop

### **[1.7.0]** - 14/01/2025

##### Mejoras
- Copia del teléfono del destinatario a <NumeroSMS> según formato y destino
- Eliminación del bloque para agregar datos de contrato, ahora visible solo al pulsar "Nuevo contrato" o "Editar" 
- Traslado del campo "empresa" al pre-registro: en Correos, al bloque Destinatario; en CEX, concatenado con el nombre del destinatario 
- Comprobación de que la extensión Soap está instalada al añadir las credenciales de Correos 
- Robustecimiento de la instalación. Resueltos algunos problemas en multitienda

##### Correcciones
- Se corrige iban enviado a PS2C. Se enviaban espacios. Reportado en INC000052969586 
- Se corrige que no se informen datos de oficina cuando se ha grabado un envío de oficina, pero luego se escoge un domicilio. Reportado en INC000052969586 
- Tomaba la compañía equivocada, e informaba el enlace de CEX cuando se hacía un pedido con Correos (versiones 1.7.6.x). Reportado en INC000052980897
- No funciona botón Modificar Transportista en pedido  (versiones 1.7.6.x). Reportado en INC000052980897
- Ajustes->Zonas y transportistas no tenía en cuenta si en estaba activo en Ajustes->Productos. Reportado en INC000052976431

### **[1.6.2]** - 03/10/2024

##### Mejoras
- Se añade animación "en proceso" cuando se apretan los botones de imprimir hasta que se termina de descargar el documento pdf
- Se añade control a la hora de añadir un código de usuario ya existente en la base de datos
- Se añade botón para eliminar el archivo a subir a la base de datos antes de guardar los ajustes de usuario
- Se añade botón para copiar al portapapeles los datos guardados en el buscador de Oficina/CityPaq dentro de un pedido
- Se añade confirmación para cancelación de pedidos de Oficina/CityPaq dentro de un pedido

##### Correcciones
- Corregido mensaje de error en el apartado devoluciones del pedido no aparece
- Corregido checkbox desaparecia al cancelar un envio dentro de un pedido
- Corregido mensaje de error proveniente de webservice no aparece cuando se cancelaba un pedido
- Corregido subida de logo personalizado distinto al guardado en la base de datos
- Corregido búsqueda y generación de envío mediante Oficina/CityPaq desde dentro de un pedido
- Corregido preregistro con más de un bulto. Reportado en INC000052969508
- Corregido productos no aparecen bien Ajustes->Zonas y Transportistas. Algunos Prestashop informan el código ISO en minúsculas. Reportado INC000052976431
- Corregido optimización de consultas en Utilidades. Reportado en INC000052971097

### **[1.6.1]** - 05/08/2024

##### Correcciones
- Se corrige un problema en Utilidades relacionado con los controladores. Reportado en INC000052895923

### **[1.6.0]** - 01/08/2024

##### Mejoras
- Se habilita la posibilidad del funcionamiento del módulo en Multitienda

### **[1.5.5]** - 15/07/2024

##### Correcciones
- Se corrige que no se redondeen los pesos en el manifiesto. Reportado en INC000052858711
- Se corrige limpieza de números de teléfono móviles durante el preregistro. Reportado en INC000052856496 
- Se corrige el envío del valor neto decimales, y descripción adunera. Reportado en INC000052857881
- Se corrige la aparición de código AT en Pedido, según compañía de transporte. Reportado en INC000052809381
- Se corrige fallo en Utilidades->Gestión Masiva, imprimir etiqueta. Reportado en INC000052864382
- Se corrige fallo de no aparece Oficina/CityPaq en proceso de compra. Reportado en INC000052857281
- Se corrige fallo de resultados en resumen de pedidos devuelve fechas mayores a la indicada INC000052876184
- Se corrige conflicto con el módulo de MRW. Reportado en INC000052874524
- Se corrige fallo en cancelación de recogidas. No se cancelaban en OV2. Reportado en INC000052876600

### **[1.5.4]** - 25/06/2024

##### Mejoras
-   Mejora la obtención del Manifiesto en Utilidades -> Resumen de Pedidos (Filtrado, Formato y Datatable)
-   Se cambia el orden de los estados de CRON en Ajustes

##### Correcciones
- Se corrige fallo en Utilidades->Gestión Masiva->Buscar. Columna ambigua en bbbdd Mariadb. Reportado en INC000052818597
- Se corrige obtener el menú padre del submenú SELL. Reportado en INC000052830872 y INC000052837752
- Se corrige conflicto con clase Utils. Reportado en INC000052829728

### **[1.5.31]** - 24/06/2024
- Se corrige problema de impresión en etiquetas pedido. Reportado en INC000052839971, INC000052827845

##### Correcciones

- Se corrige la impresión de etiquetas de envío en Pedido

### **[1.5.3]** - 07/06/2024

##### Mejoras
- Se mejoran los filtros de código postal para encontrar CityPaqs

##### Correcciones

- Se corrige el mostrar/ocultar columnas en Utilidades->Gestión Masiva de Productos
- Se corrige limpieza incorrecta de teléfonos
- Se corrige error al parsear json que venía con código HTML, debido a errores previos de programación en PHP
- Se corrige malfuncionamiento de función de transformación de centímetros a metros
- Se corrige que informe de los códigos de envío en recogida de devolución. Reportado en INC000052784579
- Se corrige que reporte un error si un usuario no está dado de alta en el servicio de recogidas de Correos. Reportado en INC00005287844
- Se corrige el mostrar países en Pedido->Destinatario
- Se añade literal "Tiempo de espera agotado". Parte de WO0000050523028 y WO0000050523133
- Se corrige puerto para instalaciones tipo Docker en enlaces al pedido Utilidades->Gestión Masiva

### **[1.5.2]** - 20/05/2024

##### Mejoras
- Se añade multicliente a ctrlvers

##### Correcciones

- Se corrige problema guardado de transportistas en Ajustes->Zonas y Transportistas. Reportado en INC000052781123
- Se corrige uso y guardado de logotipo personalizado para pedidos CEX
- Se corrige los codEnvios no informados en ListaCodEnvios en recogidas y por tanto no se informaban en OV2 (Oficina Virtual). Reportado en INC000052784579
- Se corrige error al imprimir etiquetas formato 3 etiquetas(CEX) con pedidos de más de un bulto.
- Se corrige error al cambiar modalidad de transportista en pedido. No aparecían bloques de Documentación Aduanera ni Oficina/CityPaq
- Se corrige error en Utilidades->Recogidas que solo aparezcan recogidas de Correos

### **[1.5.1]** - 07/05/2024

##### Correcciones

- Se corrige problema de error de Token o desvío a la página de inicio cuando se genera un preregistro. Reportado en INC000052758979 y INC000052771185
- Se revisan los estados en los que es posible cancelar un preregistro
- Se informa valor de contrareembolso sin importe en pedido. Reportado en INC000052763867                                           
- Se permite trabajar pedidos sin transportista de Correos. Reportado en INC000052758953           
- Traducción de texto en método de pago Reembolso. Reportado en INC000052758948
- Filtros y Ordenación. Reportado en INC000052688025

### **[1.5.0]** - 22/04/2024

##### Mejoras
-   Se añaden dimensiones por defecto para paq ligero y cityPaq
-   Se añade la selección de un método de pago como COD

##### Correcciones

-  Se corrige el problema con el logo a la hora de generar etiqueta

### **[1.4.0]** - 15/03/2024

##### Mejoras
-   Se añade funcionalidad multicliente.

##### Correcciones
- Se corrige problema en devolución. Reportado en INC000052646278
- Se corrige problema de no aparición de transportistas en zonas y transportistas. Reportado en INC000052657613
- Se corrige problema de descarga de logs en Ajustes->Configuración de Usurio->Cron. Reportado en INC000052670149
- Se corrige conflicto con una clase existente. Reportado en INC000052641735
- Se corrige guardado en Ajustes->Zonas y transportistas. Repotado en INC000052681574

### **[1.3.1]** - 07/02/2024

##### Mejoras
-   Se obtienen las etiquetas a través de servicio web PS2C en lugar de las tablas

##### Correcciones
-   Corregido problema de oficina/citypaq según el theme instalado por el cliente (baseDir mal informado por el Theme). Reportado en INC000052614636
-   Corregido problema de preregistrar/cancelar con transportistas antiguos. Reportado en INC000052616774, INC000052617486
-   Corregido problema de visualización de pedidos internacionales para versiones 1.7.6.x Reportado en INC000052626253
-   Corregido error al introducir un numero de telefono con espacios. Reportado en INC000052630044
-   Corregido el estado del checkbox "checked" o "unchecked" al tratarse de un pedido CEX/Correos. Reportado en INC000052614185

### **[1.3.0]** - 09/01/2024

##### Mejoras
-   Se implementa control de versiones y sistemas de avisos

##### Correcciones
-   Corregido problema de pérdida de historial en pedidos. Reportado en INC000052577832

### **[1.2.9]** - 02/01/2024

##### Correcciones
-   No refleja transporistas ajenos al módulo en Utilidades. Reportado en INC000052518349 
-   Se corrige warning al no encontrar ficheros de secret y tener mal puesta la contraseña. Reportado en INC000052520481
-   No informaba el codigo postal en Oficina/CityPaq en checkout en ciertos temas. Reportado en INC000052564877

### **[1.2.8.0]** - 14/11/2023

##### Mejoras

-   Se permite la generación de recogidas, directamente desde la página del pedido mediante check
-   Se elimina la opción en CEX de usar el endpoint de grabación de recogidas, unicamente se podrán hacer mediante el check de generar recogida al crear un preregistro
-   En la pestaña de Recogidas, unicamente aparecerán las de Correos
-   Se elimina la posibilidad de envios con Paq International Light a paises no incluidos en este servicio, además se incluyen a los existentes, Estados Unidos y Kazajistán

##### Correcciones

-   Se impide que se pueda generar un preregistro con el el prefijo telefónico de España en las siguientes versiones, +34 y 0034

---

### **[1.2.7.0]** - 30/10/2023

##### Mejoras

-   Nueva columna que muestra los articulos en cada envío de la pestaña utilidades
-   Permite filtrar envios según el nombre del artículo.

### **[1.2.6.0]** - 02/10/2023

##### Mejoras

-   Se añaden las traducciones en Portugués

##### Correcciones

-   Se han corregidos problemas de compatibilidad con el módulo OnePageCheckout. Reportado en INC000052432317. Es necesario habilitar desde el módulo OnePageCheckout la compatibilidad con módulos de envío

---

### **[1.2.5.0]** - 02/10/2023

##### Mejoras

-   Se añade nuevo producto para recogida en oficina para CEX. 

##### Correcciones

-   Se guardan los datos y el cod.postal correctamente tras cambiar de transportista en el menu checkout
-   Se ha solucionado un error que ocasionaba datos erroneos en los valores del selector
-   Se ha ajustado el mínimo de carácteres necesarios de 8 a 7 para clientes antiguos. Reportado en INC000052418340

---

### **[1.2.4.1]** - 29/09/2023

##### Correcciones

-   Se soluciona error en el que se quitaban los 0 de codigo postal en el checkout. Reportado en INC000052422620

---

### **[1.2.4.0]** - 11/09/2023

##### Mejoras

-   Nuevos nombres de campos en Datos de cliente. Descripción dentro de cada campo
-   Apellido opcional en pedido y utlilidades
-   Validaciones en Datos de clientes en Ajustes y mensajes de error

##### Correcciones

-   Se añade carácteres necesarios para interpretar los datos del formulario del Destinatario en la pestaña de envío. Reportado en INC000052393819
-   Se corrige un error que afectaba a la carga de la pestaña de la Doc. Aduanera. Reportado en INC000052402350

---

### **[1.2.3.0]** - 08/08/2023

##### Mejoras

-   Se informa el código postal en el buscador en el proceso de checkout cuando se elige transportista Oficina o CityPaq
- 	Tooltips en datos de de clientes en Ajustes

---

### **[1.2.2.1]** - 01/08/2023

##### Mejoras

-   Se cambian los literales para imprimir etiquetas a Papel 4 etiquetas y Papel 3 etiquetas (Solo CEX)

##### Correcciones

-   Problema con contraseña en Prestashop con contrabarra. Reportado en INC000052321224
-   Corregido error al preregistrar ePaq24

---

### **[1.2.2.0]** - 18/07/2023

##### Mejoras

-   Mejoras PR00015515, inclusión de impresión de etiquetas formato 3/A4

##### Correcciones

-   Corregido error al recuperar el estado de las devoluciones con Correos, producido por acceso evento de un objeto incorrecto. Reportado en INC000052281907

---

### **[1.2.1.0]** - 06/06/2023

##### Mejoras

-   Implementado tracking number en el detalle de Transportistas de los pedidos y detalles de pedido en parrilla de pedidos
-   Quitada validación en campo IBAN en la vista de Configuración de usuario y Pedido
-   El peso por defecto (Kg) en Configuración de usuario ahora admite valores con saltos de 100 gramos
-   Quitada obligatoriedad del campo CÓDIGO AT
-   Identificación canal pre-registro para envíos desde Pedido y Utilidades (bulto y multibulto) y devoluciones desde Pedido

##### Correcciones

-   Se prefija variable de prestashop a co_ps_base_uri ya que había colisión de nombres con otro módulo. Reportado en INC000052231342
-   Corregida posición(de horizontal a vertical) de etiqueta térmica para los productos Paq Estándar Internacional y Paq Premium Internacional. Corrige INC000052242554
-   Se escapan caracteres de contraseña antes de enviar el webservice. Solución a INC000052249162
-   Corregido problema de carga de assets en pedido producido por un bug de Prestashop en la versión 1.7.6.2. Corrige INC000052237929
-   Corregidas rutas de descarga etiqueta y customer datatable
-   Corregida descarga de etiqueta tras preregistrar un envío desde Utilidades - Gestión Masiva. Corrige INC000052257645
-   Corregido problema de caracteres en la dirección del destinatario a la hora de preregistrar. Reportado en INC000052272137

---

### **[1.2.0.7]** - 22/05/2023

##### Mejoras

-   Ejecución del CRON se hace mediante ajax
-   Se configura un log de errores del CRON
-   Se evita reintentos si el CRON no ha ido correctamente

##### Correcciones

-   Se corrige el problema de la incidencia INC000052084857 Excepción 15500 $order->id_address_delivery está vacía si se han borrado pedidos de la base de datos
-   Controlamos si no está definida la api de google en configuración para impedir el error de google is not defined en el checkout. Corrige INC000052095063
-   Limitado a 1 el número de registros devueltos en la consulta para recuperar los pedidos en Gestión Masiva de Envíos. Corrige INC000052097884
-   Corregido problema de carga de estilos en pedidos para versiones de Prestashop inferiores a 1.7.7.0. Corrige INC000052112316
-   Se corrige error al en la hora de recogida del remitente, en algunos casos no se llegaba a guardar y ponía la hora por defecto 00:00:00
-   Corregido problema quese producía al preregistrar en algunos casos. Solución a INC000052153207

---

### **[1.2.0.6]** - 17/02/2023

##### Mejoras

-   Ampliada descripción del módulo

---

### **[1.2.0.5]** - 08/02/2023

##### Mejoras

-   Mejorada gestión en descarga de etiquetas
-   Se corrige problema en Zonas y Transportistas que eliminaba producto cuando se asignaba a un transportista existente. Problema reportado en INC000052026425

---

### **[1.2.0.4]**

##### Correcciones

-   Solucionado problema al obtener la zona de envío, al haber zonas con mezcla de rangos de cp y zonas con provincias
-   Se corrige problema con puerto por defecto de la base de datos
-   Timeout a las llamadas ajax para borrar docuemntos de aduanas una vez descargados, ya que se estaban eliminando antes de que el navegador los descargara, soluciona INC000052022061
-   Insertados por defecto estados propios del módulo para configurar correctamente el mostrar progreso del estado del envío en la tienda
-   Se actualizan estados de pedidos realizados con versiones anteriores a 1.2.0.0 tras el cambio de nombres donde se añade coletilla "Correos - CEX" y se quitan deletes de estados que no se usan ya que causaban error si existían pedidos y se eliminaban estos estados

---

### **[1.2.0.3]** - 10/01/2023

##### Correcciones

-   Se corrige error al guardar productos cuando la tienda tenía configuradas las zonas de envío con códigos postales
-   Se corrige error al guardar remitente
-   Se reduce tamaño de los logos de los carriers, ya que se veían muy grandes en resumen de pedido(checkout)
-   Controlamos si en checkout se informa teléfono o teléfono móvil para posteriormente mostrarlo destinatario dentro de pedido

---

### **[1.2.0.2]** - 02/01/2023

##### Mejoras

-   Se recoge el campo dirección complementaria
-   Arreglo de Mensaje de advertencia al comprador en Ajustes->Tramitación Aduanera de Envíos
-   Mensaje de advertencia al comprador en checkout envíos internacionales
-   Corregida la hora de envío de CEX en histórico de Pedidos
-   Dirección complementaria se duplicaba en Utilidades
-   Se eliminan espacios antes y después de los campos en Ajustes->Remitentes

##### Correcciones

-   Se soluciona el error this is incompatible with sql_mode='only_full_group_by' al prerregistrar en Utilidades.
-   Prefijado estilos CSS en checkout

---

### **[1.2.0.1]** - 19/12/2022

##### Mejoras

-   Arreglos en etiquetas de devolución de Correos
-   Se envía nº de pedido en el campo Referencia en la devolución
-   Se recoge el campo dirección complementaria

---

### **[1.2.0.0]** - 16/12/2022

##### Mejoras

-   Se permiten hasta cinco descripciones aduaneras por envío en el pedido
-   Se modifica el bloque Remitentes en pedido simplificándolo
-   Se cambian nuevos logos
-   Añadida funcionalidad del CRON en Configuración de Usuario
-   Se añade la descripción del módulo, plataforma y sus versiones en el campo "refCliente" de CEX
-   En el campo "Dirección" en "Pedido" se concatena la dirección complementaria y se envía al preregistro
-   Se añade el número de pedido delante de la referencia del pedido en el campo "Referencia" de "Pedido" y en el preregistro, en el campo "ReferenciaCliente"
-   Redefinido bloque devoluciones

##### Correcciones

-   Se eliminan productos Islas Documentación y Paquetería Óptica de Correos Express cuando la zona es Portugal

---

### **[1.1.0.0]** - 28/10/2022

##### Mejoras

-   Cambio de nombre de los productos (Mayusculas-Minúsculas)
-   Cambios en el campo "referenciaCliente3"
-   Mostrar y ocultar campos en las tablas de "Utilidades"
-   Bloque devoluciones siempre visible - ya no depende del estado del envío. Cambio de lógica con selector de producto de devolución: PaqRetorno / Paq24
-   Añadidad compatibilidad con los módulos de pago contrareembolso "Codfee" y "Megareembolso"
-   Añadidad compatibilidad con el módulo "One Page Checkout PS"
-   Incluidas nuevas descripciones aduaneras
-   Modificación bloque "Datos de remitente" en "Pedido" (Enlace para editar remitente desde pedido)
-   Cambio de logos
-   Reglas de filtrado de producto en “Zonas y transportistas” para evitar errores de configuración
-   Tooltips en los campos que se pide rellenar al usuario
-   Hipervínculo a pedido desde utilidades
-   Permitir el valor “En blanco” o nulo en la asociación de productos a transportistas
-   Mostrar mensaje cuando el webservice no responda o lo haga tarde

##### Correcciones

-   Cambiamos "Descripción aduanera por defecto" por "Descripción aduanera" en pedido ->preregistro/devolución
-   Se añaden ":" en las horas de los estados del envío CEX
-   Actualización de Zonas/Transportistas en base a activación/desactivación de productos
-   Corregido error estados CEX hora datatable
-   Refresco de productos al conectar usuarios
-   Selected Citypaq/office
-   Arreglado error en checkout cuando seleccionabas carrier oficina/citypaq y continuabas el proceso de compra sin elegir oficina/citypaq
-   Ordenación de estados del envío por fecha y hora
-   Errores CSS y JS arrastrados de WC
-   Corregido error que hacía que nos se cambiara la dirección al elegir un CityPaq en el proceso de checkout
-   En Devoluciones de Pedido, al clonar paquetes se hace referencia a Devolución del paquete y no a Devolución del bulto. Además se hace referencia a Envíos en vez de a Bultos dentro del apartado "Códigos de devolución" una vez generada la devolución
-   Hacer opcional el campo "Iban"
-   Igualar nº de campos en los bloques de Remitente y Destinatario (Pedido)

---

### **[1.0.2.4]** - 20/10/2022

##### Correcciones

-   Corregida modalidad de entrega para envíos con Paq Estandar Oficina Elegida desde utilidades

---

### **[1.0.2.3]** - 17/10/2022

##### Correcciones

-   Fallo en selección de oficina/citypaq en checkout

---

### **[1.0.2.2]** - 27/09/2022

##### Mejoras

-   Correcciones de error modalidad de envíos

---

### **[1.0.2.1]** - 30/08/2022

##### Mejoras

-   Correcciones de error 500 en checkout
-   Error CP en el xml envíos internacionales

---

### **[1.0.2.0]** - 22/08/2022

##### Mejoras

-   Correcciones de errores de llamadas a funciones no propias para Prestashop

---

### **[1.0.1.0]** - 18/07/2022

##### Mejoras

-   Evolutivo de Arancelarios

---

### **[1.0.0.0]** - 07/07/2022

##### Mejoras

-   Entrega inicial de Release
