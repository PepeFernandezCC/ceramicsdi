<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Clase dummy de pago para poder usar validateOrder()
 * No aparece en el checkout, solo se usa internamente al importar pedidos.
 */
class ManoManoImportPayment extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'manomanoimportpayment';
        $this->displayName = 'ManoMano';
        $this->description = 'Método ficticio para validar pedidos importados desde ManoMano';
        $this->author = 'José Fernández';
        $this->version = '1.0.0';
        $this->active = true;

        parent::__construct();
    }
}
