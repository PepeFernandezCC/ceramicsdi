-- Solo lectura. Diagnóstico de reglas de carrito generadas por
-- ets_abandonedcart (tabla de enlace: ps_ets_abancart_discount.id_cart_rule)

-- 1. Cuántas reglas de carrito ha generado este módulo en total,
--    cuántas caducadas, cuántas vigentes.
SELECT
    COUNT(DISTINCT cr.id_cart_rule) AS total_reglas_ets_abancart,
    SUM(cr.date_to < NOW()) AS caducadas,
    SUM(cr.date_to >= NOW()) AS vigentes
FROM ps_cart_rule cr
INNER JOIN ps_ets_abancart_discount d ON d.id_cart_rule = cr.id_cart_rule;

-- 2. De las caducadas, cuántas NO están ligadas a ningún carrito activo
--    ni a ningún pedido real (candidatas seguras a borrar).
SELECT COUNT(DISTINCT cr.id_cart_rule) AS caducadas_huerfanas
FROM ps_cart_rule cr
INNER JOIN ps_ets_abancart_discount d ON d.id_cart_rule = cr.id_cart_rule
LEFT JOIN ps_cart_cart_rule ccr ON ccr.id_cart_rule = cr.id_cart_rule
LEFT JOIN ps_orders o ON o.id_cart = ccr.id_cart
WHERE cr.date_to < NOW()
  AND ccr.id_cart IS NULL;

-- 3. Volumen general de las tablas de carrito (para tener la foto
--    completa, no solo lo generado por este módulo).
SELECT COUNT(*) AS total_carts FROM ps_cart;
SELECT COUNT(*) AS total_cart_product FROM ps_cart_product;
SELECT COUNT(*) AS total_ets_abancart_discount FROM ps_ets_abancart_discount;
SELECT COUNT(*) AS total_ets_abancart_tracking FROM ps_ets_abancart_tracking;
