<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'productmetacsv/classes/ProductMetaCsvGenerator.php';

class ProductMetaCsv extends Module
{
    public function __construct()
    {
        $this->name = 'productmetacsv';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Exportar Metadatos de Productos (CSV)');
        $this->description = $this->l('Genera un CSV con los metadatos (Google/Facebook feed) de todos los productos activos, por idioma.');

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        // La descarga se procesa aquí mismo, dentro de la página de admin ya
        // autenticada: evita depender de tokens/empleado en un controlador
        // front, que no tiene sesión de empleado.
        if (Tools::isSubmit('submitPmcExport')) {
            $idLang = (int) Tools::getValue('pmc_id_lang');
            $language = new Language($idLang);

            if (Validate::isLoadedObject($language)) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                ProductMetaCsvGenerator::output($this->context, $idLang);
                exit;
            }
        }

        // No filtramos por tienda aquí: en multitienda, o cuando se ve el
        // módulo con el contexto "Todas las tiendas", $this->context->shop
        // puede venir a null.
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'pmc_languages' => $languages,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
