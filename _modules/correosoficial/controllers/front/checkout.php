<?php
 if (!defined('_PS_VERSION_')) {
     exit;
 }

 require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
 require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class CorreosOficialCheckoutModuleFrontController extends ModuleFrontController
{
    /**
     * @var module
     */
    public $module;
    public  $context;

    public function __construct (){
        $this->context = Context::getContext();
    }

    public function init(){
        // Método Abstracto
    }
    public function postProcess(){
        // Método Abstracto
    }
    public function initHeader(){
        // Método Abstracto
    }
    public function display(){
        // Método Abstracto
    }

    public function initContent()
    {
        switch (Tools::getValue('action')){

            case 'SearchCityPaqByPostalCode':
                $postcode = Normalization::normalizeData('postcode');
                $correos_soap = new CorreosSoap();
                $correos_soap->homePaqConsultaCP1($postcode);
                break;
            case 'SearchOfficeByPostalCode':
                $postcode = Normalization::normalizeData('postcode');
                $correos_soap = new CorreosSoap();
                $correos_soap->localizadorConsulta($postcode);
                break;
            case 'insertCityPaq':
            case 'insertOffice':
                $id_cart=$this->context->cookie->id_cart;
                $citypaq = Normalization::normalizeData('citypaq');
                $office = Normalization::normalizeData('office');
                $data = json_encode(Normalization::normalizeData('data'));

                if (isset($citypaq) && !empty($citypaq)){
                    $reference_code = $citypaq;
                }
                elseif (isset($office) && !empty($office)){
                    $reference_code = $office;
                }
                else {
                    die('Error 14507: '.'Error técnico: No se ha seleccionado CityPaq u Oficina. Póngase en contacto con Correos');
                }

                $existing_cart = CorreosOficialCheckout::insertCartIntoRequests($id_cart);

                CorreosOficialCheckout::insertReferenceCode($id_cart, $reference_code, $data, $existing_cart);

            break;
            default:
                throw new LogicException('ERROR CORREOS OFICIAL 21010: No se ha indicado un "action" para el formulario.');
        }
    }
}
