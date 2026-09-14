-- =====================================================================
-- Purga de carritos y direcciones temporales acumulados en la cuenta
-- ghost@ceramicconnection.es (cliente ficticio usado por
-- deliverypricecalculator para calcular gastos de envío).
--
-- Contexto: informe de incidencia de rendimiento del 14/09/2026, punto
-- 5.2.2. Desde el 11/09/2026 el módulo dejó de borrar estos carritos y
-- direcciones porque la consulta a SEUR se quedaba colgada (ver fix ya
-- aplicado en SeurLib.php + try/finally en price.php/productestimate.php).
--
-- IMPORTANTE ANTES DE EJECUTAR:
--   1. Ajusta el prefijo de tabla `ps_` si en producción es distinto
--      (buscar `_DB_PREFIX_` en config/settings.inc.php del servidor).
--   2. Haz un backup / snapshot de `ps_cart`, `ps_cart_product`,
--      `ps_cart_cart_rule`, `ps_address` antes de la purga masiva.
--   3. Ejecuta primero la sección "0. Comprobaciones" y revisa los
--      números antes de pasar a las secciones de borrado.
--   4. Las consultas de borrado NUNCA tocan carritos con pedido asociado
--      (mismo criterio que Cart::orderExists() en el core de PrestaShop)
--      ni direcciones usadas en pedidos reales (Address::isUsed()).
--   5. Ajusta la fecha '2026-09-11' si quieres purgar también los que
--      hubiera de antes (el informe dice que antes del 11/09 el
--      mecanismo de limpieza SÍ funcionaba, así que no debería haber
--      apenas carritos viejos sueltos, pero conviene comprobarlo en el
--      punto 0.3).
-- =====================================================================


-- ---------------------------------------------------------------------
-- 0. COMPROBACIONES (solo lectura, ejecutar primero)
-- ---------------------------------------------------------------------

-- 0.1 Localizar el id_customer de la cuenta ghost
SELECT id_customer, email, date_add
FROM ps_customer
WHERE email = 'ghost@ceramicconnection.es';

-- Sustituye @GHOST_ID por el id_customer obtenido arriba en todo lo que sigue
-- (o define la variable de sesión, ver 0.2).
SET @GHOST_ID = (SELECT id_customer FROM ps_customer WHERE email = 'ghost@ceramicconnection.es' LIMIT 1);

-- 0.2 Cuántos carritos hay realmente que purgar, y cuántos tienen pedido asociado (no deben tocarse)
SELECT
    COUNT(*) AS total_carritos_ghost,
    SUM(c.date_add >= '2026-09-11') AS carritos_desde_incidencia,
    SUM(o.id_cart IS NOT NULL) AS carritos_con_pedido_NO_TOCAR
FROM ps_cart c
LEFT JOIN ps_orders o ON o.id_cart = c.id_cart
WHERE c.id_customer = @GHOST_ID;

-- 0.3 Cuántas direcciones hay que purgar, y cuántas están usadas en pedidos reales (no deben tocarse)
SELECT
    COUNT(*) AS total_direcciones_ghost,
    SUM(a.date_add >= '2026-09-11') AS direcciones_desde_incidencia,
    SUM(o.id_order IS NOT NULL) AS direcciones_con_pedido_NO_TOCAR
FROM ps_address a
LEFT JOIN ps_orders o ON o.id_address_delivery = a.id_address OR o.id_address_invoice = a.id_address
WHERE a.id_customer = @GHOST_ID
  AND a.deleted = 0;


-- ---------------------------------------------------------------------
-- 1. TABLA TEMPORAL con los carritos a borrar (auditable / reutilizable)
-- ---------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_ghost_carts_to_delete;
CREATE TEMPORARY TABLE tmp_ghost_carts_to_delete AS
SELECT c.id_cart
FROM ps_cart c
WHERE c.id_customer = @GHOST_ID
  AND c.date_add >= '2026-09-11'
  AND NOT EXISTS (
      SELECT 1 FROM ps_orders o WHERE o.id_cart = c.id_cart
  );

SELECT COUNT(*) AS carritos_seleccionados_para_borrar FROM tmp_ghost_carts_to_delete;


-- ---------------------------------------------------------------------
-- 2. BORRADO DE CARRITOS (replica Cart::delete() del core)
-- ---------------------------------------------------------------------
START TRANSACTION;

-- 2.1 Datos de personalización de producto (normalmente ninguno en estos carritos, por seguridad se incluye)
DELETE cd FROM ps_customized_data cd
INNER JOIN ps_customization c ON c.id_customization = cd.id_customization
WHERE c.id_cart IN (SELECT id_cart FROM tmp_ghost_carts_to_delete);

DELETE FROM ps_customization
WHERE id_cart IN (SELECT id_cart FROM tmp_ghost_carts_to_delete);

-- 2.2 Reglas de carrito aplicadas
DELETE FROM ps_cart_cart_rule
WHERE id_cart IN (SELECT id_cart FROM tmp_ghost_carts_to_delete);

-- 2.3 Líneas de producto del carrito
DELETE FROM ps_cart_product
WHERE id_cart IN (SELECT id_cart FROM tmp_ghost_carts_to_delete);

-- 2.4 El carrito en sí
DELETE FROM ps_cart
WHERE id_cart IN (SELECT id_cart FROM tmp_ghost_carts_to_delete);

COMMIT;

-- Verificación
SELECT COUNT(*) AS carritos_ghost_restantes
FROM ps_cart
WHERE id_customer = @GHOST_ID AND date_add >= '2026-09-11';


-- ---------------------------------------------------------------------
-- 3. TABLA TEMPORAL con las direcciones a borrar
-- ---------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_ghost_addresses_to_delete;
CREATE TEMPORARY TABLE tmp_ghost_addresses_to_delete AS
SELECT a.id_address
FROM ps_address a
WHERE a.id_customer = @GHOST_ID
  AND a.deleted = 0
  AND a.date_add >= '2026-09-11'
  AND NOT EXISTS (
      SELECT 1 FROM ps_orders o
      WHERE o.id_address_delivery = a.id_address OR o.id_address_invoice = a.id_address
  );

SELECT COUNT(*) AS direcciones_seleccionadas_para_borrar FROM tmp_ghost_addresses_to_delete;


-- ---------------------------------------------------------------------
-- 4. BORRADO DE DIRECCIONES (replica Address::delete() del core)
-- ---------------------------------------------------------------------
START TRANSACTION;

-- 4.1 Desvincular de cualquier carrito que aún pudiera referenciarlas
--     (por si queda algún carrito fuera del rango purgado en el paso 2)
UPDATE ps_cart
SET id_address_delivery = 0
WHERE id_address_delivery IN (SELECT id_address FROM tmp_ghost_addresses_to_delete);

UPDATE ps_cart
SET id_address_invoice = 0
WHERE id_address_invoice IN (SELECT id_address FROM tmp_ghost_addresses_to_delete);

-- 4.2 Borrado real de las direcciones
DELETE FROM ps_address
WHERE id_address IN (SELECT id_address FROM tmp_ghost_addresses_to_delete);

COMMIT;

-- Verificación
SELECT COUNT(*) AS direcciones_ghost_restantes
FROM ps_address
WHERE id_customer = @GHOST_ID AND deleted = 0 AND date_add >= '2026-09-11';


-- ---------------------------------------------------------------------
-- 5. Limpieza de las tablas temporales de trabajo
-- ---------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_ghost_carts_to_delete;
DROP TEMPORARY TABLE IF EXISTS tmp_ghost_addresses_to_delete;
