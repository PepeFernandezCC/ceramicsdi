<?php
/**
 * Aplica filas ya validadas (ver CcpeValidator) a los productos, por lotes.
 * Cada producto se guarda en su propia transacción: o se aplican todos sus cambios o ninguno.
 */
class CcpeUpdater
{
    const BATCH_SIZE = 50;

    /** @var int */
    private $idLang;

    /** @var int */
    private $idShop;

    /** @var array [id_product => fila actual] */
    private $products;

    /** @var array [id_product => [id_feature => [id_value => ['value', 'custom']]]] */
    private $productFeatures;

    /** @var array [id_product => int[] id_category] */
    private $productCategories;

    /** @var Db */
    private $db;

    public function __construct($idLang, $idShop, array $products, array $productFeatures, array $productCategories = [])
    {
        $this->productCategories = $productCategories;
        $this->idLang = (int) $idLang;
        $this->idShop = (int) $idShop;
        $this->products = $products;
        $this->productFeatures = $productFeatures;
        $this->db = Db::getInstance();
    }

    /**
     * @param array $rows filas normalizadas
     *
     * @return array ['updated' => int, 'unchanged' => int, 'errors' => [['id_product' => int, 'message' => string]]]
     */
    public function apply(array $rows)
    {
        $result = ['updated' => 0, 'unchanged' => 0, 'errors' => []];

        foreach (array_chunk($rows, self::BATCH_SIZE) as $batch) {
            @set_time_limit(300);

            foreach ($batch as $row) {
                try {
                    if ($this->applyRow($row)) {
                        ++$result['updated'];
                    } else {
                        ++$result['unchanged'];
                    }
                } catch (Throwable $e) {
                    $result['errors'][] = ['id_product' => $row['id_product'], 'message' => $e->getMessage()];
                }
            }

            // Libera la caché en memoria de ObjectModel entre lotes
            Cache::clean('*');
        }

        return $result;
    }

    /**
     * @return bool true si el producto tenía algún cambio
     */
    private function applyRow(array $row)
    {
        $idProduct = $row['id_product'];
        $current = $this->products[$idProduct];
        $definitions = CcpeFields::all();

        $this->db->execute('START TRANSACTION');
        try {
            $product = new Product($idProduct, false, null, $this->idShop);
            $productChanged = false;

            foreach ($row['fields'] as $key => $value) {
                if ($key === 'quantity' || $key === 'out_of_stock') {
                    continue;
                }
                if (!empty($definitions[$key]['lang'])) {
                    $fieldValue = &$product->{$key}[$this->idLang];
                } else {
                    $fieldValue = &$product->{$key};
                }
                $isDifferent = $definitions[$key]['type'] === CcpeFields::TYPE_DECIMAL
                    ? abs((float) $fieldValue - (float) $value) > 0.0000001
                    : (string) $fieldValue !== (string) $value;
                if ($isDifferent) {
                    $fieldValue = $value;
                    $productChanged = true;
                }
                unset($fieldValue);
            }

            if (isset($row['id_category_default']) && (int) $row['id_category_default'] !== (int) $product->id_category_default) {
                $product->id_category_default = (int) $row['id_category_default'];
                $productChanged = true;
            }

            // Las categorías se asocian antes de guardar para que la categoría por defecto ya esté entre ellas
            $categoriesChanged = false;
            if (isset($row['categories'])) {
                $currentCategories = isset($this->productCategories[$idProduct]) ? $this->productCategories[$idProduct] : [];
                if (!$this->sameIds($currentCategories, $row['categories'])) {
                    if (!$product->updateCategories($row['categories'])) {
                        throw new PrestaShopException('PrestaShop no ha podido actualizar las categorías.');
                    }
                    $categoriesChanged = true;
                }
            }

            if ($productChanged) {
                if (isset($row['fields']['id_supplier'])) {
                    $this->ensureProductSupplier($idProduct, (int) $row['fields']['id_supplier']);
                }
                if (!$product->update()) {
                    throw new PrestaShopException('PrestaShop no ha podido guardar el producto.');
                }
            }

            $stockChanged = false;
            if (isset($row['fields']['quantity']) && (int) $row['fields']['quantity'] !== (int) $current['quantity']) {
                StockAvailable::setQuantity($idProduct, 0, (int) $row['fields']['quantity'], $this->idShop);
                $stockChanged = true;
            }
            if (isset($row['fields']['out_of_stock']) && (int) $row['fields']['out_of_stock'] !== (int) $current['out_of_stock']) {
                StockAvailable::setProductOutOfStock($idProduct, (int) $row['fields']['out_of_stock'], $this->idShop);
                $stockChanged = true;
            }

            $featuresChanged = false;
            foreach ($row['features'] as $idFeature => $newIds) {
                $currentValues = isset($this->productFeatures[$idProduct][$idFeature]) ? $this->productFeatures[$idProduct][$idFeature] : [];
                if ($this->sameIds(array_keys($currentValues), $newIds)) {
                    continue;
                }
                $this->replaceFeatureValues($idProduct, $idFeature, $currentValues, $newIds);
                $featuresChanged = true;
            }

            // Product::update() ya lanza este hook; si solo cambiaron características o categorías lo lanzamos
            // para que módulos como ps_facetedsearch reindexen el producto.
            if (($featuresChanged || $categoriesChanged) && !$productChanged) {
                Hook::exec('actionProductUpdate', ['id_product' => $idProduct, 'product' => $product]);
            }

            $this->db->execute('COMMIT');
        } catch (Throwable $e) {
            $this->db->execute('ROLLBACK');
            throw $e;
        }

        return $productChanged || $stockChanged || $featuresChanged || $categoriesChanged;
    }

    private function replaceFeatureValues($idProduct, $idFeature, array $currentValues, array $newIds)
    {
        $this->db->delete('feature_product', 'id_product = ' . (int) $idProduct . ' AND id_feature = ' . (int) $idFeature);

        foreach ($newIds as $idValue) {
            $this->db->insert('feature_product', [
                'id_feature' => (int) $idFeature,
                'id_product' => (int) $idProduct,
                'id_feature_value' => (int) $idValue,
            ]);
        }

        // Un valor personalizado que ya no usa ningún producto se borra, igual que hace el BO de PrestaShop
        foreach ($currentValues as $idValue => $info) {
            if ($info['custom'] && !in_array($idValue, $newIds, true)) {
                $stillUsed = (int) $this->db->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'feature_product`
                    WHERE id_feature_value = ' . (int) $idValue);
                if (!$stillUsed) {
                    (new FeatureValue($idValue))->delete();
                }
            }
        }
    }

    private function ensureProductSupplier($idProduct, $idSupplier)
    {
        if ($idSupplier <= 0 || ProductSupplier::getIdByProductAndSupplier($idProduct, 0, $idSupplier)) {
            return;
        }

        $productSupplier = new ProductSupplier();
        $productSupplier->id_product = (int) $idProduct;
        $productSupplier->id_product_attribute = 0;
        $productSupplier->id_supplier = (int) $idSupplier;
        $productSupplier->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $productSupplier->product_supplier_reference = '';
        $productSupplier->product_supplier_price_te = 0;
        $productSupplier->add();
    }

    private function sameIds(array $a, array $b)
    {
        $a = array_map('intval', $a);
        $b = array_map('intval', $b);
        sort($a);
        sort($b);

        return $a === $b;
    }
}
