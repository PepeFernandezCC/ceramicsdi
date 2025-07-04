<?php
/**
 * 2020 4webs PrestaPOS
 *
 * DEVELOPED by 4webs.es Prestashop Superhero Partner
 *
 * @author    4webs
 * @copyright 4webs 2020
 * @license   4webs
 * @category administration-featureFront
 */

if (!defined('_PS_VERSION_')) {
	exit;
}

class PaypalwithfeeAjaxppwfModuleFrontController extends ModuleFrontController
{
    /**
     * Handles GET requests
     */
    public function initContent()
    {
        //Security check for token ;-)
        die;
    }

    /**
     * Handles POST requests
     */
    public function postprocess()
    {
        //Security check for token ;-)
        if (!$this->module->checkToken(Tools::getValue('token'))) {
            echo '403';
            die;
        }

        //ajax autocomplete
        $context = Context::getContext();
        $cookie = $context->cookie;
        $q = $_REQUEST['keyword'];

        $sql = "SELECT pl.name, p.id_product, p.reference, p.price "
              . "FROM "._DB_PREFIX_."product p
                LEFT JOIN "._DB_PREFIX_."product_lang pl "
              . "ON ( pl.id_product = p.id_product
                AND pl.id_lang =".(int)$cookie->id_lang." ) "
              . "WHERE (pl.name LIKE '%".pSQL($q)."%'
                OR p.reference LIKE '%".pSQL($q)."%'
                OR p.ean13 LIKE '%".pSQL($q)."%'
                OR p.id_product LIKE '%".pSQL($q)."%')";


        $resultado = Db::getInstance()->executeS($sql);

        $context = $context->getContext();
        $smarty = $context->smarty;

        $smarty->assign(array(
          'resultado' => $resultado
        ));

        $fetch = $smarty->fetch(
            _PS_MODULE_DIR_ .
            '/paypalwithfee/views/templates/admin/autocomplete.tpl'
        );
        echo $fetch;
        die;
    }
}
