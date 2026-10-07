<?php
/**
 * Editor masivo de productos: listado editable con características, exportación e importación CSV.
 *
 * @author    Ceramic Connection
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/CcpeFields.php';
require_once __DIR__ . '/classes/CcpeRepository.php';
require_once __DIR__ . '/classes/CcpeValidator.php';
require_once __DIR__ . '/classes/CcpeUpdater.php';
require_once __DIR__ . '/classes/CcpeCsv.php';
require_once __DIR__ . '/classes/CcpeLog.php';

class CcProductEditor extends Module
{
    const ADMIN_CONTROLLER = 'AdminCcProductEditor';

    public function __construct()
    {
        $this->name = 'ccproducteditor';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Editor masivo de productos');
        $this->description = $this->l('Lista, edita, exporta e importa por CSV los productos con todas sus características.');
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => _PS_VERSION_];

        // PrestaShop 1.7.1+ instala y desinstala automáticamente estas pestañas
        $this->tabs = [
            [
                'class_name' => self::ADMIN_CONTROLLER,
                'parent_class_name' => 'AdminCatalog',
                'name' => 'Editor masivo',
                'visible' => true,
            ],
        ];
    }

    public function install()
    {
        return parent::install() && CcpeLog::ensureDirectory();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(self::ADMIN_CONTROLLER));
    }
}
