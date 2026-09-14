-- =====================================================================
-- Purga de ps_eventbus_incremental_sync - versión lista para pegar
-- directamente en el SQL de phpMyAdmin y ejecutar (sin editar nada:
-- usa la base de datos que ya tengas seleccionada en phpMyAdmin).
--
-- Recomendación: pega y ejecuta primero SOLO el PASO 0 (selecciona esas
-- líneas y dale a "Ejecutar" con la opción de phpMyAdmin, o simplemente
-- revisa el resultado antes de lanzar el resto). Si los números
-- cuadran con el informe, pega y ejecuta el resto (PASO 1 y PASO 2).
--
-- Qué hace (para cada "type" con más de @quantity_needed filas
-- pendientes en ps_eventbus_incremental_sync):
--   1. Lo marca para resincronización completa en ps_eventbus_type_sync.
--   2. Borra sus filas ya acumuladas (quedan obsoletas, las reemplaza
--      la resincronización completa).
-- Adaptado del script oficial del propio módulo:
-- modules/ps_eventbus/sql/tools/clean_incremental_sync.sql
-- =====================================================================

SET @db_name = DATABASE();
SET @quantity_needed = 1000;

SET @eventbus_incremental_sync_table = (SELECT table_name
FROM information_schema.tables
WHERE table_schema = @db_name
AND table_name LIKE '%\_eventbus\_incremental\_sync');

SET @eventbus_type_sync_table = (SELECT table_name
FROM information_schema.tables
WHERE table_schema = @db_name
AND table_name LIKE '%\_eventbus\_type\_sync');


-- ---------------------------------------------------------------------
-- PASO 0: DIAGNÓSTICO (solo lectura) - ejecuta esto primero y revisa
-- ---------------------------------------------------------------------

SET @full_breakdown = CONCAT('
    SELECT type, COUNT(*) AS pendientes
    FROM ', @eventbus_incremental_sync_table, '
    GROUP BY type
    ORDER BY pendientes DESC;
');
PREPARE full_breakdown FROM @full_breakdown;
EXECUTE full_breakdown;
DEALLOCATE PREPARE full_breakdown;


-- ---------------------------------------------------------------------
-- PASO 1: PURGA + forzar resincronización completa de los tipos con backlog
-- (no ejecutar hasta haber revisado el PASO 0)
-- ---------------------------------------------------------------------

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
