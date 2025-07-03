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

require_once 'Abstract_Ybc_blog.php';

if (!class_exists('Ybc_blog_gallery_lang')) {
    class Ybc_blog_gallery_lang extends Abstract_Ybc_blog
    {
        /**
         * @var string
         */
        public $table = 'ybc_blog_gallery_lang';

        public function __construct()
        {
            $primary_key = 'id_gallery';

            $fields = array('title', 'description');
            $fields_rewrite = array();
            $fields_tags = array();

            parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags);
        }
    }
}
