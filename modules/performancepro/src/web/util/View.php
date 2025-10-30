<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\web\util;

use PrestaShop\Module\PerformancePro\domain\service\util\ContextService;
use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;
use PrestaShop\Module\PerformancePro\resources\config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class View
{
    public function displayTable(array $array, bool $table = true, bool $top = false): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_dataHtml' => $array, // This contains HTML, but it is already escaped at this point.
            'pp_table' => $table,
            'pp_top' => $top,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayTable.tpl');
    }

    public function displayHeader(string $text, bool $noTop = false): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_text' => $text,
            'pp_noTop' => $noTop,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayHeader.tpl');
    }

    /**
     * @param array<string> $array
     */
    public function displayList(array $array, string $class = ''): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_array' => $array,
            'pp_class' => $class,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayList.tpl');
    }

    public function displayBtnLink(string $text, string $href): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_html' => $text, // This contains HTML, but it is already escaped at this point.
            'pp_href' => $href,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayBtnLink.tpl');
    }

    public function displayLabelInfo(string $text): string
    {
        return $this->displayLabel('info', $text);
    }

    public function displayLabel(string $type, string $text): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_type' => $type,
            'pp_text' => $text,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayLabel.tpl');
    }

    public function displayLabelSuccess(string $text): string
    {
        return $this->displayLabel('success', $text);
    }

    public function displayMagicIcon(): string
    {
        return $this->displayIcon('magic');
    }

    public function displayIcon(string $icon): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_icon' => $icon,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayIcon.tpl');
    }

    public function displayBoltIcon(): string
    {
        return $this->displayIcon('bolt');
    }

    public function displayInformationIcon(): string
    {
        return $this->displayIcon('info-circle');
    }

    public function displayWarningIcon(): string
    {
        return $this->displayIcon('warning');
    }

    public function displayAlign(string $text): string
    {
        $align = ContextService::getLanguage()->is_rtl ? 'left' : 'right';

        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_align' => $align,
            'pp_html' => $text, // This contains HTML, but it is already escaped at this point.
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayAlign.tpl');
    }

    public function displayBtnAjax(
        string $technicalName,
        string $displayName,
        string $confMsg,
        $key = null
    ): string {
        $link = LinkService::createCronLink($technicalName, $key, true);

        $id = \Tools::hashIV($technicalName . $key);

        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_id' => $id,
            'pp_link' => $link,
            'pp_confMsg' => $confMsg,
            'pp_displayNameHtml' => $displayName, // This contains HTML, but it is already escaped at this point.
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayBtnAjax.tpl');
    }

    public function displayLink(string $href, $link = null, bool $blank = true): string
    {
        if (null === $link) {
            $link = $href;
        }

        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_href' => $href,
            'pp_link' => $link,
            'pp_blank' => $blank,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayLink.tpl');
    }

    public function displayMonospaceLink(string $text, bool $copy = false): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_text' => $text,
            'pp_copy' => $copy,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/displayMonospaceLink.tpl');
    }

    public function formatStrong(string $text): string
    {
        $smarty = ContextService::getSmarty();

        $smarty->assign([
            'pp_text' => $text,
        ]);

        return $smarty->fetch(_PS_MODULE_DIR_ . Config::MODULE_NAME . '/views/templates/admin/formatStrong.tpl');
    }

    /**
     * @return string[]
     */
    public function filterModules(array $modules): array
    {
        $result = [];

        foreach ($modules as $module) {
            if (\Module::isEnabled($module)) {
                $result[] = \Module::getModuleName($module) . ' (' . $module . ')';
            }
        }

        return $result;
    }
}
