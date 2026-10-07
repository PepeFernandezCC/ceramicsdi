<?php
/**
 * Pantalla del editor masivo. Las acciones se llaman por AJAX (ajax=1&action=...):
 *  - loadProducts: listado paginado
 *  - saveProducts: guarda los cambios hechos en el listado
 *  - exportCsv / importCsv: CSV
 *  - downloadLog: descarga el log de una importación
 */
class AdminCcProductEditorController extends ModuleAdminController
{
    const PER_PAGE_OPTIONS = [50, 100, 250, 500, 0];

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS($this->module->getPathUri() . 'views/css/admin.css?v=' . filemtime($this->module->getLocalPath() . 'views/css/admin.css'), 'all', null, false);
        $this->addJS($this->module->getPathUri() . 'views/js/admin.js?v=' . filemtime($this->module->getLocalPath() . 'views/js/admin.js'));
    }

    public function initContent()
    {
        $idLang = (int) $this->context->employee->id_lang;
        $repository = new CcpeRepository($idLang, (int) $this->context->shop->id);

        $this->context->smarty->assign([
            'ccpe_languages' => Language::getLanguages(false),
            'ccpe_id_lang' => $idLang,
            'ccpe_categories' => $repository->getCategoryTree(),
            'ccpe_per_page_options' => self::PER_PAGE_OPTIONS,
            'ccpe_can_edit' => $this->access('edit'),
            'ccpe_config' => json_encode([
                'url' => $this->context->link->getAdminLink($this->controller_name),
                'productUrl' => str_replace('999999999', '{id}', $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 999999999, 'updateproduct' => 1])),
                'fields' => CcpeFields::all(),
                'multiSeparator' => CcpeFields::CSV_MULTI_SEPARATOR,
                'canEdit' => (bool) $this->access('edit'),
            ]),
        ]);

        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/editor.tpl');

        parent::initContent();
    }

    public function ajaxProcessLoadProducts()
    {
        $idLang = $this->getLangFromRequest();
        $repository = new CcpeRepository($idLang, (int) $this->context->shop->id);
        $filters = $this->getFiltersFromRequest();

        $perPage = (int) Tools::getValue('per_page', 100);
        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 100;
        }
        $total = $repository->countProducts($filters);
        $pages = $perPage > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min(max(1, (int) Tools::getValue('page', 1)), $pages);

        $products = $repository->getProducts($filters, ($page - 1) * $perPage, $perPage);
        $productFeatures = $repository->getProductFeatures(array_column($products, 'id_product'));

        $rows = [];
        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $row = [
                'id' => $idProduct,
                'has_combinations' => (int) $product['cache_default_attribute'] > 0,
                'features' => new stdClass(),
            ];
            foreach (CcpeFields::all() as $key => $definition) {
                $row[$key] = $definition['type'] === CcpeFields::TYPE_DECIMAL
                    ? CcpeFields::formatDecimal($product[$key])
                    : (string) $product[$key];
            }
            $features = [];
            foreach (isset($productFeatures[$idProduct]) ? $productFeatures[$idProduct] : [] as $idFeature => $values) {
                foreach ($values as $idValue => $info) {
                    $features[$idFeature][] = [$idValue, $info['value'], $info['custom']];
                }
            }
            if ($features) {
                $row['features'] = $features;
            }
            $rows[] = $row;
        }

        $choices = $repository->getChoices();
        $choices['taxes'] += $repository->getDeletedTaxGroups();

        $this->sendJson([
            'success' => true,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'rows' => $rows,
            'features' => $this->toPairs($repository->getFeatures()),
            'featureValues' => array_map([$this, 'toPairs'], $repository->getPredefinedValues()),
            'choices' => array_map([$this, 'toPairs'], $choices),
        ]);
    }

    public function ajaxProcessSaveProducts()
    {
        $this->denyIfReadOnly();

        $idLang = $this->getLangFromRequest();
        $payload = json_decode((string) Tools::getValue('payload'), true);
        if (!is_array($payload) || !$payload) {
            $this->sendJson(['success' => false, 'message' => 'No hay cambios que guardar.']);
        }

        $rawRows = [];
        foreach ($payload as $item) {
            $rawRows[] = [
                'line' => null,
                'raw' => [
                    'id_product' => isset($item['id']) ? (int) $item['id'] : 0,
                    'fields' => isset($item['fields']) && is_array($item['fields']) ? $item['fields'] : [],
                    'features' => isset($item['features']) && is_array($item['features']) ? $item['features'] : [],
                ],
            ];
        }

        list($rows, $errors, $products, $productFeatures) = $this->validateRows($rawRows, $idLang, CcpeValidator::SOURCE_GRID);
        if ($errors) {
            $this->sendJson([
                'success' => false,
                'message' => 'No se ha guardado nada, corrige estos errores:',
                'errors' => $errors,
            ]);
        }

        $updater = new CcpeUpdater($idLang, (int) $this->context->shop->id, $products, $productFeatures);
        $result = $updater->apply($rows);

        $this->sendJson([
            'success' => !$result['errors'],
            'message' => $result['errors']
                ? 'Se han guardado ' . $result['updated'] . ' productos, pero algunos han fallado:'
                : 'Cambios guardados: ' . $result['updated'] . ' productos actualizados.',
            'errors' => array_map(function ($error) {
                return 'ID ' . $error['id_product'] . ': ' . $error['message'];
            }, $result['errors']),
        ]);
    }

    public function ajaxProcessExportCsv()
    {
        $idLang = $this->getLangFromRequest();
        $repository = new CcpeRepository($idLang, (int) $this->context->shop->id);
        $products = $repository->getProducts($this->getFiltersFromRequest());

        $filename = 'productos_' . Language::getIsoById($idLang) . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store');

        $output = fopen('php://output', 'w');
        CcpeCsv::export(
            $output,
            $repository->getFeatures(),
            $products,
            $repository->getProductFeatures(array_column($products, 'id_product')),
            $repository->getChoices(),
            $repository->getDeletedTaxGroups()
        );
        fclose($output);
        exit;
    }

    public function ajaxProcessImportCsv()
    {
        $this->denyIfReadOnly();
        @set_time_limit(0);

        $idLang = $this->getLangFromRequest();
        $file = isset($_FILES['csv']) ? $_FILES['csv'] : null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            $this->sendJson(['success' => false, 'message' => 'No se ha recibido ningún fichero CSV.']);
        }

        $logHeader = [
            'Fichero: ' . $file['name'],
            'Fecha: ' . date('Y-m-d H:i:s'),
            'Idioma: ' . Language::getIsoById($idLang),
            'Empleado: ' . $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
        ];

        // 1. Lectura completa y validación de formato: si hay un solo error no se actualiza nada
        $repository = new CcpeRepository($idLang, (int) $this->context->shop->id);
        $csv = CcpeCsv::read($file['tmp_name'], $repository->getFeatures());
        $errors = $csv['errors'];
        $rows = [];
        $products = $productFeatures = [];
        if ($csv['rows']) {
            list($rows, $rowErrors, $products, $productFeatures) = $this->validateRows($csv['rows'], $idLang, CcpeValidator::SOURCE_CSV);
            $errors = array_merge($errors, $rowErrors);
        }

        if ($errors) {
            natsort($errors); // "Línea 2" antes que "Línea 10"
            $log = CcpeLog::write(CcpeLog::TYPE_VALIDATION, $logHeader, $errors);
            $this->sendJson([
                'success' => false,
                'message' => 'Han ocurrido uno o varios errores que hay que solucionar antes de su importación, puede verlos en el siguiente informe.',
                'errorCount' => count($errors),
                'logUrl' => $this->getLogUrl($log),
            ]);
        }

        // 2. Actualización por lotes
        $updater = new CcpeUpdater($idLang, (int) $this->context->shop->id, $products, $productFeatures);
        $result = $updater->apply($rows);
        $summary = 'Productos en el fichero: ' . count($rows) . ' | actualizados: ' . $result['updated']
            . ' | sin cambios: ' . $result['unchanged'] . ' | con incidencias: ' . count($result['errors']);

        if ($result['errors']) {
            $lines = array_map(function ($error) {
                return 'ID ' . $error['id_product'] . ': ' . $error['message'];
            }, $result['errors']);
            $log = CcpeLog::write(CcpeLog::TYPE_UPDATE, array_merge($logHeader, [$summary]), $lines);
            $this->sendJson([
                'success' => false,
                'message' => 'Han ocurrido las siguientes incidencias actualizando sus productos.',
                'summary' => $summary,
                'logUrl' => $this->getLogUrl($log),
            ]);
        }

        $this->sendJson(['success' => true, 'message' => 'Éxito en la importación.', 'summary' => $summary]);
    }

    public function ajaxProcessDownloadLog()
    {
        $path = CcpeLog::path(Tools::getValue('file'));
        if (!$path) {
            header('HTTP/1.1 404 Not Found');
            exit('Log no encontrado.');
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        readfile($path);
        exit;
    }

    /**
     * Carga el estado actual de los productos implicados y valida cada fila.
     *
     * @param array $rawRows [['line' => int|null, 'raw' => array]]
     *
     * @return array [filas normalizadas, errores, productos actuales, características actuales]
     */
    private function validateRows(array $rawRows, $idLang, $source)
    {
        $repository = new CcpeRepository($idLang, (int) $this->context->shop->id);
        $ids = array_map(function ($row) { return (int) $row['raw']['id_product']; }, $rawRows);
        $products = $repository->getProductsByIds($ids);
        $productFeatures = $repository->getProductFeatures(array_keys($products));

        $validator = new CcpeValidator(
            $repository->getFeatures(),
            $repository->getPredefinedValues(),
            $repository->getChoices(),
            $repository->getDeletedTaxGroups(),
            $products,
            $productFeatures
        );

        $rows = [];
        $errors = [];
        foreach ($rawRows as $item) {
            list($normalized, $rowErrors) = $validator->validateRow($item['raw'], $source);
            $prefix = ($item['line'] !== null ? 'Línea ' . $item['line'] . ' ' : '') . '(ID ' . (int) $item['raw']['id_product'] . '): ';
            foreach ($rowErrors as $error) {
                $errors[] = $prefix . $error;
            }
            if (!$rowErrors) {
                $rows[] = $normalized;
            }
        }

        return [$rows, $errors, $products, $productFeatures];
    }

    private function getFiltersFromRequest()
    {
        $mode = (string) Tools::getValue('mode', CcpeRepository::MODE_ALL);
        if (!in_array($mode, [CcpeRepository::MODE_ALL, CcpeRepository::MODE_CATEGORY, CcpeRepository::MODE_RANGE, CcpeRepository::MODE_CATEGORY_RANGE], true)) {
            $mode = CcpeRepository::MODE_ALL;
        }

        return [
            'mode' => $mode,
            'id_category' => (int) Tools::getValue('id_category'),
            'subcategories' => (bool) Tools::getValue('subcategories'),
            'id_from' => (int) Tools::getValue('id_from'),
            'id_to' => (int) Tools::getValue('id_to'),
        ];
    }

    private function getLangFromRequest()
    {
        $idLang = (int) Tools::getValue('id_lang');

        return Language::getLanguage($idLang) ? $idLang : (int) $this->context->employee->id_lang;
    }

    private function getLogUrl($file)
    {
        return $this->context->link->getAdminLink($this->controller_name)
            . '&ajax=1&action=downloadLog&file=' . urlencode($file);
    }

    private function denyIfReadOnly()
    {
        if (!$this->access('edit')) {
            $this->sendJson(['success' => false, 'message' => 'No tienes permiso para modificar productos.']);
        }
    }

    /** Convierte [id => texto] en [[id, texto]] para conservar el orden en JSON */
    private function toPairs(array $map)
    {
        $pairs = [];
        foreach ($map as $id => $label) {
            $pairs[] = [$id, $label];
        }

        return $pairs;
    }

    private function sendJson(array $data)
    {
        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode($data));
    }
}
