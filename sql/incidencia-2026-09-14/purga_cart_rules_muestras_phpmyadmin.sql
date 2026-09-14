-- =====================================================================
-- Purga de las reglas de carrito ("cart_rule") auto-generadas por el
-- módulo ccfreesamplediscount para descontar productos de muestra
-- (código CCFREESAMPLE-<id_cart>).
--
-- Contexto (ver modules/ccfreesamplediscount/ccfreesamplediscount.php):
-- cada carrito que contiene un producto de muestra (precio 0,01€) genera
-- su propia regla, de un solo uso, caduca a los 30 días - y NUNCA se
-- borra, ni al caducar ni al completarse el pedido (el propio código
-- dice "Do not delete the cart rule here, to avoid interfering with
-- order history"). Con el volumen de carritos de la tienda, esto explica
-- la mayor parte de las 12.818 reglas / 9.596 caducadas del informe de
-- incidencia del 14/09/2026, y de paso la tabla ps_cart_rule_combination
-- (11,4M filas / 914MB) que CartRule::delete() limpia en cascada.
--
-- Por qué es seguro borrarlas aunque estén ligadas a un pedido ya hecho:
-- ps_order_cart_rule guarda su propia copia (name, value, value_tax_excl)
-- para mostrar el histórico/factura - NO necesita que la fila de
-- ps_cart_rule siga existiendo. Por eso el propio módulo podía haberlas
-- borrado siempre y no lo hacía por exceso de cautela, no por necesidad
-- real (ver clases/order/OrderCartRule.php).
--
-- Esta consulta replica en SQL lo que hace CartRule::delete() del core
-- (limpia las tablas relacionadas antes de borrar la regla), pegable
-- directamente en el SQL de phpMyAdmin.
-- =====================================================================

SET @code_prefix = 'CCFREESAMPLE-';


-- ---------------------------------------------------------------------
-- PASO 0: DIAGNÓSTICO (solo lectura) - ejecuta esto primero y revisa
-- ---------------------------------------------------------------------

-- 0.1 Total de reglas de este módulo, cuántas caducadas, cuántas ligadas a un pedido real
SELECT
    COUNT(*) AS total_reglas_muestras,
    SUM(cr.date_to < NOW()) AS caducadas,
    SUM(cr.date_to >= NOW()) AS vigentes,
    COUNT(DISTINCT ocr.id_cart_rule) AS ligadas_a_pedido_real
FROM ps_cart_rule cr
LEFT JOIN ps_order_cart_rule ocr ON ocr.id_cart_rule = cr.id_cart_rule
WHERE CONVERT(cr.code USING utf8mb4) COLLATE utf8mb4_general_ci
      LIKE CONVERT(CONCAT(@code_prefix, '%') USING utf8mb4) COLLATE utf8mb4_general_ci;

-- 0.2 Cuántas reglas de carrito hay en total en la tienda (para comparar con el informe: 12.818)
SELECT COUNT(*) AS total_cart_rule_tienda FROM ps_cart_rule;

-- 0.3 Tamaño actual de la tabla de combinaciones (el otro gran consumidor, 11,4M filas / 914MB según el informe)
-- (SHOW TABLE STATUS en vez de information_schema.tables: no necesita permisos especiales)
SHOW TABLE STATUS LIKE 'ps_cart_rule_combination';


-- ---------------------------------------------------------------------
-- PASO 1: TABLA TEMPORAL con las reglas caducadas a borrar
-- (revisa el PASO 0 antes de seguir; solo se tocan las CADUCADAS, las
-- vigentes -carritos activos ahora mismo- no se tocan)
-- ---------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_to_delete;
CREATE TEMPORARY TABLE tmp_sample_rules_to_delete AS
SELECT id_cart_rule
FROM ps_cart_rule
WHERE CONVERT(code USING utf8mb4) COLLATE utf8mb4_general_ci
      LIKE CONVERT(CONCAT(@code_prefix, '%') USING utf8mb4) COLLATE utf8mb4_general_ci
  AND date_to < NOW();

SELECT COUNT(*) AS reglas_seleccionadas_para_borrar FROM tmp_sample_rules_to_delete;


-- ---------------------------------------------------------------------
-- PASO 2: BORRADO (replica CartRule::delete() del core, tabla por tabla)
-- ---------------------------------------------------------------------
START TRANSACTION;

DELETE FROM ps_cart_cart_rule
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE FROM ps_cart_rule_carrier
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE FROM ps_cart_rule_shop
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE FROM ps_cart_rule_group
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE FROM ps_cart_rule_country
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

-- Esta es la tabla grande (11,4M filas / 914MB en el informe)
DELETE FROM ps_cart_rule_combination
WHERE id_cart_rule_1 IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete)
   OR id_cart_rule_2 IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE FROM ps_cart_rule_product_rule_group
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

DELETE prg FROM ps_cart_rule_product_rule prg
WHERE NOT EXISTS (
    SELECT 1 FROM ps_cart_rule_product_rule_group g
    WHERE g.id_product_rule_group = prg.id_product_rule_group
);

DELETE prv FROM ps_cart_rule_product_rule_value prv
WHERE NOT EXISTS (
    SELECT 1 FROM ps_cart_rule_product_rule pr
    WHERE pr.id_product_rule = prv.id_product_rule
);

-- La regla en sí
DELETE FROM ps_cart_rule
WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_to_delete);

-- Traducciones (ps_cart_rule_lang) - PrestaShop la borra vía ObjectModel::delete(),
-- aquí la limpiamos explícitamente por si tu esquema la tiene como tabla aparte
DELETE FROM ps_cart_rule_lang
WHERE id_cart_rule NOT IN (SELECT id_cart_rule FROM ps_cart_rule);

COMMIT;


-- ---------------------------------------------------------------------
-- PASO 3: VERIFICACIÓN
-- ---------------------------------------------------------------------
SELECT
    COUNT(*) AS reglas_muestras_restantes,
    SUM(date_to < NOW()) AS todavia_caducadas_sin_borrar
FROM ps_cart_rule
WHERE CONVERT(code USING utf8mb4) COLLATE utf8mb4_general_ci
      LIKE CONVERT(CONCAT(@code_prefix, '%') USING utf8mb4) COLLATE utf8mb4_general_ci;

SELECT COUNT(*) AS total_cart_rule_tienda_tras_purga FROM ps_cart_rule;

SHOW TABLE STATUS LIKE 'ps_cart_rule_combination';

DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_to_delete;
