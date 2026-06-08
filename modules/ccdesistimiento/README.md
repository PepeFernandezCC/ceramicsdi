# ccdesistimiento

Modulo Prestashop 1.7/8.x para Ceramic Connection.

## Que hace

- Anade boton "Solicitar desistimiento" en el detalle del pedido del cliente.
- Muestra formulario con productos del pedido.
- Registra la solicitud en la tabla `ps_cc_desistimiento`.
- Anade nota privada al pedido.
- Envia email de acuse al cliente y aviso interno a Ceramic Connection.

## Configuracion

En Modulos > ccdesistimiento:

- Plazo en dias: 14 por defecto.
- Email interno: info@ceramicconnection.es.
- Telefono/WhatsApp.
- Direccion de devolucion.
- IDs de estados considerados "Entregado". Por defecto 5.
- IDs de categorias excluidas, separados por coma, para productos a medida o personalizados.

## Importante

El modulo no aprueba automaticamente devoluciones ni genera reembolsos. Solo registra la solicitud de desistimiento.

Antes de usar en produccion, probar en staging porque los hooks y estados pueden variar segun tema, version y configuracion de Prestashop.
