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

namespace TrustedshopsAddon\Hook;

use Context;
use TrustedshopsAddon\Model\Constant\WidgetLocation;
use TrustedshopsAddon\Service\HookService;
use TrustedshopsAddon\Utils\ServiceContainer;
use TrustedshopsClasslib\Hook\AbstractHook;

class HookLayout extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'displayHome',
        'displayProductButtons',
        'displayProductAdditionalInfo',
        'displayProductExtraContent',
        'displayProductTabContent',
        'displayProductListReviews',
        'displayLeftColumn',
        'displayRightColumn',
        'displayTop',
        'displayNav1',
        'displayFooter',
        'displayTrustbadge',
    ];

    public function displayHome($params)
    {
        if ($this->isQuickViewModal() === true) {
            return;
        }

        $widgetModels = $this->getWidgetModels(WidgetLocation::HOME_PAGE);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayProductButtons($params)
    {
        return $this->displayProductAdditionalInfo($params);
    }

    public function displayProductAdditionalInfo($params)
    {
        if ($this->isQuickViewModal() === true) {
            return;
        }

        $widgetModels = '';
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $widgetModels = $this->getWidgetModels(WidgetLocation::PRODUCT_DESCRIPTION, [
                'id_product' => $this->getIdProduct($params),
            ]);
        }

        $widgetModels .= $this->displayProductNameInfos($params);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    private function displayProductNameInfos($params)
    {
        $widgetModels = $this->getWidgetModels(WidgetLocation::PRODUCT_NAME, [
            'id_product' => $this->getIdProduct($params),
        ]);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayProductExtraContent($params)
    {
        if ($this->isQuickViewModal() === true) {
            return;
        }

        $result = [];

        $widgetModels = $this->getWidgetModels(WidgetLocation::PRODUCT_PAGE, [
            'id_product' => $params['product']->id,
        ]);

        if (empty($widgetModels)) {
            return $result;
        }

        $tabName = $this->l('Trusted Shops Reviews');
        $productExtraContent = new \PrestaShop\PrestaShop\Core\Product\ProductExtraContent();
        $result[] = $productExtraContent->setTitle($tabName)
            ->setContent($widgetModels);

        return $result;
    }

    public function displayProductTabContent($params)
    {
        if ($this->isQuickViewModal() === true) {
            return;
        }

        $widgetModels = '';
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $widgetModels = $this->getWidgetModels(WidgetLocation::PRODUCT_DESCRIPTION, [
                'id_product' => $params['product']->id,
            ]);
        }

        $widgetModels .= $this->getWidgetModels(WidgetLocation::PRODUCT_PAGE, [
            'id_product' => $params['product']->id,
        ]);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayProductListReviews($params)
    {
        $widgetModels = $this->getWidgetModels(
            WidgetLocation::PRODUCT_LIST,
            [
                'id_product' => $params['product']['id_product'],
            ]
        );

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayLeftColumn($params)
    {
        $widgetModels = $this->getWidgetModels(WidgetLocation::LEFT_RIGHT_MARGIN);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayRightColumn($params)
    {
        $widgetModels = $this->getWidgetModels(WidgetLocation::LEFT_RIGHT_MARGIN);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayNav1($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return;
        }

        $widgetModels = $this->getWidgetModels(WidgetLocation::HEADER);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayTop($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return;
        }

        $widgetModels = $this->getWidgetModels(WidgetLocation::HEADER);

        if (empty($widgetModels)) {
            return;
        }

        return $widgetModels;
    }

    public function displayFooter($params)
    {
        $widgetModels = $this->getWidgetModels(WidgetLocation::FOOTER);

        if (empty($widgetModels)) {
            return '';
        }

        return $widgetModels;
    }

    public function displayTrustbadge()
    {
        if (\Tools::getValue('content_only') !== false) {
            return;
        }

        if (\Tools::getValue('ajax') !== false) {
            return;
        }

        /** @var HookService $hookService */
        $hookService = ServiceContainer::getInstance()->get(HookService::class);

        $trustbadgeModels = $hookService->getTrustbadge(
            Context::getContext()->shop->id,
            Context::getContext()->language->id
        );

        $file = _PS_MODULE_DIR_ . 'trustedshopseasyintegration';
        $cssLoaderLink = Context::getContext()->link->getMediaLink(
            '/modules/trustedshopseasyintegration/views/js/front/css-loader.' . $this->module->version . '.js'
        );
        Context::getContext()->smarty->assign([
            'jsPathCssLoader' => $cssLoaderLink,
        ]);
        $html = $this->module->display(
            $file,
            'views/templates/front/css-loader.tpl'
        );

        if (empty($trustbadgeModels)) {
            return $html;
        }

        Context::getContext()->smarty->assign([
            'trustbadgeModels' => $trustbadgeModels,
        ]);

        return $html . $this->module->display(
            $file,
            'views/templates/front/trustbadge.tpl'
        );
    }

    protected function getWidgetModels($location, $params = [])
    {
        /** @var HookService $hookService */
        $hookService = ServiceContainer::getInstance()->get(HookService::class);

        $widgetModels = $hookService->getWidget(
            Context::getContext()->shop->id,
            Context::getContext()->language->id,
            $location,
            $params
        );

        if (empty($widgetModels)) {
            return null;
        }

        Context::getContext()->smarty->assign([
            'widgetModels' => $widgetModels,
            'location' => $location,
            'isPs17' => version_compare(_PS_VERSION_, '1.7', '>='),
        ]);

        $file = _PS_MODULE_DIR_ . 'trustedshopseasyintegration';

        return $this->module->display(
            $file,
            'views/templates/front/widget.tpl'
        );
    }

    private function isQuickViewModal()
    {
        $controller = Context::getContext()->controller;

        if (isset($controller->isQuickView) && $controller->isQuickView === true) {
            return true;
        }

        if (\Tools::getValue('action') !== false && \Tools::getValue('action') === 'quickview') {
            return true;
        }

        if (\Tools::getValue('ajax') !== false) {
            return true;
        }

        return false;
    }

    private function getIdProduct($params)
    {
        if (isset($params['product']) && \is_array($params['product'])) {
            return $params['product']['id'];
        }

        if (isset($params['product']) && isset($params['product']->id)) {
            return $params['product']->id;
        }

        if (\Tools::getValue('id_product') !== false) {
            return \Tools::getValue('id_product');
        }
    }
}
