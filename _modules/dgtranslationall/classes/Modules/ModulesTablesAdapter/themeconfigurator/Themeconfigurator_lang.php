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

if (!class_exists('Themeconfigurator_lang')) {
    class Themeconfigurator_lang extends \Dingedi\TablesTranslation\DgTableTranslatable17
    {
        /**
         * @var string|false
         */
        public $module = 'themeconfigurator';
        /**
         * @var string
         */
        public $table = 'themeconfigurator';

        public function __construct()
        {
            $primary_key = 'item_order';

            $fields = array('title');
            $fields_rewrite = array();
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
            $objectDest = array_filter($objectSource, function ($v, $k) {
                return !in_array($k, array('id_item', 'id_lang'));
            }, ARRAY_FILTER_USE_BOTH);

            $objectDest['url'] = $class->_translateContentUrls($objectSource['url']);

            return $objectDest;
        }
    }
}
