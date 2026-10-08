<?php
/**
 * Valida y normaliza las filas recibidas del listado (ids) o del CSV (textos).
 *
 * Fila de entrada: ['id_product' => int, 'fields' => [campo => string], 'features' => [id_feature => ids[] | string],
 *                   opcional (solo listado): 'categories' => ids[], 'id_category_default' => id|null]
 * Fila normalizada: ['id_product' => int, 'fields' => [campo => valor], 'features' => [id_feature => int[]],
 *                   y si venían categorías: 'categories' => int[], 'id_category_default' => int]
 */
class CcpeValidator
{
    const SOURCE_GRID = 'grid';
    const SOURCE_CSV = 'csv';

    /** @var array [id_feature => nombre] */
    private $features;

    /** @var array [id_feature => [id_feature_value => texto]] */
    private $predefinedValues;

    /** @var array [id_feature => [texto en minúsculas => id_feature_value]] */
    private $predefinedByText = [];

    /** @var array ver CcpeRepository::getChoices() */
    private $choices;

    /** @var array [id => nombre] grupos de impuestos borrados (solo válidos si el producto ya los tiene) */
    private $deletedTaxGroups;

    /** @var array [id_product => fila actual] */
    private $products;

    /** @var array [id_product => [id_feature => [id_value => ['value', 'custom']]]] */
    private $productFeatures;

    /** @var array [id_category => ['name', 'path']] categorías asignables */
    private $categories;

    public function __construct(array $features, array $predefinedValues, array $choices, array $deletedTaxGroups, array $products, array $productFeatures, array $categories = [])
    {
        $this->categories = $categories;
        $this->features = $features;
        $this->predefinedValues = $predefinedValues;
        $this->choices = $choices;
        $this->deletedTaxGroups = $deletedTaxGroups;
        $this->products = $products;
        $this->productFeatures = $productFeatures;

        foreach ($predefinedValues as $idFeature => $values) {
            foreach ($values as $idValue => $text) {
                $key = $this->normalizeText($text);
                // Si hay textos duplicados en una característica nos quedamos con el primero (id más bajo)
                if (!isset($this->predefinedByText[$idFeature][$key]) || $idValue < $this->predefinedByText[$idFeature][$key]) {
                    $this->predefinedByText[$idFeature][$key] = $idValue;
                }
            }
        }
    }

    /**
     * @param array $raw
     * @param string $source self::SOURCE_GRID | self::SOURCE_CSV
     *
     * @return array [fila normalizada, string[] errores]
     */
    public function validateRow(array $raw, $source)
    {
        $idProduct = (int) $raw['id_product'];
        if (!isset($this->products[$idProduct])) {
            return [null, ['El producto con ID ' . $idProduct . ' no existe.']];
        }

        $errors = [];
        $normalized = ['id_product' => $idProduct, 'fields' => [], 'features' => []];
        $definitions = CcpeFields::all();

        foreach ($raw['fields'] as $key => $value) {
            if (!isset($definitions[$key])) {
                $errors[] = 'Campo desconocido "' . $key . '".';
                continue;
            }
            $error = null;
            $result = $this->validateField($idProduct, $key, $definitions[$key], (string) $value, $source, $error);
            if ($error !== null) {
                $errors[] = $definitions[$key]['label'] . ': ' . $error;
            } else {
                $normalized['fields'][$key] = $result;
            }
        }

        foreach ($raw['features'] as $idFeature => $value) {
            $idFeature = (int) $idFeature;
            if (!isset($this->features[$idFeature])) {
                $errors[] = 'La característica con ID ' . $idFeature . ' no existe.';
                continue;
            }
            $featureErrors = [];
            $ids = $source === self::SOURCE_CSV
                ? $this->resolveFeatureText($idProduct, $idFeature, (string) $value, $featureErrors)
                : $this->resolveFeatureIds($idProduct, $idFeature, (array) $value, $featureErrors);
            if ($featureErrors) {
                $errors = array_merge($errors, $featureErrors);
            } else {
                $normalized['features'][$idFeature] = $ids;
            }
        }

        if (isset($raw['categories'])) {
            $categoryErrors = [];
            list($ids, $idDefault) = $this->resolveCategories($idProduct, (array) $raw['categories'], $raw['id_category_default'], $categoryErrors);
            if ($categoryErrors) {
                $errors = array_merge($errors, $categoryErrors);
            } else {
                $normalized['categories'] = $ids;
                $normalized['id_category_default'] = $idDefault;
            }
        }

        return [$normalized, $errors];
    }

