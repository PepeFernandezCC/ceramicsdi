-- =====================================================================
-- Purga de las reglas CCFREESAMPLE-* caducadas, vía procedimiento
-- almacenado: hace TODO el bucle de lotes dentro de MySQL, sin
-- depender de que el navegador/PHP/phpMyAdmin aguanten toda la
-- ejecución. Se sube como archivo (pestaña "Importar" de phpMyAdmin,
-- no "SQL") y se ejecuta con una única llamada.
--
-- (Ya comprobado con SHOW INDEX: id_cart_rule_1 e id_cart_rule_2 SÍ
-- están indexados por separado, así que la lentitud no era por falta
-- de índice - probablemente es contención de locks con el tráfico real
-- de la tienda sobre estas tablas. El bucle por lotes de aquí abajo
-- sigue siendo la forma correcta de evitarlo: transacciones cortas en
-- vez de una única transacción gigante.)
-- =====================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS purge_sample_cart_rules$$

CREATE PROCEDURE purge_sample_cart_rules()
BEGIN
    DECLARE v_batch_size INT DEFAULT 500;
    DECLARE v_remaining INT DEFAULT 1;

    WHILE v_remaining > 0 DO

        DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_batch;
        CREATE TEMPORARY TABLE tmp_sample_rules_batch AS
            SELECT id_cart_rule
            FROM ps_cart_rule
            WHERE code LIKE 'CCFREESAMPLE-%'
              AND date_to < NOW()
            ORDER BY id_cart_rule ASC
            LIMIT v_batch_size;

        SELECT COUNT(*) INTO v_remaining FROM tmp_sample_rules_batch;

        IF v_remaining > 0 THEN

            DELETE FROM ps_cart_cart_rule
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_carrier
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_shop
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_group
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_country
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_combination
            WHERE id_cart_rule_1 IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_combination
            WHERE id_cart_rule_2 IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule_lang
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

            DELETE FROM ps_cart_rule
            WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch);

        END IF;

        DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_batch;

    END WHILE;

END$$

DELIMITER ;

CALL purge_sample_cart_rules();

DROP PROCEDURE IF EXISTS purge_sample_cart_rules;

-- Verificación
SELECT
    COUNT(*) AS reglas_muestras_restantes,
    SUM(date_to < NOW()) AS todavia_caducadas
FROM ps_cart_rule
WHERE code LIKE 'CCFREESAMPLE-%';

SELECT COUNT(*) AS total_cart_rule_tienda FROM ps_cart_rule;
