<?php
class UpdateTaxesAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->helloWorld();

    }

    public function helloWorld() {

        header('Content-Type: application/json');

        $response = [
            'success' => true, 
            'msg' => 'Estoy Dentro'
        ];

        return json_encode($response);
     
    }
}