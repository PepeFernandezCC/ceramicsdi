<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use Scaledev\Adeo\Core\ObjectModel\AbstractObjectModel;
use Scaledev\Adeo\Component\Configuration as ScaledevConfiguration;

/**
 * @param $object
 * @return bool
 */
function upgrade_module_1_3_0($object)
{
    return updateCategoryRule()
        && updatePricesRule()
        && updateCarrierRule()
        && updatePriceRule()
        && updateTable()
    ;
}

function updateCategoryRule()
{

    $tableName = _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoCategoryRule::TABLE;
    $columnsToAdd = [
        'weightMax' => ['type' => 'FLOAT', 'position' => 'AFTER `logisticClass`'],
        'weightMin' => ['type' => 'FLOAT', 'position' => 'AFTER `weightMax`']
    ];

    foreach ($columnsToAdd as $columnName => $columnDetails) {
        // Requête pour vérifier si la colonne existe
        $sqlCheck = 'SHOW COLUMNS FROM `' . $tableName . '` LIKE "' . $columnName . '"';
        $result = Db::getInstance()->executeS($sqlCheck);

        // Si la colonne n'existe pas, on l'ajoute
        if (empty($result)) {
            $sql = 'ALTER TABLE `' . $tableName . '` ADD `' . $columnName . '` ' . $columnDetails['type'] . ' NULL ' . $columnDetails['position'] . ';';
            Db::getInstance()->execute($sql);
        }
    }

    return true;
}

function updatePricesRule()
{
    $tableName = _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoPricingRule::TABLE;
    $columnName = 'countries';

    // Requête pour vérifier si la colonne existe
    $sqlCheck = 'SHOW COLUMNS FROM `' . $tableName . '` LIKE "' . $columnName . '"';
    $result = Db::getInstance()->executeS($sqlCheck);

    if (empty($result)) {
        // Si la colonne n'existe pas, on l'ajoute
        $sql = 'ALTER TABLE `' . $tableName . '` ADD `' . $columnName . '`TEXT NULL AFTER `categoryRule`;';
        return Db::getInstance()->execute($sql);
    }
}

function updateCarrierRule()
{
    $tableName = _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoCarrierRule::TABLE;
    $columnName = 'countryIso';

    // Requête pour vérifier si la colonne existe
    $sqlCheck = 'SHOW COLUMNS FROM `' . $tableName . '` LIKE "' . $columnName . '"';
    $result = Db::getInstance()->executeS($sqlCheck);

    if (empty($result)) {
        // Si la colonne n'existe pas, on l'ajoute
        $sql = 'ALTER TABLE `' . $tableName . '` ADD `' . $columnName . '` ENUM(\'FR\',\'IT\',\'ES\',\'PT\') NOT NULL DEFAULT \'FR\' AFTER `internalCarrierId`;';
        return Db::getInstance()->execute($sql);
    }

    return true;
}

function updatePriceRule()
{
    $tableName = _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoCategoryRule::TABLE;
    $columnName = SdevAdeoCategoryRule::COLUMN_ADDITIONAL_PRICE;

    // Requête pour vérifier l'existence de la colonne
    $sqlCheck = 'SHOW COLUMNS FROM `' . $tableName . '` LIKE "' . $columnName . '"';
    $result = Db::getInstance()->executeS($sqlCheck);

    if (!empty($result)) {
        $categoryRule = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoCategoryRule::TABLE . ' WHERE 1');
        foreach ($categoryRule as $category){
            if(!$category['additionalPrice']){
                continue;
            }

            $priceRule = Scaledev\Adeo\Core\Tools::getPricingRule($category['id']);

            if(!$priceRule) {
                (new SdevAdeoPricingRule())
                    ->setMaxAmount('1000000')
                    ->setMinAmount('0')
                    ->setValue($category['additionalPrice'])
                    ->setCategoryRule($category['id'])
                    ->setTypePercent(true)
                ;
            }
            
        }

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . AbstractObjectModel::TABLE_PREFIX . SdevAdeoCategoryRule::TABLE . '` DROP COLUMN ' . SdevAdeoCategoryRule::COLUMN_ADDITIONAL_PRICE;

        return Db::getInstance()->execute($sql);
    }

    return true;
}

function updateTable()
{
    $models = array(
        SdevAdeoFilter::class,
    );

    $result = true;
    /** @var AbstractObjectModel $model */
    foreach ($models as $model) {
        if (!($result &= $model::createTable())) {
            $this->_errors[] = sprintf(
                $this->l('An issue appears while installation of table: %s.'),
                $model::getTableName()
            );
        }
    }

    return $result;
}