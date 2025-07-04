<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('Layered_indexable_attribute_lang_value')) {
    if (\Module::isInstalled('ps_facetedsearch')) {
        class Layered_indexable_attribute_lang_value extends \Dingedi\TablesTranslation\DgTableTranslatable17
        {
            /**
             * @var string|false
             */
            public $module = 'ps_facetedsearch';
            /**
             * @var string
             */
            public $table = 'layered_indexable_attribute_lang_value';

            public function __construct()
            {
                if (\Dingedi\PsTools\DgShopInfos::isPrestaShop16()) {
                    $this->module = 'blocklayered';
                }

                $primary_key = 'id_attribute';

                $fields = array('meta_title', 'url_name');
                $fields_rewrite = array(
                    'url_name' => 'meta_title'
                );
                $fields_tags = array();

                parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags);
            }

            /**
             * @param mixed[] $objectSource
             * @param mixed[] $objectDest
             * @param \Dingedi\TablesTranslation\DgTableTranslation $class
             * @return mixed[]
             */
            public function beforeAction($objectSource, $objectDest, $class)
            {
                if ($objectSource['meta_title'] !== "") {
                    $objectDest['meta_title'] = $class->_translate($objectSource['meta_title']);
                } else {
                    $item = \Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "attribute_lang WHERE id_attribute = " . $objectDest['id_attribute'] . " AND id_lang = " . $objectDest['id_lang']);
                    $item = $item[0];

                    if ($item !== false) {
                        $objectDest['meta_title'] = $item['name'];
                    }
                }

                return $objectDest;
            }
        }
    }
}
