<?php
/**
 * Copyright since 2022 Trusted shops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Trusted shops
 * @license https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *
 * This source file is loading the components connector.umd.js and eventsLib.js
 * (itself subject to the Trusted Shops EULA https://policies.etrusted.com/IE/en/plugin-licence.html)  to connect to Trusted Shops. For these components, you will find below a list of the open source libraries we use for our Services.
 * Please note that the following list may be subject to amendments and modifications, and does not thus claim (perpetual) exhaustiveness. You can always refer to the following website for up-to-date information on the open source software Trusted Shops uses:
 * https://policies.etrusted.com/IE/en/plugin-licence.html
 *
 * Name                Licence         Copyright Disclaimer
 * axios               MIT     Copyright (c) 2014-present (Matt Zabriskie)
 * babel               MIT     Copyright (c) 2014-present (Sebastian McKenzie and other Contributors)
 * follow-redirects    MIT     Copyright (c) 2014–present (Olivier Lalonde, James Talmage, Ruben Verborgh)
 * history             MIT     Copyright (c) 2016-2020 (React Training), Copyright (c) 2020-2021 (Remix Software)
 * hookform/resolvers  MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * inherits            ISC     Copyright (c) 2011-2022 (Isaac Z. Schlueter)
 * js-tokens           MIT     Copyright (c) 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 (Simon Lydell)
 * lodash              MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * lodash-es           MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * loose-envify        MIT     Copyright (c) 2015 (Andreas Suarez)
 * nanoclone           MIT     Copyright (c) 2017 (Anton Kosykh)
 * path                MIT     Copyright (c) (Joyent, Inc. and other Node contributors.)
 * preact              MIT     Copyright (c) 2015-present (Jason Miller)
 * preact-router       MIT     Copyright (c) 2015 (Jason Miller)
 * process             MIT     Copyright (c) 2013 (Roman Shtylman)
 * property-expr       MIT     Copyright (c) 2014 (Jason Quense)
 * react-hook-form     MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * regenerator-runtime MIT     Copyright (c) 2014-present (Facebook, Inc.)
 * resolve-pathname    MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * tiny-invariant      MIT     Copyright (c) 2019 (Alexander Reardon)
 * tiny-warning        MIT     Copyright (c) 2019 (Alexander Reardon)
 * toposort            MIT     Copyright (c) 2012 (Marcel Klehr)
 * types/lodash        MIT     (none)
 * util                MIT     Copyright (c) (Joyent, Inc. and other Node contributors)
 * value-equal         MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * yup                 MIT     Copyright (c) 2014 (Jason Quense)
 * zustand             MIT     Copyright (c) 2019 (Paul Henschel)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'trustedshopseasyintegration/config_prod.php';
require_once _PS_MODULE_DIR_ . 'trustedshopseasyintegration/vendor/autoload.php';

class Trustedshopseasyintegration extends Module
{
    use TrustedshopsAddon\Utils\ModuleTrait {
        TrustedshopsAddon\Utils\ModuleTrait::__construct as private __mConstruct;
        TrustedshopsAddon\Utils\ModuleTrait::install as private mInstall;
        TrustedshopsAddon\Utils\ModuleTrait::uninstall as private mUninstall;
    }

    /** @var string This module requires at least PHP version */
    public $php_version_required = '5.6';

    /**
     * @var Context
     */
    public $context;

    /**
     * List of ModuleFrontController used in this Module
     * Module::install() register it, after that you can edit it in BO (for rewrite if needed)
     *
     * @var array
     */
    public $controllers = [
    ];

    /**
     * List of objectModel used in this Module
     *
     * @var array
     */
    public $objectModels = [
        \TrustedshopsChannel::class,
    ];

    public $moduleAdminControllers = [
        [
            'name' => [
                'en' => 'Trustedshops',
                'fr' => 'Trustedshops',
            ],
            'class_name' => 'trustedshopseasyintegration',
            'parent_class_name' => 'CONFIGURE',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Trustedshops',
                'fr' => 'Trustedshops',
            ],
            'class_name' => 'AdminTrustedshopseasyintegrationParent',
            'parent_class_name' => 'trustedshopseasyintegration',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Configuration',
                'fr' => 'Configuration',
            ],
            'class_name' => 'AdminTrustedshopseasyintegrationConfiguration',
            'parent_class_name' => 'AdminTrustedshopseasyintegrationParent',
            'visible' => true,
        ],
        [
            'name' => [
                'en' => 'Configuration Ajax',
                'fr' => 'Configuration Ajax',
            ],
            'class_name' => 'AdminTrustedshopseasyintegrationConfigurationAjax',
            'parent_class_name' => 'trustedshopseasyintegration',
            'visible' => false,
        ],
    ];

    const CLIENT_ID = 'TRUSTEDSHOPSEASYINTEGRATION_CLIENT_ID';

    const CLIENT_SECRET = 'TRUSTEDSHOPSEASYINTEGRATION_CLIENT_SECRET';

    const SKU_TYPE = 'TRUSTEDSHOPSEASYINTEGRATION_SKU_TYPE';

    const GTIN_TYPE = 'TRUSTEDSHOPSEASYINTEGRATION_GTIN_TYPE';

    const ORDER_STATUS_EVENTS_CONFIG = 'TRUSTEDSHOPSEASYINTEGRATION_ORDER_STATUS_EVENTS_CONFIG';

    public function __construct()
    {
        $this->module_key = 'ed69e3433cc658b9130d7e9325820bf1';
        $this->name = 'trustedshopseasyintegration';
        $this->version = '1.0.5';
        $this->author = '202 ecommerce';
        $this->tab = 'front_office_features';
        $this->ps_versions_compliancy = [
            'min' => '1.6.1.24',
            'max' => _PS_VERSION_,
        ];
        $this->need_instance = 0;

        $this->__mConstruct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->confirmUninstall = $this->l('This will delete the Trusted shops module, are you sure ?');
        $this->displayName = $this->l('Trusted Shops Easy Integration');
        $this->description = $this->l('This module integrates Trusted Shops into your Prestashop installation.');
        $this->hookDispatcher = new TrustedshopsAddon\Hook\HookDispatcher($this);
        $this->hooks = array_merge($this->hooks, $this->hookDispatcher->getAvailableHooks());
    }

    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminTrustedshopseasyintegrationConfiguration'));
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function install()
    {
        $result = $this->mInstall();
        $moduleInitializer = new \TrustedshopsAddon\Utils\ModuleInitializer();
        $result &= $moduleInitializer->initialize();

        Tools::clearSmartyCache();

        return (bool) $result;
    }

    public function uninstall()
    {
        return $this->mUninstall();
    }

    /**
     * Handle Hooks loaded on extension
     *
     * @param string $name Hook name
     * @param array $arguments Hook arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($result = $this->handleExtensionsHook($name,
            !empty($arguments[0]) ? $arguments[0] : [])
        ) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }
}
