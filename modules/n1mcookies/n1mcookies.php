<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class N1mCookies extends Module
{
    public function __construct()
    {
        $this->name = 'n1mcookies';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'N1mCookies';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->path = _PS_MODULE_DIR_ . $this->name; // Define the path

        parent::__construct();

        $this->displayName = $this->l('N1mCookies ConsentV2 Plugin');
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
            Configuration::updateValue('N1MCOOKIES_CODE', '') &&
            Configuration::updateValue('N1MCOOKIES_PALETTE', 'dark') &&
            Configuration::updateValue('N1MCOOKIES_LANGUAGE', 'es') &&
            Configuration::updateValue('N1MCOOKIES_ANALYTICS', '');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('N1MCOOKIES_CODE') &&
            Configuration::deleteByName('N1MCOOKIES_PALETTE') &&
            Configuration::deleteByName('N1MCOOKIES_LANGUAGE') &&
            Configuration::deleteByName('N1MCOOKIES_ANALYTICS');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit'.$this->name)) {
            $code = (string) Tools::getValue('N1MCOOKIES_CODE');
            $palette = (string) Tools::getValue('N1MCOOKIES_PALETTE');
            $language = (string) Tools::getValue('N1MCOOKIES_LANGUAGE');
            $analytics = (string) Tools::getValue('N1MCOOKIES_ANALYTICS');

            Configuration::updateValue('N1MCOOKIES_CODE', $code);
            Configuration::updateValue('N1MCOOKIES_PALETTE', $palette);
            Configuration::updateValue('N1MCOOKIES_LANGUAGE', $language);
            Configuration::updateValue('N1MCOOKIES_ANALYTICS', $analytics);

            $output .= $this->displayConfirmation($this->l('Configuración actualizada'));
        }

        return $output.$this->renderForm();
    }

    public function hookFooter($params)
    {
        $this->context->smarty->assign(array(
            'n1mcookies_code' => Configuration::get('N1MCOOKIES_CODE'),
            'n1mcookies_palette' => Configuration::get('N1MCOOKIES_PALETTE'),
            'n1mcookies_language' => Configuration::get('N1MCOOKIES_LANGUAGE'),
            'n1mcookies_analytics' => Configuration::get('N1MCOOKIES_ANALYTICS'),
            'module_dir' => $this->_path,
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
                        'type' => 'html',
                        'name' => 'N1MCOOKIES_INSTRUCTIONS_TITLE',
                        'html_content' => '<h3>' . $this->l('Instrucciones') . '</h3>',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'N1MCOOKIES_INSTRUCTIONS_CONTENT',
                        'html_content' => '<ol>
                                              <li>' . $this->l('Accede a tu cuenta de Google Ads.') . '</li>
                                              <li>' . $this->l('Ve a la sección de "Herramientas" > "Gestor de datos".') . '</li>
                                              <li>' . $this->l('Haz clic en "Etiqueta de Google".') . '</li>
                                              <li>' . $this->l('Copia el código de "Su etiqueta de Google" que comienza con "AW-" y pégalo en el campo de abajo.') . '</li>
                                              <li><a href="https://doc.nimbox360.com/tema/n1mcookies-v2/">Haz click aquí para información actualizada</a></li>
                                           </ol>',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Código AW-XXXXXXXXX:'),
                        'name' => 'N1MCOOKIES_CODE',
                        'size' => 20,
                        'cols' => 5,
                        'required' => true,
                        'desc' => $this->l('Introduce el código de seguimiento de Google.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Código de Google Analytics:'),
                        'name' => 'N1MCOOKIES_ANALYTICS',
                        'size' => 20,
                        'cols' => 5,
                        'desc' => $this->l('Introduce el código de Google Analytics (UA-XXXXXXXX-X).'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Tema:'),
                        'name' => 'N1MCOOKIES_PALETTE',
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
                        'name' => 'N1MCOOKIES_LANGUAGE',
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

        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = $this->context->language->id;

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

        $helper->fields_value['N1MCOOKIES_CODE'] = Configuration::get('N1MCOOKIES_CODE');
        $helper->fields_value['N1MCOOKIES_PALETTE'] = Configuration::get('N1MCOOKIES_PALETTE');
        $helper->fields_value['N1MCOOKIES_LANGUAGE'] = Configuration::get('N1MCOOKIES_LANGUAGE');
        $helper->fields_value['N1MCOOKIES_ANALYTICS'] = Configuration::get('N1MCOOKIES_ANALYTICS');

        return $helper->generateForm(array($fields_form));
    }
}
