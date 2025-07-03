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

if (!class_exists('Smartblog_smart_blog_category_lang')) {
    class Smartblog_smart_blog_category_lang extends \Dingedi\TablesTranslation\DgTableTranslatable17
    {
        /**
         * @var string
         */
        public $table = 'smart_blog_category_lang';
        /**
         * @var string|false
         */
        public $module = 'smartblog';
        /**
         * @var string|false
         */
        public $controller = 'AdminBlogCategory';

        public function __construct()
        {
            $primary_key = 'id_smart_blog_category';

            $fields = array('name', 'meta_title', 'meta_keyword', 'meta_description', 'description', 'link_rewrite');
            $fields_rewrite = array(
                'link_rewrite' => 'name'
            );
            $fields_tags = array('meta_keywords');

            parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags);
        }
    }
}
