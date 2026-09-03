<?php

class Address extends AddressCore
{
    /** Importe (subtotal de productos) a partir del cual una dirección de
     *  facturación extranjera necesita DNI/CIF. */
    const FOREIGN_ID_MIN_AMOUNT = 2200;

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
        $this->buildPhoneFormat();

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
        $this->buildPhoneFormat();

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
        $totalNeedsID = $cart['subtotals']['products']['amount'] >= self::FOREIGN_ID_MIN_AMOUNT;
        $hasDni = !empty(trim((string) $address['dni']));

        $requiresForeignID = $isInvoice && !$isSpanish && $totalNeedsID;
        
        if(!$requiresForeignID) {
            return true;
        }

        return $hasDni;
    }

    private function buildPhoneFormat()
    {
        $prefixes = [
            1 => '+49',   // DE
            2 => '+43',   // AT
            3 => '+32',   // BE
            4 => '+1',    // CA
            6 => '+34',   // ES
            7 => '+358',  // FI
            8 => '+33',   // FR
            9 => '+30',   // GR
            10 => '+39',  // IT
            12 => '+352', // LU
            13 => '+31',  // NL
            14 => '+48',  // PL
            15 => '+351', // PT
            16 => '+420', // CZ
            17 => '+44',  // GB
            18 => '+46',  // SE
            19 => '+41',  // CH
            20 => '+45',  // DK
            23 => '+47',  // NO
            26 => '+353', // IE
            29 => '+972', // IL
            36 => '+40',  // RO
            37 => '+421', // SK
            40 => '+376', // AD
            52 => '+375', // BY
            74 => '+385', // HR
            76 => '+357', // CY
            86 => '+372', // EE
            93 => '+995', // GE
            97 => '+350', // GI
            106 => '+379',// VA
            124 => '+371',// LV
            129 => '+423',// LI
            130 => '+370',// LT
            138 => '+356',// MT
            142 => '+36', // HU
            146 => '+373',// MD
            147 => '+377',// MC
            149 => '+382',// ME
            188 => '+381',// RS
            191 => '+386',// SI
            231 => '+387',// BA
            233 => '+359' // BG
        ];

        $idCountry = (int) $this->id_country;

        if (!isset($prefixes[$idCountry])) {
            return;
        }

        $prefix = $prefixes[$idCountry];

        if (!empty($this->phone)) {
            $this->phone = $this->normalizePhoneWithPrefix($this->phone, $prefix);
        }

        if (!empty($this->phone_mobile)) {
            $this->phone_mobile = $this->normalizePhoneWithPrefix($this->phone_mobile, $prefix);
        }
    }

    private function normalizePhoneWithPrefix($rawPhone, $prefix)
    {
        $phone = preg_replace('/\D+/', '', (string) $rawPhone);
        $prefixDigits = preg_replace('/\D+/', '', (string) $prefix);

        if ($phone === '') {
            return '';
        }

        /*
        * Casos que cubre:
        * +4917664262437  -> 4917664262437
        * 004917664262437 -> 004917664262437
        * 4917664262437   -> 4917664262437
        * 017664262437    -> 017664262437
        */

        // Si viene como 0049..., quitar 0049
        if ($prefixDigits && strpos($phone, '00' . $prefixDigits) === 0) {
            $phone = substr($phone, strlen('00' . $prefixDigits));
        }

        // Si viene como 49..., quitar 49
        if ($prefixDigits && strpos($phone, $prefixDigits) === 0) {
            $phone = substr($phone, strlen($prefixDigits));
        }

        // Quitar ceros iniciales nacionales: 0176... -> 176...
        $phone = preg_replace('/^0+/', '', $phone);

        if ($phone === '') {
            return '';
        }

        return $prefix . $phone;
    }
}
