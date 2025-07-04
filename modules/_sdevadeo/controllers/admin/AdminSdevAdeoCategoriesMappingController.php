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

use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Core\Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoCategoriesMappingController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoCategoriesMappingController extends AbstractModuleAdminController
{
    const TEMPLATE = 'categories_mapping';

    /**
     * @inheritdoc
     * @throws PrestaShopDatabaseException
     */
    public function renderList()
    {
        $id_lang = $this->context->language->id;
        $current_shop = new Shop($this->context->shop->id);
        $root = Category::getRootCategory($id_lang, $current_shop);
        $first_level_categories = Category::getChildren($root->id, $id_lang, true, $current_shop->id);

        foreach ($first_level_categories as $key => $first_level_category) {
            $first_level_categories[$key]['has_children'] = Category::hasChildren($first_level_category['id_category'], $id_lang, true, $current_shop->id);
        }

        $categoryMapping = array();
        foreach (SdevAdeoCategoryMapping::findAll() as $mapping) {
            if ($mapping['category_rule'] == null) {
                $mapping['category_rule'] = 0;
            }
            $categoryMapping[$mapping['prestashop_category']] = array(
                'category_rule' => $mapping['category_rule'],
                'active' => $mapping['active'],
            );
        }
        $this->context->smarty->assign(array(
            'root' => $root,
            'rootDirectChild' => $first_level_categories,
            'category_rules' => SdevAdeoCategoryRule::findAll(),
            'category_mapping' => $categoryMapping,
        ));

        return parent::renderList();
    }

    public function ajaxProcessGetSubCategories()
    {
        $parent = Tools::getRequest()['idCategory'];
        $id_lang = $this->context->language->id;
        $current_shop = new Shop($this->context->shop->id);

        $subCategories = Category::getChildren($parent, $id_lang, true, $current_shop->id);
        foreach ($subCategories as $key => $category) {
            $subCategories[$key]['has_children'] = Category::hasChildren($category['id_category'], $id_lang, true, $current_shop->id);
            $query = (new \DbQuery())
                ->from(SdevAdeoCategoryMapping::getTableName())
                ->where(SdevAdeoCategoryMapping::COLUMN_PRESTASHOP_CATEGORY .'='.pSQL($category['id_category']))
                ->where(SdevAdeoCategoryMapping::COLUMN_ID_SHOP.'='.(int)$current_shop->id);
            $subCategories[$key]['mapping'] = Db::getInstance()->executeS($query);
        }

        die(json_encode($subCategories));
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function ajaxProcessSaveMapping()
    {
        $result = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        try {
            self::createMappingCategory(Tools::getRequest()['categoryList']);
            $result['errorMessage'][] = $this->module->l('Category rules successfully added.');
        } catch (Exception $e) {
            $result['hasError'] = true;
            $result['errorMessage'][] = $e->getMessage();
        }
        die(json_encode($result));
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private static function createMappingCategory($categoryMapping, $mappingReference = null) {
        if (!$mappingReference) {
            if (
                array_key_exists('childrenList', $categoryMapping)
                && !empty($categoryMapping['childrenList'])
            ) {
                foreach ($categoryMapping['childrenList'] as $childCategoryMapping) {
                    self::createMappingCategory($childCategoryMapping);
                }
            } else {
                $child = (new Category($categoryMapping['idCategory']))->getAllChildren();
                foreach ($child->getAll()->getResults() as $childCategory) {
                    self::createMappingCategory($childCategory, $categoryMapping);
                }
            }
        }

        (int)$categoryRule = $mappingReference == null && array_key_exists('categoryRule', $categoryMapping)
            ? $categoryMapping['categoryRule']
            : $mappingReference['categoryRule'];
        if ($categoryRule == 0) {
            $categoryRule = null;
        }

        $active = $mappingReference == null && array_key_exists('categoryActivated', $categoryMapping)
            ? $categoryMapping['categoryActivated']
            : $mappingReference['categoryActivated'];
        $active = $active == 'true';

        (int)$category = $mappingReference == null && array_key_exists('idCategory', $categoryMapping)
            ? $categoryMapping['idCategory']
            : $categoryMapping->id;

        $query = (new \DbQuery())
            ->select('id')
            ->from(SdevAdeoCategoryMapping::getTableName())
            ->where(SdevAdeoCategoryMapping::COLUMN_PRESTASHOP_CATEGORY.'='.(int)$category)
            ->where(SdevAdeoCategoryMapping::COLUMN_ID_SHOP.'='.(int)Context::getContext()->shop->id);
        if (!($id = Db::getInstance()->getValue($query))) {
            $id = null;
        }

        $categoryMapping = (new SdevAdeoCategoryMapping($id))
            ->setCategoryRule($categoryRule)
            ->setPrestashopCategory($category)
            ->setActive($active);
        if ($categoryMapping->getId()) {
            $categoryMapping->update();
        } else {
            $categoryMapping->add();
        }
    }
}
