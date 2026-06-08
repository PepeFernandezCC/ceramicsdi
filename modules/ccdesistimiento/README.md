# ccdesistimiento

Modulo Prestashop 1.7/8.x para Ceramic Connection.

## Version 1.1.0

Incluye:

- Boton `Solicitar desistimiento` en el detalle del pedido.
- Boton `Solicitar desistimiento` tambien en `Mi cuenta > Historial de pedidos`, junto a `Detalles`, para pedidos dentro de plazo.
- Formulario de confirmacion con productos afectados.
- Registro interno en tabla `cc_desistimiento`.
- Nota privada en el pedido.
- Email automatico al cliente y aviso interno.
- Configuracion de plazo, email, telefono, direccion de devolucion, estados considerados `Entregado` y categorias excluidas.

## Importante

El boton del historial se inyecta desde el hook `displayHeader` mediante JavaScript, porque la plantilla habitual de historial de pedidos no incluye un hook propio en la columna de acciones. De esta forma no es necesario editar el fichero del tema.

Si el modulo ya estaba instalado, actualizar a la version 1.1.0 o reinstalar/restablecer para registrar el hook `displayHeader`.

Probar siempre en staging antes de produccion.
