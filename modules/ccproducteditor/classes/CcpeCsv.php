<?php
/**
 * Exportación y lectura del CSV (separador ";", UTF-8 con BOM para que Excel lo abra bien).
 */
class CcpeCsv
{
    const DELIMITER = ';';

    /**
     * @param resource $handle
     * @param array $features [id_feature => nombre]
     * @param array $products filas de CcpeRepository::getProducts()
     * @param array $productFeatures ver CcpeRepository::getProductFeatures()
     * @param array $choices ver CcpeRepository::getChoices()
     * @param array $deletedTaxGroups
     */
    public static function export($handle, array $features, array $products, array $productFeatures, array $choices, array $deletedTaxGroups)
    {
        $definitions = CcpeFields::all();
        $choices['taxes'] += $deletedTaxGroups;

        fwrite($handle, "\xEF\xBB\xBF");

        $header = [CcpeFields::CSV_ID_HEADER];
        foreach ($definitions as $definition) {
            $header[] = $definition['label'];
        }
        foreach ($features as $idFeature => $name) {
            $header[] = CcpeFields::featureHeader($idFeature, $name);
        }
        fputcsv($handle, $header, self::DELIMITER);

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $line = [$idProduct];
            foreach ($definitions as $key => $definition) {
                $value = $product[$key];
                if ($definition['type'] === CcpeFields::TYPE_CHOICE) {
                    $value = isset($choices[$definition['choices']][(int) $value]) ? $choices[$definition['choices']][(int) $value] : '';
                } elseif ($definition['type'] === CcpeFields::TYPE_DECIMAL) {
                    $value = CcpeFields::formatDecimal($value);
                }
                $line[] = (string) $value;
            }
            foreach ($features as $idFeature => $name) {
                $line[] = empty($productFeatures[$idProduct][$idFeature])
                    ? CcpeFields::CSV_NULL
                    : implode(CcpeFields::CSV_MULTI_SEPARATOR, array_column($productFeatures[$idProduct][$idFeature], 'value'));
            }
            fputcsv($handle, $line, self::DELIMITER);
        }
    }

    /**
     * Lee el CSV y comprueba su estructura (cabeceras, nº de columnas, IDs). El contenido de cada
     * celda lo valida después CcpeValidator.
     *
     * @param string $path
     * @param array $features [id_feature => nombre]
     *
     * @return array ['rows' => [['line' => int, 'raw' => array]], 'errors' => string[]]
     */
    public static function read($path, array $features)
    {
        $content = (string) file_get_contents($path);
        if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
            $content = substr($content, 3);
        }
        // Excel en Windows guarda a veces en ANSI (Windows-1252)
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }
        if (trim($content) === '') {
            return ['rows' => [], 'errors' => ['El fichero está vacío.']];
        }

        $firstLine = strtok($content, "\r\n");
        $delimiter = self::DELIMITER;
        foreach ([',', "\t"] as $candidate) {
            if (substr_count($firstLine, $candidate) > substr_count($firstLine, $delimiter)) {
                $delimiter = $candidate;
            }
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $errors = [];
        $columns = self::parseHeader(fgetcsv($handle, 0, $delimiter) ?: [], $features, $errors);
        if ($errors) {
            fclose($handle);

            return ['rows' => [], 'errors' => $errors];
        }

        $rows = [];
        $seenIds = [];
        $lineNumber = 1;
        while (($cells = fgetcsv($handle, 0, $delimiter)) !== false) {
            ++$lineNumber;
            if (count(array_filter($cells, function ($cell) { return trim((string) $cell) !== ''; })) === 0) {
                continue;
            }
            if (count($cells) !== count($columns)) {
                $errors[] = 'Línea ' . $lineNumber . ': tiene ' . count($cells) . ' columnas y se esperaban ' . count($columns) . '.';
                continue;
            }

            $raw = ['id_product' => 0, 'fields' => [], 'features' => []];
            foreach ($columns as $index => $column) {
                $cell = (string) $cells[$index];
                if ($column['kind'] === 'id') {
                    $raw['id_product'] = trim($cell);
                } elseif ($column['kind'] === 'field') {
                    $raw['fields'][$column['key']] = $cell;
                } else {
                    $raw['features'][$column['key']] = $cell;
                }
            }

            if (!ctype_digit($raw['id_product'])) {
                $errors[] = 'Línea ' . $lineNumber . ': el ID "' . $raw['id_product'] . '" no es válido.';
                continue;
            }
            $raw['id_product'] = (int) $raw['id_product'];
            if (isset($seenIds[$raw['id_product']])) {
                $errors[] = 'Línea ' . $lineNumber . ': el producto con ID ' . $raw['id_product']
                    . ' está repetido (ya aparece en la línea ' . $seenIds[$raw['id_product']] . ').';
                continue;
            }
            $seenIds[$raw['id_product']] = $lineNumber;
            $rows[] = ['line' => $lineNumber, 'raw' => $raw];
        }
        fclose($handle);

        if (!$rows && !$errors) {
            $errors[] = 'El fichero no contiene ningún producto.';
        }

        return ['rows' => $rows, 'errors' => $errors];
    }

    /**
     * @return array [índice => ['kind' => id|field|feature, 'key' => string|int]]
     */
    private static function parseHeader(array $header, array $features, array &$errors)
    {
        $labels = [];
        foreach (CcpeFields::all() as $key => $definition) {
            $labels[Tools::strtolower($definition['label'])] = $key;
        }

        $columns = [];
        $seen = [];
        foreach ($header as $index => $title) {
            $title = trim((string) $title);
            $lower = Tools::strtolower($title);
            $idFeature = CcpeFields::parseFeatureHeader($title);

            if ($lower === Tools::strtolower(CcpeFields::CSV_ID_HEADER)) {
                $column = ['kind' => 'id', 'key' => 'id'];
            } elseif (isset($labels[$lower])) {
                $column = ['kind' => 'field', 'key' => $labels[$lower]];
            } elseif ($idFeature !== null && isset($features[$idFeature])) {
                $column = ['kind' => 'feature', 'key' => $idFeature];
            } else {
                $errors[] = 'Cabecera: la columna "' . $title . '" (columna ' . ($index + 1) . ') no se reconoce.'
                    . ($idFeature !== null ? ' No existe ninguna característica con ID ' . $idFeature . '.' : '');
                continue;
            }

            $seenKey = $column['kind'] . ':' . $column['key'];
            if (isset($seen[$seenKey])) {
                $errors[] = 'Cabecera: la columna "' . $title . '" está repetida.';
                continue;
            }
            $seen[$seenKey] = true;
            $columns[$index] = $column;
        }

        if (!isset($seen['id:id'])) {
            $errors[] = 'Cabecera: falta la columna obligatoria "' . CcpeFields::CSV_ID_HEADER . '".';
        }

        return $columns;
    }
}
