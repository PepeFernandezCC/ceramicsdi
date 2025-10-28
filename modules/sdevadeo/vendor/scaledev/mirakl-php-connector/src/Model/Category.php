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
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Model;

use Scaledev\MiraklPhpConnector\Collection\CategoryCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Category
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Category extends AbstractModel
{
    /**
     * Code Category
     *
     * @var string
     */
    private $code;
    /**
     * Label of the Category
     *
     * @var string
     */
    private $label;
    /**
     * Translations of the label
     *
     * @var array
     */
    private $label_translations;
    /**
     * Depth of the category
     *
     * @var int
     */
    private $level;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return array
     */
    public function getLabelTranslations()
    {
        return $this->label_translations;
    }

    /**
     * @param array $label_translations
     * @return $this
     */
    public function setLabelTranslations(array $label_translations)
    {
        $this->label_translations = $label_translations;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     * @param string $parentCode
     * @return $this
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
        return $this;
    }

    /**
     * @return Category
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    /**
     * @param Category $parentCategory
     * @return $this
     */
    public function setParentCategory(Category $parentCategory)
    {
        $this->parentCategory = $parentCategory;
        return $this;
    }

    /**
     * @return CategoryCollection
     */
    public function getChildCategoryCollection()
    {
        return $this->childCategoryCollection;
    }

    /**
     * @param CategoryCollection $childCategoryCollection
     * @return $this
     */
    public function setChildCategoryCollection(CategoryCollection $childCategoryCollection)
    {
        $this->childCategoryCollection = $childCategoryCollection;
        return $this;
    }
}
