<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

class PEExportedProduct
{
    const TABLE_NAME = 'pe_exported_product';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = 'CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '` (
				`id_exported_product` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_product`  INT(11) NULL,
		        `id_task` VARCHAR(255) NULL,
				PRIMARY KEY (`id_exported_product`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        return \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        $query = 'DROP TABLE IF EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '`';
        return \Db::getInstance()->execute($query);
    }

    public static function saveExportedProductIdsToDb($product_ids, $id_task)
    {
        foreach ($product_ids as $product) {
            \Db::getInstance()->insert(self::TABLE_NAME, ['id_product' => $product['id_product'], 'id_task' => (int)$id_task]);
        }
    }
}