# CC Free Sample Discount

Primera versión del módulo para PrestaShop.

## Qué hace

Detecta productos del carrito cuyo precio unitario final sea `0,01` y aplica una regla de carrito automática por el mismo importe total.

Ejemplo:

```text
Muestra A                         0,01 €
Muestra B                         0,01 €
Descuento por muestras gratis    -0,02 €
```

## Funcionamiento

- Hook principal: `actionCartSave`.
- Detección: productos con `price_wt` redondeado a 1 céntimo.
- Descuento: regla de carrito automática llamada `Descuento por muestras gratis`.
- Código interno de la regla: `CCFREESAMPLE-{id_cart}`.
- Al validar pedido, la regla se desactiva para que no quede reutilizable.

## Instalación

1. Subir el ZIP desde el back office de PrestaShop: **Módulos > Gestor de módulos > Subir un módulo**.
2. Instalar el módulo.
3. Probar en staging con productos configurados a `0,01 €`.

## Notas de prueba

Probar como mínimo:

- Carrito sin muestras: no debe aparecer descuento.
- 1 muestra: descuento de `-0,01 €`.
- 2 muestras: descuento de `-0,02 €`.
- Cambiar cantidades: el descuento debe actualizarse.
- Quitar muestras: el descuento debe desaparecer.
- Pedido validado: comprobar que Outvio/Correos recibe las líneas de producto a `0,01 €`.

## Limitación conocida

Esta primera versión asume que ningún producto real de venta cuesta `0,01 €`. Si existe esa posibilidad, conviene añadir una condición adicional por categoría, referencia, feature o fabricante.
