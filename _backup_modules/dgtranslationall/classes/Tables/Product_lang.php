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

class Product_lang extends \Dingedi\TablesTranslation\DgTableTranslatable16
{
    /**
     * @var false|string
     */
    public $controller = 'AdminProducts';
    /**
     * @var string
     */
    public $table = 'product_lang';
    /**
     * @var string|false
     */
    public $object_model = 'Product';
    /**
     * @var string|false
     */
    public $active_filter = 'product.active';
    /**
     * @var mixed[]
     */
    public $dynamic_fields = array('tags');

    public function __construct()
    {
        $primary_key = 'id_product';

        $fields = array('description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords', 'meta_title', 'name', 'available_now', 'available_later');

        $fields = array_merge($fields, $this->dynamic_fields);

        $fields_rewrite = array(
            'link_rewrite' => 'name'
        );
        $fields_tags = array('meta_keywords');

        $filters = array(
            array(
                'key' => 'category',
                'table' => 'category_product',
                'whereKey' => 'id_category',
                'label' => $this->l('Category')
            ),
            array(
                'key' => 'manufacturer',
                'table' => 'product',
                'whereKey' => 'id_manufacturer',
                'label' => $this->l('Brands'),
            )
        );

        $relatedItems = array(
            array(
                'key' => 'images',
                'label' => $this->l('Images')
            ),
            array(
                'key' => 'features',
                'label' => $this->l('Features')
            ),
            array(
                'key' => 'attributes',
                'label' => $this->l('Attributes')
            ),
            array(
                'key' => 'attachments',
                'label' => $this->l('Files')
            )
        );

        parent::__construct($this->table, $primary_key, $fields, $fields_rewrite, $fields_tags, $filters, $relatedItems);
    }

    /**
     * @param array $objectSource
     * @param array $objectDest
     * @param $class DgTableTranslation
     * @return bool
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     */
    public function afterAction($objectSource, $objectDest, $class)
    {
        if (!in_array('tags', $class->dgTableTranslatable->getFields())) {
            return true;
        }

        $id_product = (int)$objectSource['id_product'];

        $to_id = $class->to['id_lang'];
        $from_id = $class->from['id_lang'];

        $isAutomaticTranslation = isset($_POST['translation_data']['automatic_progress']) && $_POST['translation_data']['automatic_progress'] === true;

        $overwrite = $class->overwrite;

        $tags = \Tag::getProductTags($id_product);

        if ($isAutomaticTranslation) {
            $overwrite = false;

            if (empty($this->getTagsInRequest($from_id)) && (json_encode(isset($tags[$from_id]) ? $tags[$from_id] : []) !== json_encode($this->getTagsInRequest($from_id)))) {
                foreach ([$from_id, $to_id] as $id_lang) {
                    $_POST['tags_' . $id_lang] = [];
                    $_POST['form']['step6']['tags'][$id_lang] = "";
                }
            }
        }

        if (($isAutomaticTranslation === false && $tags !== false && isset($tags[$from_id])) || ($isAutomaticTranslation && !empty($this->getTagsInRequest($from_id)))) {
            if ($overwrite || $isAutomaticTranslation || (isset($tags[$to_id]) && count($tags[$to_id]) !== count($tags[$from_id])) || !isset($tags[$to_id])) {
                $langs = array($from_id, $to_id);

                $hash = $this->hash(
                    $isAutomaticTranslation ? $this->getTagsInRequest($from_id) : $tags[$from_id],
                    isset($tags[$to_id]) ? $tags[$to_id] : []
                );

                if ($overwrite === false && !$class->dgSameTranslations->needTranslation($id_product, 'tags|' . $hash, $langs)) {
                    return true;
                }

                if ($isAutomaticTranslation) {
                    $this->saveTagsPostIfExists($id_product, $from_id);
                    $this->saveTagsPostIfExists($id_product, $to_id);

                    $tags = \Tag::getProductTags($id_product);
                }

                $tags[$to_id] = array();

                if (isset($tags[$from_id])) {
                    foreach ($tags[$from_id] as $tag) {
                        $_translation = $class->_translate($tag);

                        if (trim($_translation) !== "") {
                            $tags[$to_id][] = $_translation;
                        }
                    }
                }

                $tags[$to_id] = array_filter($tags[$to_id]);

                if ($isAutomaticTranslation && empty($tags[$to_id])) {
                    $_POST['tags_' . $to_id] = [];
                    $_POST['form']['step6']['tags'][$to_id] = "";
                }

                $this->saveTags($id_product, $to_id, $tags);

                $tags = \Tag::getProductTags($id_product);

                if ($isAutomaticTranslation) {
                    foreach ($tags as $key => $value) {
                        if (!in_array($key, [$from_id, $to_id])) {
                            continue;
                        }

                        $_POST['tags_' . $key] = implode(',', $value);
                        $_POST['form']['step6']['tags'][$to_id] = implode(',', $value);
                    }
                }

                $hash = $this->hash(
                    $tags[$from_id],
                    isset($tags[$to_id]) ? $tags[$to_id] : []
                );

                $class->dgSameTranslations->addTranslations(array(
                    'i' => $id_product,
                    'f' => 'tags|' . $hash,
                    'langs' => $langs
                ));
            }
        }

        return true;
    }

