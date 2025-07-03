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

class Image_lang extends \Dingedi\TablesTranslation\DgTableTranslatable16
{
    /**
     * @var string
     */
    public $table = 'image_lang';
    /**
     * @var string|false
     */
    public $object_model = 'Image';

    public function __construct()
    {
        $primary_key = 'id_image';

        $fields = array('legend');
        $fields_rewrite = array();
        $fields_tags = array();

        $filters = array(
            array(
                'key'      => 'product',
                'table'    => 'image',
                'whereKey' => 'id_product',
                'label'    => $this->l('Product')
            )
        );

        parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags, $filters);
    }

    /**
     * @param string $filter
     * @return mixed[]
     */
    public function getFilterData($filter)
    {
        if ($filter === 'product') {
            $products = Db::getInstance()->executeS('
			SELECT c.id_product as `value`, cl.name as label
			FROM ' . _DB_PREFIX_ . 'product c' . Shop::addSqlAssociation('product', 'c') . '
			LEFT JOIN ' . _DB_PREFIX_ . 'product_lang cl ON c.id_product = cl.id_product' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE cl.id_lang = ' . (int)\Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId()
            );

            return array_map(function ($a) {
                $a['label'] = '#' . $a['value'] . ' ' . $a['label'];

                return $a;
            }, $products);
        }

        return parent::getFilterData($filter);
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->l('Images');
    }
}
