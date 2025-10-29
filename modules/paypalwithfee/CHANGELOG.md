# Paypalwithfee

#### Registro de ramas
* v5.5.1
  * Se fuerza la dirección de envío desde la tienda
* v5.5.0
  * Añadida nueva tabla para controlar hash de los carritos
* v5.4.14
  * Eliminación de hooks deprecados por versión antigua de Prestashop
* v5.4.13
  * Solución problema validación de pedidos
* v5.4.12
  * Correción status_payment
  * Ajustes descuadres centimos
* v5.4.11
  * Añadimos comprobación de pago completado
  * Ofrecemos más información sobre los errores en la pantalla de error cuando se intenta validar un pedido
* v5.4.10
  * Eliminar upgrade duplicado
* v5.4.9
  * Solucionado problema mensaje de error con opciones del delivery en paypalwithfee.php
* v5.4.8
  * Solucionado problema con idiomas con apostrofe en configure.tpl
* v5.4.7
  * Solucionado problema descuadre céntimos
* v5.4.6 
  * Añadimos comprobaciones para verificar que el carrito no haya sufrido cambios inesperados
* v5.4.5
  * Eliminamos mensaje payment_infos.tpl en el checkout
  * Eliminamos configuración protección de pagos
  * Eliminamos protección de pagos de los templates
  * Enviamos a paypal la dirección del cliente en nuestra tienda e impedimos que la cambie desde paypal
