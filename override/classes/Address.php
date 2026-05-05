<?php

class Address extends AddressCore
{
    /** @var int */
    public $is_invoice;

    /** @var string */
    public $dni_format;

    protected static function addCustomFieldsDefinition()
    {
        self::$definition['fields']['is_invoice'] = [
            'type'     => self::TYPE_INT,
            'validate' => 'isUnsignedInt',
            'required' => false,
        ];

        self::$definition['fields']['dni_format'] = [
            'type'     => self::TYPE_STRING,
            'validate' => 'isCleanHtml',
            'required' => false,
            'size'     => 18,
        ];
    }

    protected function syncCustomFieldsDefinition()
    {
        if (isset($this->def['fields'])) {
            $this->def['fields']['is_invoice'] = self::$definition['fields']['is_invoice'];
            $this->def['fields']['dni_format'] = self::$definition['fields']['dni_format'];
        }
    }

    public function __construct($id = null, $idLang = null)
    {
        self::addCustomFieldsDefinition();

        parent::__construct($id, $idLang);

        $this->syncCustomFieldsDefinition();
    }

    protected function buildDniFormat() {
        // OJO: si tu campo real no se llama "dni", cambia aquí.
        $dni = (string) $this->dni;

        // Limpieza básica (opcional pero recomendable)
        $dni = trim($dni);
        $dni = str_replace([' ', '-', '.'], '', $dni);

        if ($dni === '') {
            $this->dni_format = '';
            return;
        }

        // Normaliza prefijo
        $dniUpper = strtoupper($dni);

        if ((int) $this->id_country === 6) {
            // Si ya viene con ES, no duplicar
            if (strpos($dniUpper, 'ES') === 0) {
                $this->dni_format = $dniUpper;
            } else {
                $this->dni_format = 'ES' . $dniUpper;
            }
        } else {
            // Para otros países, guardar tal cual (en mayúsculas y limpio)
            $this->dni_format = $dniUpper;
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        self::addCustomFieldsDefinition();
        $this->syncCustomFieldsDefinition();

        $postIsInvoice = Tools::getValue('is_invoice', null);

        if ($postIsInvoice !== null) {
            $this->is_invoice = (int) $postIsInvoice;
        }

        $this->buildDniFormat();

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        self::addCustomFieldsDefinition();
        $this->syncCustomFieldsDefinition();

        $postIsInvoice = Tools::getValue('is_invoice', null);

        if ($postIsInvoice !== null) {
            $this->is_invoice = (int) $postIsInvoice;
        }

        $this->buildDniFormat();

        return parent::update($null_values);
    }


    public static function getVatApiData($id_delivery, $id_invoice) {
        $delivery_address = new address($id_delivery);
        $delivery_country = $delivery_address->id_country;

        $address = new address($id_invoice);
        $cif = $address->dni;
        $vat = $address->vat_number;
        $country = $address->id_country;
        $customer = $address->id_customer;
        $validate = true;

        if ($country == 6 || $delivery_country == 6) {
            customer::removeIntracomunitaryGroup($customer);
            $validate = false;
        }

        return [
            'vat_number' => ($vat == '' || $vat == NULL) ? $cif : $vat,
            'customer' => $customer,
            'country' => $country, 
            'validate' => $validate
        ];
    }

    public static function updateIntracomunitaryAddress($idAddress, $vatNumber) {
        Db::getInstance()->update('address', [
            'vat_number' => $vatNumber,
        ], 'id_address = ' . (int) $idAddress);
    }
    
    public static function getAddressValidations($id_address) {
        $query = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE `id_address` = ' . (int)$id_address ;
        $addresses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $address = $addresses[0];
        $dni_error = false;
        $postCode_error = false;
        $error = 'Sin errores';
        $spain = false; 

        if ($address['id_country'] == '6') {
            $spain = true;
        } 

        if($spain) {
            if ($address['dni'] == '') {
                $dni_error = true;
            }
        } else {
            if ($address['dni'] == '' && $address['company'] != '') {
                $dni_error = true;
            }
        }

        if ($address['postcode'] == '') {
            $postCode_error = true;
        }

        if ($dni_error || $postCode_error) {
            if ($dni_error) {
                $error = 'Error en el campo dni';
            }
            if ($postCode_error) {
                $error = 'Error en el campo postal';
            }
            if ($dni_error && $postCode_error) {
                $error = 'Errores en varios campos';
            }

            return [
                'validations'   => false,
                'dni_error'     => $dni_error,
                'postCode_error'=> $postCode_error, 
                'spain'         => $spain,
                'error'         => $error
            ];
        }

        return [
            'validations'   => true,
            'dni_error'     => $dni_error,
            'postCode_error'=> $postCode_error, 
            'spain'         => $spain,
            'error'         => $error
        ];
    }

    public static function isValidAddressCard($address, $cart) {
        
        $isInvoice = (int)$address['is_invoice'] !== 0;
        $isSpanish = (int)$address['id_country'] === 6;
        $totalNeedsID = $cart['subtotals']['products']['amount'] >= 2200;
        $hasDni = !empty(trim((string) $address['dni']));

        $requiresForeignID = $isInvoice && !$isSpanish && $totalNeedsID;
        
        if(!$requiresForeignID) {
            return true;
        }

        return $hasDni;
    }

    private function debugAddressPayload($method)
    {
        $logFile = _PS_ROOT_DIR_ . '/var/logs/address_payload_debug.log';

        $data = [
            'date' => date('Y-m-d H:i:s'),
            'method' => $method,
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,
            'post_is_invoice' => $_POST['is_invoice'] ?? 'NO_POST',
            'tools_is_invoice' => \Tools::getValue('is_invoice', 'NO_TOOLS'),
            'object_is_invoice' => $this->is_invoice ?? 'NO_OBJECT_VALUE',
            'POST' => $_POST,
            'GET' => $_GET,
            'raw_input' => file_get_contents('php://input'),
        ];

        file_put_contents(
            $logFile,
            print_r($data, true) . "\n\n----------------------\n\n",
            FILE_APPEND
        );
    }
}
