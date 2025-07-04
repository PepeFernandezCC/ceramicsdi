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

if (!class_exists('Prestablog_prestablog_news_lang')) {
    class Prestablog_prestablog_news_lang extends \Dingedi\TablesTranslation\DgTableTranslatable17
    {
        /**
         * @var string|false
         */
        public $controller = \Dingedi\TablesTranslation\AbstractTableAdapter::CONTROLLER_ADMINMODULES;
        /**
         * @var string|false
         */
        public $module = 'prestablog';
        /**
         * @var string
         */
        public $table = 'prestablog_news_lang';

        public function __construct()
        {
            $primary_key = 'id_prestablog_news';

            $fields = array('title', 'paragraph', 'content', 'meta_description', 'meta_keywords', 'meta_title', 'link_rewrite');
            $fields_rewrite = array(
                'link_rewrite' => 'meta_title'
            );
            $fields_tags = array();

            parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags);
        }

        /**
         * @param string $controller
         * @param bool $checkHasId
         * @return bool
         */
        public function supportController($controller, $checkHasId = true)
        {
            return parent::supportController($controller, $checkHasId) && \Tools::getValue('editNews') !== false;
        }

        /**
         * @return bool|int
         */
        public function getObjectIdInRequest()
        {
            return \Tools::getValue('idN');
        }
    }
}
