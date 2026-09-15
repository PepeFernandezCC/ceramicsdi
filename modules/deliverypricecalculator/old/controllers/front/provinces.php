<?php

class DeliverypricecalculatorProvincesModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function initContent()
    {
        header('Content-Type: application/json');

        $id_country = (int) Tools::getValue('id_country');

        if (!$id_country) {
            $this->ajaxDie(json_encode([]));
        }

        $provinces = State::getProvincesByCountry($id_country);

        $this->ajaxDie(json_encode($provinces));
    }
}
