<?php
/**
 * CRUD de tipos de incidencia (crear / editar / borrar / reordenar /
 * activar-desactivar) sin tocar codigo. Ver CcIncidenciasTipo.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'ccincidencias/classes/CcIncidenciasTipo.php';

class AdminCcIncidenciasTiposController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'ccincidencias_tipo';
        $this->identifier = 'id_ccincidencias_tipo';
        $this->className = 'CcIncidenciasTipo';
        $this->lang = true;
        $this->bootstrap = true;
        $this->position_identifier = 'id_ccincidencias_tipo';
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';
        $this->allow_export = false;

        parent::__construct();

        $this->fields_list = array(
            'id_ccincidencias_tipo' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'code' => array(
                'title' => $this->l('Codigo (va en el correo como "tipo:")'),
            ),
            'descripcion' => array(
                'title' => $this->l('Descripcion'),
                'lang' => true,
            ),
            'email' => array(
                'title' => $this->l('Email destino'),
            ),
            'position' => array(
                'title' => $this->l('Posicion'),
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'active' => array(
                'title' => $this->l('Activo'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Tipo de incidencia'),
                'icon' => 'icon-tags',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Codigo'),
                    'name' => 'code',
                    'required' => true,
                    'desc' => $this->l('Valor exacto que recibira el sistema de incidencias en la clave "tipo:" del correo. Se guarda siempre en MAYUSCULAS. Si vas a cambiar el codigo de un tipo que el sistema de incidencias ya reconoce, avisa antes al responsable de ese sistema: un cambio aqui rompe la entrada de incidencias sin que nadie se entere hasta que alguien se queje.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Descripcion'),
                    'name' => 'descripcion',
                    'lang' => true,
                    'required' => true,
                    'desc' => $this->l('Texto que ve el cliente en el desplegable del formulario, en cada idioma de la tienda.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email destino'),
                    'name' => 'email',
                    'desc' => $this->l('A donde se envia el correo de las incidencias de este tipo. Dejalo vacio para usar el email general del modulo.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Activo'),
                    'name' => 'active',
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Si')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
            ),
            'submit' => array('title' => $this->l('Guardar')),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::getValue('code') !== false) {
            $_POST['code'] = CcIncidenciasTipo::normalizeCode(Tools::getValue('code'));
        }

        return parent::processSave();
    }

    public function initToolbar()
    {
        parent::initToolbar();

        // Volver a la configuracion general del modulo desde el listado.
        $this->toolbar_btn['back-to-config'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=ccincidencias&module_name=ccincidencias',
            'desc' => $this->l('Volver a la configuracion del modulo'),
            'icon' => 'process-icon-back',
        );
    }
}
