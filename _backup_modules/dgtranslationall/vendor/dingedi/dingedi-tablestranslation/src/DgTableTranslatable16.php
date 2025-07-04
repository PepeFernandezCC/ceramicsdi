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

namespace Dingedi\TablesTranslation;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DgTableTranslatable16 extends AbstractTableAdapter implements \JsonSerializable
{
    /**
     * Get table name with or without prefix
     * @param bool $with_prefix
     * @return string
     */
    public function getTableName($with_prefix = true)
    {
        $withoutPrefix = preg_replace('/^' . _DB_PREFIX_ . '/', '', $this->table);

        if ($with_prefix) {
            return _DB_PREFIX_ . $withoutPrefix;
        }

        return $withoutPrefix;
    }

    /**
     * @throws \Exception
     * @return bool|string
     */
    public function getPrimaryKey()
    {
        if ($this->primary_key === null) {
            $this->primary_key = $this->guessPrimaryKey();
        }

        return $this->primary_key;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @param bool $withDynamicFields
     * @return mixed[]
     */
    public function getFields($withDynamicFields = true)
    {
        if (empty($this->fields)) {
            $this->fields = $this->guessFields();
        }

        if ($withDynamicFields === false) {
            return array_diff($this->fields, $this->dynamic_fields);
        }

        return $this->fields;
    }


    /**
     * @param mixed[] $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        foreach ($this->fields_rewrite as $k => $v) {
            if (!in_array($k, $fields)) {
                unset($this->fields_rewrite[$k]);
            }
        }

        return $this;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @return mixed[]
     */
    public function getFieldsRewrite()
    {
        $fields = array_keys($this->fields_rewrite);
        $i = array();

        foreach ($fields as $field) {
            if (in_array($field, $this->getFields())) {
                $i[$field] = $this->fields_rewrite[$field];
            }
        }

        return $i;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @return mixed[]
     */
    public function getFieldsTags()
    {
        return array_intersect($this->getFields(), $this->fields_tags);
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @return bool
     */
    public function isExist()
    {
        if ($this->exist !== null) {
            return $this->exist;
        }

        try {
            $this->exist = !empty(\Db::getInstance()->executeS("SHOW TABLES LIKE '" . \pSQL($this->getTableName()) . "'"));
        } catch (\PrestaShopDatabaseException $exception) {
            $this->exist = false;
        }

        return $this->exist;
    }

    /**
     * @return bool
     */
    public function isCertified()
    {
        if ($this->certified !== null) {
            return $this->certified;
        }

        $this->certified = in_array($this->getTableName(), DgTablesList::getCertifiedList());

        return $this->certified;
    }

    /**
     * @return bool|mixed[]
     * @param mixed[] $item
     */
    public function supportedItemRewrite($item)
    {
        if ($this->supported_item_rewrite !== null) {
            return $this->supported_item_rewrite;
        }

        $item = array_keys($item);

        foreach ($item as $field) {
            if (isset($this->fields_rewrite[$field])) {
                if (!is_array($this->fields_rewrite[$field])) {
                    $this->fields_rewrite[$field] = array($this->fields_rewrite[$field]);
                }

                $i = array_intersect($this->fields_rewrite[$field], $item);

                if (!empty($i)) {
                    $this->supported_item_rewrite = array($field => array_values($i)[0]);
                    return $this->supported_item_rewrite;
                }
            }
        }

        $this->supported_item_rewrite = false;

        return false;
    }

    /**
     * @throws \Exception
     * @return mixed[]
     */
    public function getPrimaryKeys()
    {
        $keys = array();
        $keys[] = $this->getPrimaryKey();
        $keys[] = 'id_lang';

        if ($this->hasMultiShopColumn()) {
            $keys[] = 'id_shop';
        }

        return $keys;
    }

    /**
     * @return bool
     */
    public function isMultiShop()
    {
        if ($this->multi_shop !== null) {
            return $this->multi_shop;
        }

        $this->multi_shop = \Shop::isFeatureActive() && $this->hasMultiShopColumn();

        return $this->multi_shop;
    }

    /**
     * @return bool
     */
    public function hasMultiShopColumn()
    {
        return $this->hasColumn('id_shop');
    }

    /**
     * @throws \Exception
     * @return int
     */
    public function getTotalItems()
    {
        if ($this->total_items !== null) {
            return $this->total_items;
        }

        $query = new \DbQuery();
        $query->select('COUNT(' . $this->getPrimaryKey() . ') as total_items')
            ->from($this->getTableName(false))
            ->where('id_lang = ' . \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId());

        $query = $query->build();

        if ($this->hasMultiShopColumn()) {
            $query .= ' ' . \Shop::addSqlRestrictionOnLang();
        }

        $items = \Db::getInstance()->executeS($query)[0]['total_items'];

        $this->total_items = (int)$items;

        return $this->total_items;
    }

    /***
     * @throws \PrestaShopDatabaseException
     */
    /**
     * @return mixed[]
     */
    private function guessFields()
    {
        $columns = \Db::getInstance()->executeS("SHOW COLUMNS FROM " . $this->getTableName());

        if (\Dingedi\PsTools\DgTools::searchSubArray($columns, 'Field', 'id_lang') === null) {
            return array();
        }

        $item = $this->findOne(array('id_lang' => (int)\Configuration::get('PS_LANG_DEFAULT')));

        $translatableColumns = array();

        foreach ($columns as $column) {
            if (in_array($column["Field"], array('id_lang', 'id_shop'))) {
                continue;
            }

            $re = '/([a-z]*text)|(varchar\([0-9]*\))/m';

            if (preg_match($re, (string)$column["Type"]) && !empty($item)) {
                if (!($item[0][$column['Field']] !== null && $this->isJson($item[0][$column['Field']]))) {
                    $translatableColumns[] = $column["Field"];
                }
            }
        }
        return $translatableColumns;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isJson($string)
    {
        $string = (string) $string;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param string $column
     * @return bool
     */
    private function hasColumn($column)
    {
        $column = (string) $column;
        try {
            return !empty(\Db::getInstance()->executeS("SHOW COLUMNS FROM " . \pSQL($this->getTableName()) . " LIKE '" . \pSQL($column) . "'"));
        } catch (\PrestaShopDatabaseException $exception) {
            return false;
        }
    }

    /**
     * @throws \Exception
     * @return bool|string
     */
    private function guessPrimaryKey()
    {
        $primaryKey = 'id_' . preg_replace('/_lang$/i', '', $this->getTableName(false));

        if ($this->hasColumn($primaryKey)) {
            return $primaryKey;
        }

        if (\Tools::substr($primaryKey, -1) === "s") {
            $primaryKey = \Tools::substr($primaryKey, 0, -1);

            if ($this->hasColumn($primaryKey)) {
                return $primaryKey;
            }
        }

        try {
            $columns = \Db::getInstance()->executeS("SHOW COLUMNS FROM " . \pSQL($this->getTableName()));
        } catch (\PrestaShopDatabaseException $exception) {
            $columns = array();
        }

        $columns = array_filter($columns, function ($column) use ($m, $f, $primaryKey) {
            return $column['Key'] === 'PRIMARY' || (preg_match("/^id_/", (string)$column['Field'], $m) && preg_match("/^(tinyint|smallint|mediumint|int|bigint)[\(0-9\)]{0,}/i", (string)$column['Type'], $f)) || in_array($column, array(\pSQL($primaryKey), 'id_shop', 'id_lang'));
        });

        $primaryKeys = array_map(function ($item) {
            return $item['Field'];
        }, $columns);

        $primaryKeys = array_unique($primaryKeys);
        $primaryKeys = array_values(array_filter($primaryKeys, function ($v) {
            return !in_array($v, ['id_lang', 'id_shop']);
        }));

        if (count($primaryKeys) > 1 || count($primaryKeys) === 0) {
            return false;
        }

        return $primaryKeys[0];
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        if ($this->id_shop !== null) {
            return $this->id_shop;
        }

        $this->id_shop = \Context::getContext()->shop->id;

        return $this->id_shop;
    }

    /**
     * @return \DbQuery
     */
    private function getBaseQuery()
    {
        $queryBuilder = new \DbQuery();

        $queryBuilder->select($this->getTableName() . '.*')
            ->from($this->getTableName(false));

        return $queryBuilder;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return \DbQuery
     */
    private function _findAll(array $where, $limit = null, $offset = null)
    {
        $sql = $this->getBaseQuery();

        if ($this->hasMultiShopColumn()) {
            $where['id_shop'] = array_values(\Shop::getContextListShopID());
        }

        foreach ($where as $k => $v) {
            if (is_array($v)) {
                if (preg_match('/<|>|=/i', (string)$v[0])) {
                    $sql->where($this->getTableName() . '.' . $k . ' ' . $v[0]);
                    $sql->where($this->getTableName() . '.' . $k . ' ' . $v[1]);

                    continue;
                } else {
                    $whereValue = " IN (" . implode(",", $v) . ")";
                }
            } else {
                $whereValue = " = " . (is_numeric($v) ? $v : "'" . $v . "'");
            }

            if ($k !== "") {
                $sql->where($this->getTableName() . '.' . $k . ' ' . $whereValue);
            } else {
                $sql->where($v);
            }
        }

        $filterType = (int)\Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationFilter();

        if ($filterType !== 2 && $this->supportActiveFilter()) {
            $filter = explode('.', $this->getActiveFilter());

            $filterTable = \pSQL(_DB_PREFIX_ . $filter[0]);
            $filterField = \pSQL($filter[1]);

            $sql->innerJoin(
                $filter[0],
                null,
                $filterTable . '.' . $filterField . '=' . $filterType . ' AND ' . $filterTable . '.' . $this->getPrimaryKey() . '=' . $this->getTableName() . '.' . $this->getPrimaryKey()
            );
        }

        if ($this->hasRequestFilters()) {
            $sql = $this->addSqlRequestFilters($sql);
            $sql->groupBy($this->getPrimaryKey());
        }

        if ($limit !== null) {
            $sql->limit($limit, $offset);
        }

        return $sql;
    }

    /**
     * @return mixed[]|false
     * @param mixed[] $where
     * @param int|null $limit
     * @param int|null $offset
     */
    public function findAll($where, $limit = null, $offset = null)
    {
        $sql = $this->_findAll($where, $limit, $offset);

        return \Db::getInstance()->executeS($sql->build());
    }

    /**
     * @param string $query
     * @param int|null $id_lang
     * @param int|null $limit
     * @param bool $advancedSearch
     * @return mixed[]
     */
    public function findAllLike($query, $id_lang = null, $limit = null, $advancedSearch = false)
    {
        if ($limit === null && $query === "") {
            $limit = 200;
        }

        $searchRegex = '/';

        $searchQueryQuoted = preg_quote($query, '/');

        if (!$advancedSearch) {
            $searchRegex .= '(\b' . $searchQueryQuoted . '\b)';
        } else {
            $searchRegex .= '(' . $searchQueryQuoted . ')';
        }

        $searchRegex .= '/um';
        $whereRegexp = " LIKE '%" . str_replace('\\', '\\\\\\', $query) . "%' ";

        $where = [];

        if ($id_lang !== null) {
            $where['id_lang'] = $id_lang;
        }

        $whereOr = [];
        $fields = array_merge([$this->getPrimaryKey()], $this->getFields(false));

        foreach ($fields as $field) {
            $whereOr[] = $this->getTableName() . "." . $field . " " . $whereRegexp;
        }

        $where[''] = implode(' OR ', $whereOr);

        $sql = $this->_findAll($where, $limit);

        if ($id_lang !== null) {
            $sql->groupBy($this->getPrimaryKey());
        }

        $results = \Db::getInstance()->executeS($sql->build());

        return [
            'results' => $results,
            'regex' => $searchRegex
        ];
    }

    /**
     * @throws \Exception
     * @return mixed[]|false
     * @param mixed[] $where
     */
    public function findOne($where)
    {
        return $this->findAll($where, 1);
    }

    /**
     * @throws \Exception
     * @return mixed[]|false
     * @param int $id
     * @param mixed[] $where
     */
    public function findOneByPrimaryKey($id, $where = array())
    {
        return $this->findOne(array_merge(array(
            $this->getPrimaryKey() => $id
        ), $where));
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return null;
    }

    /**
     * @param string $string
     * @return string
     */
    public function l($string)
    {
        return \Translate::getModuleTranslation('dgcontenttranslation', $string, get_class($this));
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    /**
     * @throws \Exception
     */
    public function jsonSerialize()
    {
        if (!$this->isExist()) {
            return false;
        }

        $data = array(
            'type' => 'table',
            'name' => $this->getTableName(false),
            'name_with_prefix' => $this->getTableName(),
            'certified' => $this->isCertified(),
            'fields' => $this->getFields(),
            'multi_shop' => $this->hasMultiShopColumn(),
            'total_items' => $this->getTotalItems(),
            'exist' => $this->isExist(),
            'label' => $this->getLabel()
        );

        if (!empty($this->filters)) {
            $data['filters'] = $this->filters;
        }

        if (!empty($this->relatedItems)) {
            $data['relatedItems'] = $this->relatedItems;
        }

        if ($this->module !== false) {
            $data['module'] = $this->module;
        }

        return $data;
    }
}