    /**
     * @return mixed[]
     */
    private function getTagsInRequest($id_lang)
    {
        $tags = [];

        if (isset($_POST['form']['step6']['tags'][$id_lang])) {
            $tags = explode(',', $_POST['form']['step6']['tags'][$id_lang]);
        } else if (isset($_POST['tags_' . $id_lang])) {
            $tags = explode(',', $_POST['tags_' . $id_lang]);
        }

        $tags = array_filter($tags);

        return array_map('trim', $tags);
    }

    private function saveTagsPostIfExists($id_product, $id_lang)
    {
        if (isset($_POST['form']['step6']['tags'][$id_lang])) {
            $tags = \Tag::getProductTags($id_product);
            $tags[$id_lang] = $this->getTagsInRequest($id_lang);
            $this->saveTags($id_product, $id_lang, $tags);
        } else if (isset($_POST['tags_' . $id_lang])) {
            $tags = \Tag::getProductTags($id_product);
            $tags[$id_lang] = $this->getTagsInRequest($id_lang);
            $this->saveTags($id_product, $id_lang, $tags);
        }
    }

    private function saveTags($id_product, $to_id, $tags)
    {
        if (method_exists('Tag', 'deleteProductTagsInLang')) {
            try {
                \Tag::deleteProductTagsInLang($id_product, $to_id);

                if (!empty($tags[$to_id])) {
                    \Tag::addTags($to_id, $id_product, $tags[$to_id]);
                }

                return;
            } catch (\Exception $e) {

            }
        }

        \Tag::deleteTagsForProduct($id_product);

        foreach ($tags as $id_lang => $tags_lang) {
            if (!empty($tags_lang)) {
                \Tag::addTags($id_lang, $id_product, $tags_lang);
            }
        }
    }

    /**
     * @return string
     */
    private function hash(array $arr1, array $arr2)
    {
        return sha1(json_encode($arr1) . json_encode($arr2));
    }

    /**
     * @param string $filter
     * @return mixed[]
     */
    public function getFilterData($filter)
    {
        if ($filter === 'category') {
            $categories = Db::getInstance()->executeS('
			SELECT c.id_category as `value`, c.level_depth, cl.name as label
			FROM ' . _DB_PREFIX_ . 'category c' . Shop::addSqlAssociation('category', 'c') . '
			LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON c.id_category = cl.id_category' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE cl.id_lang = ' . (int)\Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId() . '
			' . 'ORDER BY c.`level_depth` ASC, category_shop.`position` ASC'
            );

            return array_map(function ($a) {
                $a['label'] = str_repeat('-', (int)$a['level_depth'] * 3) . ' #' . $a['value'] . ' ' . $a['label'];
                unset($a['level_depth']);

                return $a;
            }, $categories);
        }

        if ($filter === 'manufacturer') {
            $manufacturers = Db::getInstance()->executeS('
			SELECT DISTINCT(m.id_manufacturer) as `value`, m.name as label
			FROM ' . _DB_PREFIX_ . 'manufacturer m' . Shop::addSqlAssociation('manufacturer', 'm')
            );

            return array_map(function ($a) {
                $a['label'] = '#' . $a['value'] . ' ' . $a['label'];
                return $a;
            }, $manufacturers);
        }

        return parent::getFilterData($filter);
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->l('Products');
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return void
     */
    public function translateRelatedItems($objectSource, $objectDest, $class)
    {
        foreach ($this->parseRequestRelatedItems() as $relatedItem) {

            if (in_array($relatedItem, ['images', 'features', 'attributes', 'attachments'])) {
                $items = [];
                $key = '';
                $table = null;

                if ($relatedItem === 'images') {
                    $items = (new Product($objectSource['id_product']))->getImages($class->from['id_lang']);
                    $key = 'id_image';
                    $table = new Image_lang();
                }
                if ($relatedItem === 'features') {
                    $items = (new Product($objectSource['id_product']))->getFeatures();
                    $key = 'id_feature';
                    $table = new Feature_lang();
                }
                if ($relatedItem === 'attributes') {
                    $items = (new Product($objectSource['id_product']))->getAttributeCombinations($class->from['id_lang']);
                    $key = 'id_attribute';
                    $table = new Attribute_lang();
                }
                if ($relatedItem === 'attachments') {
                    $items = (new Product($objectSource['id_product']))->getAttachments($class->from['id_lang']);
                    $key = 'id_attachment';
                    $table = new Attachment_lang();
                }

                $items_ids = array_values(array_unique(array_map(function ($i) use($key) {
                    return $i[$key];
                }, $items)));

                foreach ($items_ids as $id) {
                    $_POST['translation_data']['plage_enabled'] = 'true';
                    $_POST['translation_data']['start_id'] = $id;
                    $_POST['translation_data']['end_id'] = $id;

                    $dgTableTranslation = new DgTableTranslation($table, (int)$class->from['id_lang'], (int)$class->to['id_lang'], $class->overwrite, $class->latin);
                    $dgTableTranslation->translate(1);
                }
            }
        }
    }
}
