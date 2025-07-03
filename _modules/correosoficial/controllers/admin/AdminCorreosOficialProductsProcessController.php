<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';	
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';

require_once dirname(__FILE__).'/../../classes/CorreosOficialCarrier.php';

class AdminCorreosOficialProductsProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    private $dao;
    private $products_dao;
    
    private $info1, $info2, $info3, $info4;

    public function __construct()
    {
        parent::__construct();
        $this->info1=$this->l('Products created as carriers in your shop');
        $this->info3=$this->l('Products created as carriers in your shop. You have selected products that were already in your store. Have been activated');
        $this->info2=$this->l('You have selected products that were already in your store. Have been activated');
        $this->info4=$this->l('There aren\'t active products as carriers in your shop.');

        // Obtenemos un objetoDao
        $this->dao = new CorreosOficialProductsDao();

        parent::__construct(); 

        switch (Tools::getValue('action')) {
            case 'CorreosProductsForm':
                $this->updateProducts();
                break;
            case 'getActiveProducts':
                $this->getActiveProducts();
                break;
            default:
                throw new Exception('ERROR CORREOS OFICIAL 14502: No se ha indicado un "action" para el formulario.');
        }

    }

    public function updateProducts(){
        $products=Tools::getValue('products');
        $return=array();
		
        // Resetea la tabla productos poniendo a 0 el active de ese id_shop
        $this->dao->resetProducts();

        if ($products){

            // Reseteamos los carriers de correos en ps_carrier a active 0
            CorreosOficialCarrier::resetCarriers($this->context->shop->id);

            $existing_products=0;
            $added_product=0;

            foreach ($products as $key=>$value){
                // insertamos los carriers en correos_oficial_products_shop con active = 1
                $this->dao->updateProducts($key);

                $product = $this->dao->getProduct($key, 'correos_oficial_products');
                $carrier = new CorreosOficialCarrier();
                
                // con carrierExist ponemos en ps_carrier active = 1 en caso de que exista
                if (!$carrier->carrierExists($product[0]->name)){
                    // Si no existe lo añadimos a ps_carrier
                    $carrier->addCarrier($product[0]);
                    $added_product++;
                }
                else {
                    $id_carrier=$carrier->getCarrierID($product[0]->name);
                    // Añadimos el id_carrier en la table correos_oficial_products
                    $carrier->updateProductCorreosOficial($id_carrier[0]['id_carrier'], $product[0]->id);
                    $existing_products++;
                }
                // para multitienda actualizamos la db ps_carrier_shop
                if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                    $id_carrier=$carrier->getCarrierID($product[0]->name);
                    $carrier->updateCarrierShop($id_carrier[0]['id_carrier'], $this->context->shop->id);
                }
            }


            // Products created as carriers in your shop');
            if ($existing_products==0){
                $return['info']='INFO 14503';
                $return['desc']=$this->info1;
            }
            // You have selected products that were already in your store.
            // Have been activated
            else if ($existing_products>0 && $added_product==0){
                $return['info']='INFO 14506';
                $return['desc']=$this->info2;
            }
            // Products created as carriers in your shop.
            // You have selected products that were already in your store.
            // Have been activated
            else {
                $return['info']='INFO 14504';
                $return['desc']=$this->info3;
            }
            die(json_encode($return));
       }
       // There aren\'t active products as carriers in your shop.
       else {
            CorreosOficialCarrier::resetCarriers($this->context->shop->id);
            $return['info']='INFO 14505';
            $return['desc']=$this->info4;
            die(json_encode($return));
       }
    }

    public function getActiveProducts()
    {
        $this->products_dao = new CorreosOficialProductsDao();
        $products = $this->products_dao->getActiveProducts(' WHERE coc.active = 1 and coc.id_shop = '.$this->context->shop->id);
        die(json_encode($products));
    }

}
?>
