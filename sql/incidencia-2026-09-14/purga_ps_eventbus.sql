-- =====================================================================
-- Purga de la cola pendiente de ps_eventbus_incremental_sync (614.575
-- registros según el informe de incidencia del 14/09/2026) + forzado de
-- resincronización completa para los tipos afectados.
--
-- Este script es una adaptación del que trae el PROPIO módulo
-- ps_eventbus (modules/ps_eventbus/sql/tools/clean_incremental_sync.sql
-- y count_incremental_sync.sql) - no es un DELETE improvisado.
--
-- Contexto: PS Accounts está VERIFICADO y conectado en esta tienda
-- (nacho.ramos@ceramicconnection.es), así que NO se desactiva
-- ps_eventbus/ps_metrics (esa era la otra rama del informe, para cuando
-- no se usa ninguna integración). Aquí se sigue la rama "sí se usa":
-- purgar y dejar que el módulo resincronice desde cero los tipos
-- afectados en su próxima ejecución programada.
--
-- Qué hace exactamente, para cada "type" con más de @quantity_needed
-- filas pendientes (custom_product_carriers, carts, cart_products,
-- cart_rules... los que aparecen en el informe):
--   1. Marca ese tipo para resincronización completa en
--      ps_eventbus_type_sync (offset=0, full_sync_finished=0).
--   2. Borra sus filas ya acumuladas en ps_eventbus_incremental_sync
--      (quedan obsoletas: la resincronización completa las reemplaza).
--
-- IMPORTANTE ANTES DE EJECUTAR:
--   1. Cambia @db_name por el nombre real de la base de datos de
--      producción (SELECT DATABASE(); para consultarlo).
--   2. Backup de ps_eventbus_incremental_sync y ps_eventbus_type_sync
--      antes de ejecutar (son solo datos de estado de sincronización,
--      no datos de negocio, pero por si acaso).
--   3. Ejecuta primero el PASO 0 (diagnóstico, solo lectura) y revisa
--      qué "types" van a verse afectados antes de purgar.
--   4. Después de esto, la tienda tiene que volver a sincronizar desde
--      cero esos tipos con PrestaShop - es esperado, no es un error;
--      puede generar carga puntual mientras se pone al día, pero nada
--      comparable a los 9.360 millones de filas leídas del problema
--      actual.
-- =====================================================================


-- ---------------------------------------------------------------------
-- PASO 0: DIAGNÓSTICO (solo lectura) - cuántas filas pendientes hay por tipo
-- ---------------------------------------------------------------------
SET @db_name = 'NOMBRE_BASE_DE_DATOS_PRODUCCION';  -- <-- AJUSTAR (SELECT DATABASE();)
SET @quantity_needed = 1000; -- umbral: cualquier tipo con más filas pendientes que esto se purga

SET @eventbus_incremental_sync_table = (SELECT table_name
FROM information_schema.tables
WHERE table_schema = @db_name
AND table_name LIKE '%\_eventbus\_incremental\_sync');

-- Desglose completo por tipo (para ver el panorama real, sin filtrar por umbral)
SET @full_breakdown = CONCAT('
    SELECT type, COUNT(*) AS pendientes
    FROM ', @eventbus_incremental_sync_table, '
    GROUP BY type
    ORDER BY pendientes DESC;
');
PREPARE full_breakdown FROM @full_breakdown;
EXECUTE full_breakdown;
DEALLOCATE PREPARE full_breakdown;

-- Solo los tipos que van a purgarse con el umbral actual
SET @get_content_with_extra_counts = CONCAT('
    SELECT type, COUNT(*) as incr_type_count
    FROM ', @eventbus_incremental_sync_table, '
    GROUP BY type
    HAVING COUNT(*) > ', @quantity_needed, '
');
PREPARE get_content_with_extra_counts FROM @get_content_with_extra_counts;
EXECUTE get_content_with_extra_counts;
DEALLOCATE PREPARE get_content_with_extra_counts;


-- ---------------------------------------------------------------------
-- PASO 1: PURGA + forzar resincronización completa
-- (revisa el resultado del PASO 0 antes de seguir)
-- ---------------------------------------------------------------------

SET @eventbus_type_sync_table = (SELECT table_name
FROM information_schema.tables
WHERE table_schema = @db_name
AND table_name LIKE '%\_eventbus\_type\_sync');

-- 1.1 Marcar para resincronización completa los tipos con backlog
SET @enable_full_sync = CONCAT('
    UPDATE ', @eventbus_type_sync_table, '
    SET `offset` = 0, full_sync_finished = 0
    WHERE type IN (
        SELECT type FROM (
            SELECT type, COUNT(*) as incr_type_count
            FROM ', @eventbus_incremental_sync_table, '
            GROUP BY type
            HAVING COUNT(*) > ', @quantity_needed, '
        ) AS subquery
    );
');
PREPARE enable_full_sync FROM @enable_full_sync;
EXECUTE enable_full_sync;
DEALLOCATE PREPARE enable_full_sync;

-- 1.2 Borrar las filas acumuladas de esos tipos
SET @delete_query = CONCAT('
    DELETE FROM ', @eventbus_incremental_sync_table, '
    WHERE type IN (
        SELECT type FROM (
            SELECT type, COUNT(*) as incr_type_count
            FROM ', @eventbus_incremental_sync_table, '
            GROUP BY type
            HAVING COUNT(*) > ', @quantity_needed, '
        ) AS subquery
    );
');
PREPARE delete_query FROM @delete_query;
EXECUTE delete_query;
DEALLOCATE PREPARE delete_query;


-- ---------------------------------------------------------------------
-- PASO 2: VERIFICACIÓN
-- ---------------------------------------------------------------------
SET @verify = CONCAT('
    SELECT type, COUNT(*) AS pendientes_restantes
    FROM ', @eventbus_incremental_sync_table, '
    GROUP BY type
    ORDER BY pendientes_restantes DESC;
');
PREPARE verify FROM @verify;
EXECUTE verify;
DEALLOCATE PREPARE verify;

SET @verify_types = CONCAT('
    SELECT type, `offset`, full_sync_finished, last_sync_date
    FROM ', @eventbus_type_sync_table, '
    ORDER BY type;
');
PREPARE verify_types FROM @verify_types;
EXECUTE verify_types;
DEALLOCATE PREPARE verify_types;
