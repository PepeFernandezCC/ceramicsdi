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

require_once(_PS_MODULE_DIR_ . 'paypalwithfee' . DIRECTORY_SEPARATOR . 'classes/PaypalOrder.php');

class PaypalwithfeeAddressajaxModuleFrontController extends ModuleFrontController
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
        //Get the id Fee
        $idFee = Tools::getValue("idFee");

        //Load the fee object
        $fee = PaypalOrderx::getFeeById($idFee);

        //Now load the order
        $order = new Order((int) $fee['id_order']);

        //Decode the customer data
        $cData = json_decode($fee['customer_data']);

        //Try to match the address parts into states and countries id's from prestashop
        $parts = $this->module->matchParts(
            $cData->address->admin_area_1,
            $cData->address->admin_area_2,
            $cData->address->country_code
        );

        $id_state = State::getIdByName($cData->address->admin_area_2);

        $originalAddress = new Address((int) $order->id_address_delivery);

        //Create a new address
        $address = new Address();
        $address->alias = 'ppwf correction';
        $address->id_country = $parts['countryCode'];
        $address->id_state = $id_state;
        $address->id_customer = $order->id_customer;
        $address->city = $parts['cityOne'];
        $address->address1 = $cData->address->address_line_1;
        $address->postcode = $cData->address->postal_code;
        $address->firstname = $cData->name->full_name;
        $address->lastname = $cData->name->full_name;
        $address->dni = $originalAddress->dni;
        $address->add();
        
        $order->id_address_delivery = $address->id;
        $order->update();

        print_r(
            json_encode(
                [
                    'result' => 'ok'
                ]
            )
        );

        //Send an email to the customer if the admin allows to notify the address has been changed
        if (Configuration::get('PPAL_SEND_EMAIL_ON_ADDR_UPDATE') == 1) {
            $customer = new Customer((int) $order->id_customer);

            try {
                return Mail::send(
                    (int)Configuration::get('PS_LANG_DEFAULT'),
                    'alert',
                    $this->l('Order address changed'),
                    [
                        'ref' => $order->reference
                    ],
                    $customer->email, //Receiver mail
                    $customer->firstname . ' ' . $customer->lastname, //From email addr
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'), //From name
                    null, //Attachment
                    null, //Mode smtp
                    __PS_BASE_URI__ . 'modules/paypalwithfee/mails/es/'
                );
            } catch (\Throwable $th) {
                var_dump($th);
                return null;
            }
        }

        die;
    }
}