    /**
     * @param mixed $idDefault id de la nueva categoría por defecto, o null para mantener la actual
     *
     * @return array [int[] ids, int id categoría por defecto]
     */
    private function resolveCategories($idProduct, array $ids, $idDefault, array &$errors)
    {
        $result = [];
        foreach ($ids as $id) {
            if (!ctype_digit((string) $id) || !isset($this->categories[(int) $id])) {
                $errors[] = 'La categoría con ID ' . $id . ' no existe.';
                continue;
            }
            $result[] = (int) $id;
        }
        $result = array_values(array_unique($result));
        sort($result);

        if (!$result && !$errors) {
            $errors[] = 'El producto debe pertenecer al menos a una categoría.';
        }

        $idDefault = $idDefault === null || $idDefault === '' ? (int) $this->products[$idProduct]['id_category_default'] : (int) $idDefault;
        if ($result && !in_array($idDefault, $result, true)) {
            $name = isset($this->categories[$idDefault]) ? $this->categories[$idDefault]['name'] : 'ID ' . $idDefault;
            $errors[] = 'La categoría por defecto «' . $name . '» no está entre las categorías del producto. Añádela o elige otra por defecto (☆).';
        }

        return [$result, $idDefault];
    }

    private function validateField($idProduct, $key, array $definition, $value, $source, &$error)
    {
        $value = trim($value);

        switch ($definition['type']) {
            case CcpeFields::TYPE_BOOL:
                if ($value !== '0' && $value !== '1') {
                    $error = 'debe ser 1 (activo) o 0 (desactivado), se recibió "' . $value . '".';
                }

                return (int) $value;

            case CcpeFields::TYPE_DECIMAL:
                if (!preg_match('/^\d+(\.\d{1,6})?$/', $value)) {
                    $error = '"' . $value . '" no es un número válido (usa "." como separador decimal, máximo 6 decimales, sin separador de miles).';
                }

                return $value;

            case CcpeFields::TYPE_INT:
                if (!preg_match('/^-?\d+$/', $value)) {
                    $error = '"' . $value . '" no es un número entero.';
                } elseif ($key === 'quantity' && (int) $this->products[$idProduct]['cache_default_attribute'] > 0
                    && (int) $value !== (int) $this->products[$idProduct]['quantity']) {
                    $error = 'el producto tiene combinaciones, la cantidad se debe editar por combinación.';
                }

                return (int) $value;

            case CcpeFields::TYPE_LINK_REWRITE:
                $value = Tools::strtolower($value);
                if ($value === '') {
                    $error = 'no puede estar vacía.';
                } elseif (Tools::strlen($value) > $definition['max']) {
                    $error = 'supera los ' . $definition['max'] . ' caracteres.';
                } elseif (!Validate::isLinkRewrite($value)) {
                    $error = '"' . $value . '" contiene caracteres no permitidos (usa letras, números y guiones).';
                }

                return $value;

            case CcpeFields::TYPE_EAN13:
                if (Tools::strlen($value) > 13 || !Validate::isEan13($value)) {
                    $error = '"' . $value . '" no es un EAN13 válido (hasta 13 dígitos, sin notación científica).';
                }

                return $value;

            case CcpeFields::TYPE_CHOICE:
                return $this->resolveChoice($idProduct, $key, $definition['choices'], $value, $source, $error);

            default: // TYPE_TEXT
                if (!empty($definition['required']) && $value === '') {
                    $error = 'no puede estar vacío.';
                } elseif (Tools::strlen($value) > $definition['max']) {
                    $error = 'supera los ' . $definition['max'] . ' caracteres.';
                } elseif ($value !== '' && !call_user_func(['Validate', $definition['validate']], $value)) {
                    $error = '"' . $value . '" contiene caracteres no permitidos (por ejemplo < > ; = # { }).';
                }

                return $value;
        }
    }

