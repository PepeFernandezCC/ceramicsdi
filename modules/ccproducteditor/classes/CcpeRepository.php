<?php
/**
 * Lecturas de base de datos para el editor (productos, características y listas de opciones).
 */
class CcpeRepository
{
    const MODE_ALL = 'all';
    const MODE_CATEGORY = 'category';
    const MODE_RANGE = 'range';
    const MODE_CATEGORY_RANGE = 'category_range';

    /** @var int */
    private $idLang;

    /** @var int */
    private $idShop;

    /** @var Db */
    private $db;

    public function __construct($idLang, $idShop)
    {
        $this->idLang = (int) $idLang;
        $this->idShop = (int) $idShop;
        $this->db = Db::getInstance();
    }

    /**
     * @return array [id_feature => nombre] en el orden de posición del BO
     */
    public function getFeatures()
    {
        $rows = $this->db->executeS('
            SELECT f.id_feature, fl.name
            FROM `' . _DB_PREFIX_ . 'feature` f
            INNER JOIN `' . _DB_PREFIX_ . 'feature_shop` fs ON fs.id_feature = f.id_feature AND fs.id_shop = ' . $this->idShop . '
            LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON fl.id_feature = f.id_feature AND fl.id_lang = ' . $this->idLang . '
            ORDER BY f.position ASC, f.id_feature ASC');

        $features = [];
        foreach ($rows ?: [] as $row) {
            $features[(int) $row['id_feature']] = (string) $row['name'];
        }

        return $features;
    }

    /**
     * Valores predefinidos (no personalizados) de cada característica.
     *
     * @return array [id_feature => [id_feature_value => texto]]
     */
    public function getPredefinedValues()
    {
        $rows = $this->db->executeS('
            SELECT fv.id_feature, fv.id_feature_value, fvl.value
            FROM `' . _DB_PREFIX_ . 'feature_value` fv
            LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                ON fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = ' . $this->idLang . '
            WHERE fv.custom = 0
            ORDER BY fvl.value ASC');

        $values = [];
        foreach ($rows ?: [] as $row) {
            $values[(int) $row['id_feature']][(int) $row['id_feature_value']] = (string) $row['value'];
        }

        return $values;
    }

    /**
     * Valores asignados a cada producto (incluye personalizados y multivalor).
     *
     * @param int[] $productIds
     *
     * @return array [id_product => [id_feature => [id_feature_value => ['value' => string, 'custom' => bool]]]]
     */
    public function getProductFeatures(array $productIds)
    {
        $result = [];
        foreach (array_chunk(array_map('intval', $productIds), 1000) as $chunk) {
            $rows = $this->db->executeS('
                SELECT fp.id_product, fp.id_feature, fp.id_feature_value, fv.custom, fvl.value
                FROM `' . _DB_PREFIX_ . 'feature_product` fp
                INNER JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON fv.id_feature_value = fp.id_feature_value
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                    ON fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = ' . $this->idLang . '
                WHERE fp.id_product IN (' . implode(',', $chunk) . ')
                ORDER BY fp.id_feature_value ASC');

            foreach ($rows ?: [] as $row) {
                $result[(int) $row['id_product']][(int) $row['id_feature']][(int) $row['id_feature_value']] = [
                    'value' => (string) $row['value'],
                    'custom' => (bool) $row['custom'],
                ];
            }
        }

        return $result;
    }

    /**
     * Listas de opciones de los campos desplegables.
     *
     * @return array ['taxes' => [id => nombre], 'manufacturers' => ..., 'suppliers' => ..., 'availability' => ...]
     */
    public function getChoices()
    {
        $taxes = [0 => 'Sin impuestos'];
        foreach ($this->db->executeS('SELECT id_tax_rules_group, name FROM `' . _DB_PREFIX_ . 'tax_rules_group`
            WHERE deleted = 0 ORDER BY name ASC') ?: [] as $row) {
            $taxes[(int) $row['id_tax_rules_group']] = $row['name'];
        }

        $manufacturers = [0 => ''];
        foreach ($this->db->executeS('SELECT id_manufacturer, name FROM `' . _DB_PREFIX_ . 'manufacturer`
            ORDER BY name ASC') ?: [] as $row) {
            $manufacturers[(int) $row['id_manufacturer']] = $row['name'];
        }

        $suppliers = [0 => ''];
        foreach ($this->db->executeS('SELECT id_supplier, name FROM `' . _DB_PREFIX_ . 'supplier`
            ORDER BY name ASC') ?: [] as $row) {
            $suppliers[(int) $row['id_supplier']] = $row['name'];
        }

        return [
            'taxes' => $taxes,
            'manufacturers' => $manufacturers,
            'suppliers' => $suppliers,
            'availability' => CcpeFields::availabilityChoices(),
        ];
    }

    /**
     * Nombre de grupos de impuestos borrados que todavía usan productos (para poder mostrarlos).
     *
     * @return array [id => nombre]
     */
    public function getDeletedTaxGroups()
    {
        $groups = [];
        foreach ($this->db->executeS('SELECT id_tax_rules_group, name FROM `' . _DB_PREFIX_ . 'tax_rules_group`
            WHERE deleted = 1') ?: [] as $row) {
            $groups[(int) $row['id_tax_rules_group']] = $row['name'] . ' (eliminada)';
        }

        return $groups;
    }

    /**
     * Árbol de categorías aplanado para el desplegable del filtro.
     *
     * @return array [['id' => int, 'label' => string]] con la profundidad indicada con guiones
     */
    public function getCategoryTree()
    {
        $rows = $this->db->executeS('
            SELECT c.id_category, c.level_depth, cl.name
            FROM `' . _DB_PREFIX_ . 'category` c
            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON cs.id_category = c.id_category AND cs.id_shop = ' . $this->idShop . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON cl.id_category = c.id_category AND cl.id_lang = ' . $this->idLang . ' AND cl.id_shop = ' . $this->idShop . '
            WHERE c.is_root_category = 0 AND c.id_parent > 0
            ORDER BY c.nleft ASC');

        $tree = [];
        foreach ($rows ?: [] as $row) {
            $depth = max(0, (int) $row['level_depth'] - 2);
            $tree[] = ['id' => (int) $row['id_category'], 'label' => str_repeat('— ', $depth) . $row['name']];
        }

        return $tree;
    }

    /**
     * @param array $filters ['mode', 'id_category', 'subcategories', 'id_from', 'id_to']
     *
     * @return int
     */
    public function countProducts(array $filters)
    {
        return (int) $this->db->getValue('
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON ps.id_product = p.id_product AND ps.id_shop = ' . $this->idShop . '
            WHERE ' . $this->buildWhere($filters));
    }

    /**
     * @param array $filters
     * @param int $offset
     * @param int $limit 0 = sin límite
     *
     * @return array filas con los campos de CcpeFields::all() más id_product
     */
    public function getProducts(array $filters, $offset = 0, $limit = 0)
    {
        return $this->selectProducts($this->buildWhere($filters), $offset, $limit);
    }

    /**
     * @param int[] $productIds
     *
     * @return array [id_product => fila]
     */
    public function getProductsByIds(array $productIds)
    {
        $result = [];
        foreach (array_chunk(array_map('intval', $productIds), 1000) as $chunk) {
            foreach ($this->selectProducts('p.id_product IN (' . implode(',', $chunk) . ')') as $row) {
                $result[(int) $row['id_product']] = $row;
            }
        }

        return $result;
    }

    private function selectProducts($where, $offset = 0, $limit = 0)
    {
        $sql = '
            SELECT p.id_product, ps.active, p.reference, pl.name, ps.price, ps.id_tax_rules_group,
                IFNULL(sa.quantity, 0) AS quantity, p.id_manufacturer, p.id_supplier,
                IFNULL(sa.out_of_stock, ' . CcpeFields::OUT_OF_STOCK_DEFAULT . ') AS out_of_stock,
                pl.available_now, pl.available_later, p.weight, pl.link_rewrite, p.ean13,
                ps.cache_default_attribute
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON ps.id_product = p.id_product AND ps.id_shop = ' . $this->idShop . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON pl.id_product = p.id_product AND pl.id_lang = ' . $this->idLang . ' AND pl.id_shop = ' . $this->idShop . '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON sa.id_product = p.id_product AND sa.id_product_attribute = 0'
                . StockAvailable::addSqlShopRestriction(null, $this->idShop, 'sa') . '
            WHERE ' . $where . '
            ORDER BY p.id_product ASC';

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        }

        return $this->db->executeS($sql) ?: [];
    }

    private function buildWhere(array $filters)
    {
        $conditions = ['1'];
        $mode = $filters['mode'];

        if (in_array($mode, [self::MODE_CATEGORY, self::MODE_CATEGORY_RANGE], true)) {
            $categoryIds = [(int) $filters['id_category']];
            if (!empty($filters['subcategories'])) {
                $category = new Category((int) $filters['id_category']);
                $categoryIds = array_map('intval', array_column($this->db->executeS('
                    SELECT id_category FROM `' . _DB_PREFIX_ . 'category`
                    WHERE nleft >= ' . (int) $category->nleft . ' AND nright <= ' . (int) $category->nright) ?: [], 'id_category'));
            }
            $conditions[] = 'EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                WHERE cp.id_product = p.id_product AND cp.id_category IN (' . implode(',', $categoryIds ?: [0]) . '))';
        }

        if (in_array($mode, [self::MODE_RANGE, self::MODE_CATEGORY_RANGE], true)) {
            if ((int) $filters['id_from'] > 0) {
                $conditions[] = 'p.id_product >= ' . (int) $filters['id_from'];
            }
            if ((int) $filters['id_to'] > 0) {
                $conditions[] = 'p.id_product <= ' . (int) $filters['id_to'];
            }
        }

        return implode(' AND ', $conditions);
    }
}
