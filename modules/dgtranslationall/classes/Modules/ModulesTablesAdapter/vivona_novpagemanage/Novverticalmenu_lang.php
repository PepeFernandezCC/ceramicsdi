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


use Dingedi\TablesTranslation\DgTableTranslation;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('Novverticalmenu_lang')) {
    class Novverticalmenu_lang extends \Dingedi\TablesTranslation\DgTableTranslatable17
    {
        /**
         * @var string|false
         */
        public $module = 'novpagemanage';
        /**
         * @var string
         */
        public $table = 'novverticalmenu_lang';

        public function __construct()
        {
            $primary_key = 'id_novverticalmenu';

            $fields = array('title', 'sub_title', 'html');
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
        public function beforeTranslateAction($objectSource, $objectDest, $class)
        {
            $sourceDecoded = base64_decode($objectSource['html']);

            $objectSource['html'] = $sourceDecoded === '""' ? "" : $sourceDecoded;
            $objectDest['html'] = base64_decode($objectDest['html']);

            return array($objectSource, $objectDest);
        }

        /**
         * @param mixed[] $objectSource
         * @param mixed[] $objectDest
         * @param \Dingedi\TablesTranslation\DgTableTranslation $class
         * @return mixed[]
         */
        public function afterTranslateAction($objectSource, $objectDest, $class)
        {
            if ($objectDest['html'] === "") {
                $objectDest['html'] = '""';
            }

            $objectDest['html'] = base64_encode($objectDest['html']);

            return array($objectSource, $objectDest);
        }
    }
}
