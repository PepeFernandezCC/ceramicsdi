<?php
/**
 * Ceramic Connection - Imagenes promocionales de producto.
 *
 * Permite subir, desde la ficha de producto del admin, dos imagenes
 * adicionales por producto (vertical y horizontal) que NO se muestran
 * en el front. Se guardan en su propia tabla para poder exportarlas
 * mas adelante al feed de Channable.
 *
 * Prestashop 1.7 / 8.x module.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcPromoImages extends Module
{
    const TIPOS = array('vertical', 'horizontal');

    const ALLOWED_EXT = array(
        'jpg' => array('image/jpeg', 'image/pjpeg'),
        'jpeg' => array('image/jpeg', 'image/pjpeg'),
        'png' => array('image/png'),
        'webp' => array('image/webp'),
    );

    const MAX_FILE_SIZE = 8388608; // 8 MB

    public function __construct()
    {
        $this->name = 'ccpromoimages';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Imagenes promocionales de producto');
        $this->description = $this->l('Sube una imagen vertical y otra horizontal por producto, para uso en feeds externos (no se muestran en la tienda).');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->installAdminTab()
            && $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->registerHook('actionObjectProductDeleteAfter');
    }

    public function uninstall()
    {
        $this->uninstallAdminTab();
        $this->uninstallDb();
        $this->deleteUploadsDir();

        return parent::uninstall();
    }

    private function installDb()
    {
        return $this->runSqlFile('install.sql');
    }

    private function uninstallDb()
    {
        return $this->runSqlFile('uninstall.sql');
    }

    /**
     * Ejecuta un fichero sql/*.sql, un statement por bloque separado por
     * ";" a final de linea, sustituyendo el placeholder PREFIX_ por el
     * prefijo real de tablas de la tienda. Mismo patron que usan otros
     * modulos propios del proyecto (p. ej. ccincidencias, inspiration).
     */
    private function runSqlFile($fileName)
    {
        $path = dirname(__FILE__) . '/sql/' . $fileName;

        if (!file_exists($path)) {
            return false;
        }

        $sql = str_replace('PREFIX_', _DB_PREFIX_, file_get_contents($path));

        foreach (array_filter(array_map('trim', preg_split('/;\s*\n/', $sql))) as $statement) {
            if ($statement && !Db::getInstance()->execute($statement)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tab oculto (id_parent = -1): no aparece en ningun menu, solo existe
     * para que el controlador admin de subida/borrado por AJAX sea
     * accesible. Mismo patron que AdminAjaxPsgdprController.
     */
    private function installAdminTab()
    {
        $className = 'AdminAjaxCcPromoImages';

        if ((int) Tab::getIdFromClassName($className)) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->id_parent = -1;
        $tab->module = $this->name;
        $tab->name = array_fill_keys(Language::getIDs(false), $this->displayName);

        return (bool) $tab->add();
    }

    private function uninstallAdminTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminAjaxCcPromoImages');

        if (!$idTab) {
            return true;
        }

        $tab = new Tab($idTab);

        if (!Validate::isLoadedObject($tab)) {
            return true;
        }

        return (bool) $tab->delete();
    }

    private function getUploadsDir()
    {
        return dirname(__FILE__) . '/uploads/';
    }

    /**
     * Ruta relativa web (para getMediaLink), no de filesystem.
     */
    private function getUploadsRelativeUrl()
    {
        return _MODULE_DIR_ . $this->name . '/uploads/';
    }

    private function deleteUploadsDir()
    {
        $dir = $this->getUploadsDir();

        if (!is_dir($dir)) {
            return true;
        }

        foreach (glob($dir . '*') as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        return @rmdir($dir);
    }

    /**
     * Este hook (nucleo de Prestashop) se renderiza en la pestaña
     * "Ajustes basicos" de la ficha de producto, justo despues del
     * bloque de Resumen/Descripcion - no existe ningun hook nucleo
     * entre las imagenes y el Resumen. El propio template reposiciona
     * el panel con JS para que quede donde lo pide el negocio: debajo
     * de las imagenes, antes del Resumen. No se ejecuta nunca en el
     * front.
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $idProduct = (int) (isset($params['id_product']) ? $params['id_product'] : Tools::getValue('id_product'));

        if (!$idProduct) {
            return '';
        }

        $images = $this->getImagesForProduct($idProduct);

        $this->context->smarty->assign(array(
            'ccpromoimages_id_product' => $idProduct,
            'ccpromoimages_images' => $images,
            'ccpromoimages_ajax_url' => $this->context->link->getAdminLink('AdminAjaxCcPromoImages'),
        ));

        return $this->context->smarty->fetch(dirname(__FILE__) . '/views/templates/hook/product_extra_images.tpl');
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        if (empty($params['object']) || empty($params['object']->id)) {
            return;
        }

        foreach (self::TIPOS as $tipo) {
            $this->deleteImage((int) $params['object']->id, $tipo);
        }
    }

    /**
     * @return array{vertical: array|null, horizontal: array|null}
     */
    public function getImagesForProduct($idProduct)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_imagen`, `tipo`, `url_imagen`
             FROM `' . _DB_PREFIX_ . 'ccpromoimages`
             WHERE `id_producto` = ' . (int) $idProduct
        );

        $result = array('vertical' => null, 'horizontal' => null);

        foreach ($rows as $row) {
            $row['media_url'] = $this->context->link->getMediaLink(
                $this->getUploadsRelativeUrl() . $row['url_imagen']
            );
            $result[$row['tipo']] = $row;
        }

        return $result;
    }

    /**
     * Valida y guarda una imagen subida para un producto/tipo. Si ya
     * existia una imagen de ese tipo para ese producto, se sustituye
     * (se borra el fichero anterior).
     *
     * @return array{success: bool, error?: string, image?: array}
     */
    public function saveUploadedImage($idProduct, $tipo, array $file)
    {
        if (!in_array($tipo, self::TIPOS, true)) {
            return array('success' => false, 'error' => 'Tipo invalido');
        }

        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return array('success' => false, 'error' => 'No se ha podido subir el archivo');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return array('success' => false, 'error' => 'El archivo supera el tamaño maximo permitido (8 MB)');
        }

        $ext = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!isset(self::ALLOWED_EXT[$ext])) {
            return array('success' => false, 'error' => 'Formato de imagen no permitido (usa jpg, png o webp)');
        }

        $mimeType = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }

        if ($mimeType && !in_array($mimeType, self::ALLOWED_EXT[$ext], true)) {
            return array('success' => false, 'error' => 'El contenido del archivo no coincide con una imagen valida');
        }

        $dir = $this->getUploadsDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = (int) $idProduct . '-' . $tipo . '-' . time() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return array('success' => false, 'error' => 'No se ha podido guardar el archivo en el servidor');
        }

        $existing = Db::getInstance()->getRow(
            'SELECT `url_imagen` FROM `' . _DB_PREFIX_ . 'ccpromoimages`
             WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
        );

        if ($existing && $existing['url_imagen'] && $existing['url_imagen'] !== $filename) {
            @unlink($dir . $existing['url_imagen']);
        }

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'ccpromoimages`
                 SET `url_imagen` = "' . pSQL($filename) . '", `date_upd` = "' . $now . '"
                 WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
            );
        } else {
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'ccpromoimages`
                 (`id_producto`, `tipo`, `url_imagen`, `date_add`, `date_upd`)
                 VALUES (' . (int) $idProduct . ', "' . pSQL($tipo) . '", "' . pSQL($filename) . '", "' . $now . '", "' . $now . '")'
            );
        }

        return array(
            'success' => true,
            'image' => array(
                'tipo' => $tipo,
                'url_imagen' => $filename,
                'media_url' => $this->context->link->getMediaLink($this->getUploadsRelativeUrl() . $filename),
            ),
        );
    }

    public function deleteImage($idProduct, $tipo)
    {
        if (!in_array($tipo, self::TIPOS, true)) {
            return false;
        }

        $existing = Db::getInstance()->getRow(
            'SELECT `url_imagen` FROM `' . _DB_PREFIX_ . 'ccpromoimages`
             WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
        );

        if ($existing && $existing['url_imagen']) {
            @unlink($this->getUploadsDir() . $existing['url_imagen']);
        }

        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'ccpromoimages`
             WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
        );
    }
}
