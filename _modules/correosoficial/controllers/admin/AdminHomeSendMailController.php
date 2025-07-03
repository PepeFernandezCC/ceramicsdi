<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/SendEmail.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminHomeSendMailController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    public function __construct()
    {
        global $co_signup_customers_from;
        global $co_signup_customers_cc;

        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct(); 

        $inputCompany     = Normalization::normalizeData('input_company');
        $inputCif         = Normalization::normalizeData('input_cif');
        $inputContactName = Normalization::normalizeData('input_contact_name');
        $inputPhoneMobile = Normalization::normalizeData('input_mobile_phone');
        $inputPhone       = Normalization::normalizeData('input_phone');
        $inputEmail       = Normalization::normalizeData('input_email');
        $productCategory  = Normalization::normalizeData('product_category');

        $platform_and_version=PLATFORM_AND_VERSION;

        $body_to_Customer=
        $this->l('Dear Customer, we have receive your request from module CorreosOficial for ', 'AdminHomeSendMailController').PLATFORM_AND_VERSION.".\r\n\r\n".
        $this->l('We will contact you as soon as possible.', 'AdminHomeSendMailController')."\r\n\r\n".
        "$inputCompany\r\n".
        "$inputCif\r\n".
        "$inputContactName\r\n".
        "$inputPhoneMobile\r\n".
        "$inputPhone\r\n".
        "$inputEmail\r\n".
        "$productCategory\r\n\r\n".
        "--\r\n\r\nMódulo E-COMMERCE CorreosOficial\r\n\r\n";
   

        $body_to_CorreosGroup =
        "Se ha recibido una solicitud desde ".$platform_and_version.": \r\n\r\n".
        "Compañía: $inputCompany\r\n".
        "CIF: $inputCif\r\n".
        "Persona de contacto: $inputContactName\r\n".
        "Teléfono Móvil: $inputPhoneMobile\r\n".
        "Teléfono fijo: $inputPhone\r\n".
        "Email: $inputEmail\r\n".
        "Categoría de producto: $productCategory\r\n\r\n".
        "--\r\n\r\nMódulo E-COMMERCE CorreosOficial\r\n\r\n";


        // Email al cliente
        $result1=$this->SendEMail($inputEmail, $this->l('Sign up in CorreosOficial: You will receive an answer soon', 'AdminHomeSendMailController'),
         $body_to_Customer, $co_signup_customers_from);

        // Email a Grupo Correos
        $result2=$this->SendEMail($co_signup_customers_cc, _('New lead from CorreosOficial E-COMMERCE: ').$platform_and_version, 
           $body_to_CorreosGroup, $co_signup_customers_from, $co_signup_customers_cc);
        $result=array($result1, $result2);
        CorreosOficialUtils::varDump("ENVIO DE CORREO", $result);
        die($result1);
    }

    public function SendEMail($email, $subject, $message, $from, $cc=null){
        $mail = new SendMail($email, $subject, $message, $from, $cc);
        return $mail->sendEmail();
    }
}
?>