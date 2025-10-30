<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_6_6_7($module)
{
    Shop::isFeatureActive() && Shop::setContext(Shop::CONTEXT_ALL);

    return $module->registerHook('actionOutputHTMLBefore')
        && $module->unregisterHook('filterCmsContent')
        && $module->unregisterHook('filterProductContent')
        && $module->unregisterHook('filterCategoryContent');
}
