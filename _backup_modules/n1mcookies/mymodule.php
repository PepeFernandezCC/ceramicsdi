<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'TuNombre';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('N1mConsent Cookie Plugin');
        $this->description = $this->l('Añade el código de consentimiento de cookies de Google.');

        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->registerHook('footer') &&
            Configuration::updateValue('MYMODULE_CODE', '') &&
            Configuration::updateValue('MYMODULE_PALETTE', 'dark') &&
            Configuration::updateValue('MYMODULE_LANGUAGE', 'es');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('MYMODULE_CODE') &&
            Configuration::deleteByName('MYMODULE_PALETTE') &&
            Configuration::deleteByName('MYMODULE_LANGUAGE');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit'.$this->name)) {
            $code = (string) Tools::getValue('MYMODULE_CODE');
            $palette = (string) Tools::getValue('MYMODULE_PALETTE');
            $language = (string) Tools::getValue('MYMODULE_LANGUAGE');

            Configuration::updateValue('MYMODULE_CODE', $code);
            Configuration::updateValue('MYMODULE_PALETTE', $palette);
            Configuration::updateValue('MYMODULE_LANGUAGE', $language);

            $output .= $this->displayConfirmation($this->l('Configuración actualizada'));
        }

        return $output.$this->renderForm();
    }

    public function hookFooter($params)
    {
        $this->context->smarty->assign(array(
            'mymodule_code' => Configuration::get('MYMODULE_CODE'),
            'mymodule_palette' => Configuration::get('MYMODULE_PALETTE'),
            'mymodule_language' => Configuration::get('MYMODULE_LANGUAGE'),
        ));

        return $this->display(__FILE__, 'views/templates/hook/footer.tpl');
    }

    // Método para crear el formulario de configuración
private function renderForm()
{
    $fields_form = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Configuraciones'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Código AW-XXXXXXXXX:'),
                    'name' => 'MYMODULE_CODE',
                    'size' => 20,
                    'required' => true,
                    'desc' => $this->l('Introduce el código de seguimiento de Google.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tema:'),
                    'name' => 'MYMODULE_PALETTE',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'dark',
                                'name' => $this->l('Oscuro')
                            ),
                            array(
                                'id_option' => 'light',
                                'name' => $this->l('Claro')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Idioma:'),
                    'name' => 'MYMODULE_LANGUAGE',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'en',
                                'name' => $this->l('Inglés')
                            ),
                            array(
                                'id_option' => 'es',
                                'name' => $this->l('Español')
                            ),
                            array(
                                'id_option' => 'de',
                                'name' => $this->l('Alemán')
                            ),
                            array(
                                'id_option' => 'fr',
                                'name' => $this->l('Francés')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Guardar'),
                'class' => 'btn btn-default pull-right'
            )
        ),
    );

    $helper = new HelperForm();

    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;

    $helper->title = $this->displayName;
    $helper->show_toolbar = true;
    $helper->toolbar_scroll = true;
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Guardar'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Volver a la lista')
        )
    );

    $helper->fields_value['MYMODULE_CODE'] = Configuration::get('MYMODULE_CODE');
    $helper->fields_value['MYMODULE_PALETTE'] = Configuration::get('MYMODULE_PALETTE');
    $helper->fields_value['MYMODULE_LANGUAGE'] = Configuration::get('MYMODULE_LANGUAGE');

    return $helper->generateForm(array($fields_form));
}

}
