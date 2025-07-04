<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Install;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Configuration\moduleConfiguration;

class installTab implements installInterface
{
    /**
     * method install of module
     *
     * @param mixed $mParam
     *
     * @return bool $isInstalled : true => validate install, false => invalidate install
     */
    public static function install($mParam = null)
    {
        $isInstalled = false;

        foreach (moduleConfiguration::GMCP_TABS as $tab_data) {
            $tabId = (int) \Tab::getIdFromClassName($tab_data['class_name']);
            if (!$tabId) {
                $tabId = null;
            }
            $tab = new \Tab($tabId);
            $tab->active = 1;
            $tab->class_name = $tab_data['class_name'];
            foreach (\Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = !empty($tab_data['name'][$lang['iso_code']]) ? $tab_data['name'][$lang['iso_code']] : $tab_data['name']['en'];
            }
            if (!empty($tab_data['parent_class_name'])) {
                $tab->id_parent = (int) \Tab::getIdFromClassName($tab_data['parent_class_name']);
            } else {
                if (empty($tab_data['hide'])) {
                    $tab->id_parent = 0;
                } else {
                    $tab->id_parent = -1;
                }
            }

            $tab->icon = $tab_data['icon'];
            $tab->module = \GMerchantCenterPro::$oModule->name;
            if ($tab->save()) {
                $isInstalled = true;
            }
        }

        return $isInstalled;
    }

    /**
     * method uninstall of module
     *
     * @param mixed $mParam
     *
     * @return bool $bReturn : true => validate uninstall, false => invalidate uninstall
     */
    public static function uninstall($mParam = null)
    {
        // set return execution
        $bReturn = true;

        // loop on each admin tab
        foreach (moduleConfiguration::GMCP_TABS as $sAdminClassName => $aTab) {
            // get ID
            $iTabId = Tab::getIdFromClassName($sAdminClassName);

            if (!empty($iTabId)) {
                // instantiate
                $oTab = new Tab($iTabId);

                // use case - check delete
                if (false == $oTab->delete()) {
                    $bReturn = false;
                } else {
                    if (!defined('_PS_IMG_DIR')) {
                        define('_PS_IMG_DIR', _PS_ROOT_DIR_ . '/img/');
                    }
                    if (file_exists(_PS_IMG_DIR . 't/' . $sAdminClassName . '.gif')) {
                        @unlink(_PS_IMG_DIR . 't/' . $sAdminClassName . '.gif');
                    }
                }
                unset($oTab);
            }
        }
        unset($mParam);

        return $bReturn;
    }
}