    private function resolveChoice($idProduct, $key, $listName, $value, $source, &$error)
    {
        $options = $this->choices[$listName];
        $currentId = (int) $this->products[$idProduct][$key];
        if ($listName === 'taxes' && isset($this->deletedTaxGroups[$currentId])) {
            $options[$currentId] = $this->deletedTaxGroups[$currentId];
        }

        if ($source === self::SOURCE_GRID) {
            if (!ctype_digit($value) || !array_key_exists((int) $value, $options)) {
                $error = 'la opción con ID "' . $value . '" no existe.';
            }

            return (int) $value;
        }

        // CSV: se compara por texto. Si coincide con la opción actual del producto, se mantiene tal cual.
        $wanted = $this->normalizeText($value);
        if (isset($options[$currentId]) && $this->normalizeText($options[$currentId]) === $wanted) {
            return $currentId;
        }
        $matches = [];
        foreach ($options as $id => $label) {
            if ($this->normalizeText($label) === $wanted) {
                $matches[] = (int) $id;
            }
        }
        if ($listName === 'availability' && !$matches && ctype_digit($value) && isset($options[(int) $value])) {
            $matches[] = (int) $value;
        }

        if (count($matches) === 1) {
            return $matches[0];
        }
        $error = $matches
            ? 'el valor "' . $value . '" es ambiguo, existen varias opciones con ese nombre.'
            : 'el valor "' . $value . '" no existe entre las opciones disponibles.';

        return null;
    }

    private function resolveFeatureIds($idProduct, $idFeature, array $ids, array &$errors)
    {
        $allowed = isset($this->predefinedValues[$idFeature]) ? array_keys($this->predefinedValues[$idFeature]) : [];
        if (isset($this->productFeatures[$idProduct][$idFeature])) {
            $allowed = array_merge($allowed, array_keys($this->productFeatures[$idProduct][$idFeature]));
        }

        $result = [];
        foreach ($ids as $id) {
            if (!ctype_digit((string) $id) || !in_array((int) $id, $allowed, true)) {
                $errors[] = 'El valor con ID ' . $id . ' no pertenece a la característica "' . $this->features[$idFeature] . '".';
                continue;
            }
            $result[] = (int) $id;
        }

        return array_values(array_unique($result));
    }

    private function resolveFeatureText($idProduct, $idFeature, $value, array &$errors)
    {
        $value = trim($value);
        if ($value === CcpeFields::CSV_NULL) {
            return [];
        }
        if ($value === '') {
            $errors[] = 'La característica "' . $this->features[$idFeature] . '" está vacía. Escribe ' . CcpeFields::CSV_NULL . ' si el producto no debe tenerla.';

            return [];
        }

        // Valores que ya tiene el producto (incluidos los personalizados) tienen prioridad sobre los predefinidos
        $current = [];
        if (isset($this->productFeatures[$idProduct][$idFeature])) {
            foreach ($this->productFeatures[$idProduct][$idFeature] as $idValue => $info) {
                $current[$this->normalizeText($info['value'])] = $idValue;
            }
        }

        $result = [];
        foreach (explode(CcpeFields::CSV_MULTI_SEPARATOR, $value) as $text) {
            $key = $this->normalizeText($text);
            if (isset($current[$key])) {
                $result[] = $current[$key];
            } elseif (isset($this->predefinedByText[$idFeature][$key])) {
                $result[] = $this->predefinedByText[$idFeature][$key];
            } else {
                $errors[] = 'El valor : ' . trim($text) . ' de la característica : ' . $this->features[$idFeature]
                    . ' no existe, por favor créalo y vuelve a intentarlo.';
            }
        }

        return array_values(array_unique($result));
    }

    private function normalizeText($text)
    {
        return Tools::strtolower(trim(preg_replace('/\s+/u', ' ', (string) $text)));
    }
}
