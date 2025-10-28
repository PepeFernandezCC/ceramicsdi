<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Core\Controller\Admin;

use Context;
use Media;
use ModuleAdminController;
use PrestaShopException;
use Scaledev\Adeo\Core\Module;
use Scaledev\Adeo\Core\Tools;
use SdevAdeo;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AbstractModuleAdminController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
abstract class AbstractModuleAdminController extends ModuleAdminController
{
    /**
     * Defines the template to render.
     */
    const TEMPLATE = null;

    /** @var SdevAdeo */
    public $module;

    /**
     * AbstractModuleAdminController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        $this->context->smarty->assign(array(
            'documentationsList' => Module::getDocumentationsList(),
            'module' => $this->module,
        ));
    }

    /**
     * Display the controller's template.
     *
     * @return false|string
     */
    public function renderList()
    {

        Media::addJsDef(array(
            'chosenNoResultMessage' => $this->module->l('No match found !'),
            'yesTranslation' => $this->module->l('Yes'),
            'noTranslation' => $this->module->l('No'),
            'deleteRuleMessage' => $this->module->l('Do you really want to delete the rule ?'),
            'modifyTranslation' => $this->module->l('Modify'),
            'deleteTranslation' => $this->module->l('Delete'),
            'percentTranslation' => $this->module->l('Percent'),
            'amountTranslation' => $this->module->l('Amount'),
            'noPricingRuleMessage' => $this->module->l('No pricing rules.'),
            'disclaimerMessage' => $this->module->l('The value has to be a number')
        ));

        $this->checkUrl();

        return $this->module->display(
            _PS_MODULE_DIR_.$this->module->name,
            'views/templates/admin/'.static::TEMPLATE.'.tpl'
        );
    }

    /**
     * @inheritdoc
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS($this->module->getPathUri().'views/js/admin/main.js');
        $this->addJS($this->module->getPathUri().'views/js/admin/'.static::TEMPLATE.'.js');
        $this->addCSS($this->module->getPathUri().'views/css/main.css');
        $this->addCSS($this->module->getPathUri().'views/css/font-awesome.min.css');

        $this->addCSS(sprintf(
            $this->module->getPathUri().'views/css/main-1-7-8-%s.css',
            Tools::version_compare(_PS_VERSION_, '1.7.8', '<')
                ? 'earlier'
                : 'later'
        ));
    }

    /**
     * Check the current URL and redirect to a valid URL if necessary.
     *
     * @return void
     */
    private function checkUrl()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            return;
        }

        $currentUrl = (
            getenv('HTTPS')
            || (getenv('HTTP_X_FORWARDED_PROTO') == 'https')
        ) ? 'https://' : 'http://';

        $currentUrl .= getenv('HTTP_HOST') . getenv('REQUEST_URI');

        $validUrl = Tools::getShopDomain(true, true);
        $validUrl .= basename(_PS_ADMIN_DIR_).'/';
        $validUrl .= $this->context->link->getAdminLink(
            Tools::getValue('controller')
        );

        if ($currentUrl != $validUrl) {
            Tools::redirect($validUrl);
            exit;
        }
    }
}
