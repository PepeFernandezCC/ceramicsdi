<?php
/**
 * Controlador admin sin menu (id_parent = -1), solo para las llamadas
 * AJAX de subida/borrado de las imagenes promocionales desde la
 * pestaña "Modulos" de la ficha de producto.
 */
class AdminAjaxCcPromoImagesController extends ModuleAdminController
{
    /** @var CcPromoImages */
    public $module;

    public function ajaxProcessUpload()
    {
        $idProduct = (int) Tools::getValue('id_product');
        $tipo = Tools::getValue('tipo');

        if (!$idProduct || !Validate::isLoadedObject(new Product($idProduct))) {
            $this->ajaxDie(json_encode(array('success' => false, 'error' => 'Producto no valido')));
        }

        if (empty($_FILES['image'])) {
            $this->ajaxDie(json_encode(array('success' => false, 'error' => 'No se ha recibido ningun archivo')));
        }

        $result = $this->module->saveUploadedImage($idProduct, $tipo, $_FILES['image']);

        $this->ajaxDie(json_encode($result));
    }

    public function ajaxProcessDelete()
    {
        $idProduct = (int) Tools::getValue('id_product');
        $tipo = Tools::getValue('tipo');

        if (!$idProduct) {
            $this->ajaxDie(json_encode(array('success' => false, 'error' => 'Producto no valido')));
        }

        $success = $this->module->deleteImage($idProduct, $tipo);

        $this->ajaxDie(json_encode(array('success' => (bool) $success)));
    }
}
