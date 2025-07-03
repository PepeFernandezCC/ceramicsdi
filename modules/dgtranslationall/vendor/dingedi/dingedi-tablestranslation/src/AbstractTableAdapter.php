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
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\TablesTranslation;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractTableAdapter
{
    const CONTROLLER_ADMINMODULES = "AdminModules";

    /**
     * @var string|false
     */
    public $module = false;

    /**
     * @var mixed[]|false
     */
    public $supportedModuleVersion = false;

    /**
     * @var string|false
     */
    public $controller = false;

    /**
     * @var string|false
     */
    public $object_model = false;

    /**
     * @var mixed[]
     */
    public $fields_rewrite = array(
        'link_rewrite' => array('name', 'title', 'meta_title'),
        'url_rewrite' => array('name', 'title', 'meta_title'),
    );

    /**
     * @var mixed[]
     */
    public $fields_tags = array('meta_keywords', 'tags');

    /**
     * @var string|false
     */
    public $active_filter = false;

    /**
     * @var bool|null
     */
    public $exist;

    /**
     * @var bool|null
     */
    public $multi_shop;

    /**
     * @var int|null
     */
    public $total_items;

    /**
     * @var mixed[]|bool|null
     */
    public $supported_item_rewrite = null;

    /**
     * @var int|null
     */
    public $id_shop;

    /**
     * @var bool|null
     */
    public $certified;

    /**
     * @var mixed[]
     */
    public $requestFilters = array();

    /**
     * @var mixed[]
     */
    public $requestRelatedItems = array();

    /**
     * @var mixed[]
     */
    public $dynamic_fields = array();
    /**
     * @var string
     */
    public $table;
    /**
     * @var string|null
     */
    public $primary_key;
    /**
     * @var mixed[]
     */
    public $fields = array();
    /**
     * @var mixed[]
     */
    public $filters = array();
    /**
     * @var mixed[]
     */
    public $relatedItems = array();

    /**
     * @param string|null $primary_key
     * @param string $table
     */
    public function __construct($table, $primary_key = null, array $fields = array(), array $fields_rewrite = array(), array $fields_tags = array(), array $filters = array(), array $relatedItems = array())
    {
        $table = (string) $table;
        $this->table = $table;
        $this->primary_key = $primary_key;
        $this->fields = $fields;
        $this->filters = $filters;
        $this->relatedItems = $relatedItems;
        $this->fields_rewrite = array_merge_recursive($fields_rewrite, $this->fields_rewrite);
        $this->fields_tags = array_unique(array_merge($fields_tags, $this->fields_tags));
    }

    /**
     * @param string $table
     * @return bool
     */
    public function supportTable($table)
    {
        return $table === $this->table;
    }

    /**
     * @return bool
     */
    public function supportModuleVersion()
    {
        if ($this->module === false || $this->supportedModuleVersion === false) {
            return true;
        }

        $moduleVersion = \Module::getInstanceByName($this->module)->version;

        if (isset($this->supportedModuleVersion['min']) && !\Tools::version_compare($this->supportedModuleVersion['min'], $moduleVersion, '<=')) {
            return false;
        }

        if (isset($this->supportedModuleVersion['max']) && !\Tools::version_compare($this->supportedModuleVersion['max'], $moduleVersion, '>=')) {
            return false;
        }

        return true;
    }

    /**
     * @param string $controller
     * @param bool $checkHasId
     * @return bool
     */
    public function supportController($controller, $checkHasId = true)
    {
        if (strtolower($this->controller) === strtolower(self::CONTROLLER_ADMINMODULES) && \Tools::getValue('configure') !== $this->module) {
            return false;
        }

        if (!$this->supportModuleVersion()) {
            return false;
        }

        return strtolower($controller) === strtolower($this->controller)
            && (($checkHasId && $this->getObjectIdInRequest() !== false) || !$checkHasId);
    }

    /**
     * @return bool
     */
    public function requestUriMatch()
    {
        return true;
    }

    /**
     * @return bool|int
     */
    public function getObjectIdInRequest()
    {
        $default = false;

        if ($this->requestUriMatch()) {
            $base = explode('?', (string)$_SERVER['REQUEST_URI']);
            $default = basename($base[0]);

            if ($default === 'edit') {
                $default = basename(dirname($base[0]));
            }
        }

        $id = (int)\Tools::getValue($this->primary_key, $default);

        if ($id === 0) {
            return false;
        }

        return $id;
    }

    /**
     * @param string $filter
     * @return mixed[]
     */
    public function getFilterData($filter)
    {
        return array();
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return void
     */
    public function translateRelatedItems($objectSource, $objectDest, $class)
    {
    }

    /**
     * @return bool
     */
    public function supportObjectModel($objectModel)
    {
        if (!is_string($this->object_model)) {
            return false;
        }

        return get_class($objectModel) === $this->object_model;
    }

    /**
     * @param mixed[] $filters
     * @return void
     */
    public function setRequestFilters($filters)
    {
        $this->requestFilters = $filters;
    }

    /**
     * @return bool
     */
    public function hasRequestFilters()
    {
        return !empty($this->requestFilters);
    }

    /**
     * @param mixed[] $related_items
     * @return void
     */
    public function setRequestRelatedItems($related_items)
    {
        $this->requestRelatedItems = $related_items;
    }

    /**
     * @return bool
     */
    public function hasRequestRelatedItems()
    {
        return !empty($this->requestRelatedItems);
    }

    /**
     * @return mixed[]
     */
    public function parseRequestRelatedItems()
    {
        return $this->requestRelatedItems;
    }

    /**
     * @return mixed[]
     */
    public function parseRequestFilters()
    {
        $filters = [];

        foreach ($this->requestFilters as $name => $filterGroup) {
            foreach ($filterGroup as $filter) {
                $filters[$name][] = (int)$filter['value'];
            }
        }

        return $filters;
    }

    /**
     * @param string $name
     * @return mixed[]
     */
    public function getFilter($name)
    {
        $elem = false;

        foreach ($this->filters as $filter) {
            if ($filter['key'] === $name) {
                $elem = $filter;
                break;
            }
        }

        return $elem;
    }

    /**
     * @param \DbQuery $sql
     * @return \DbQuery
     */
    public function addSqlRequestFilters($sql)
    {
        $filters = $this->parseRequestFilters();

        foreach ($filters as $name => $values) {
            $filter = $this->getFilter($name);

            if ($filter === false) {
                continue;
            }

            $key = $this->getPrimaryKey();
            $alias = 'al' . $name;

            $sql->innerJoin($filter['table'], $alias, $this->getTableName() . '.' . $key . ' = ' . $alias . '.' . $key);
            $sql->where($alias . '.' . $filter['whereKey'] . ' IN (' . implode(',', $values) . ') ');
        }

        return $sql;
    }

    /**
     * @return mixed[]
     */
    public function getDynamicFields()
    {
        return $this->dynamic_fields;
    }

    /**
     * @return bool
     */
    public function supportActiveFilter()
    {
        return $this->active_filter !== false;
    }

    /**
     * @return false|string
     */
    public function getActiveFilter()
    {
        return $this->active_filter;
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return mixed[]
     */
    public function beforeAction($objectSource, $objectDest, $class)
    {
        return $objectDest;
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return mixed[]
     */
    public function beforeTranslateAction($objectSource, $objectDest, $class)
    {
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
        return array($objectSource, $objectDest);
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return bool
     */
    public function afterAction($objectSource, $objectDest, $class)
    {
        return true;
    }

    /**
     * @param mixed[] $objectSource
     * @param mixed[] $objectDest
     * @param \Dingedi\TablesTranslation\DgTableTranslation $class
     * @return bool
     */
    public function needTranslation($objectSource, $objectDest, $class)
    {
        return true;
    }
}
