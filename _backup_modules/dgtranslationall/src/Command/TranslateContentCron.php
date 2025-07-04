<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class TranslateContentCron
{
    public static function translate($from_lang, $dest_lang, $tables, $overwrite, $range)
    {
        $fromLangId = \Language::getIdByIso($from_lang);

        if (\Validate::isLoadedObject(new \Language($fromLangId)) === false) {
            throw new \Exception(sprintf('%s is not a valid iso code.', $from_lang));
        }

        if (!in_array($overwrite, ['on', 'off'])) {
            throw new \Exception('Overwrite must be set "on" or "off"');
        }

        $overwrite = $overwrite === "on";

        $module = \Module::getInstanceByName('dgtranslationall');
        $module->initContent(true);

        $destLang = $dest_lang;
        $langsToTranslate = [];

        if (strpos($destLang, ',') !== false) {
            $langsToTranslate = explode(',', $destLang);
        } else {
            $langsToTranslate[] = $destLang;
        }

        $tables = self::parseTables($tables);

        $perRequest = \Dingedi\PsTranslationsApi\DgTranslationTools::getPerRequest() * 10;

        \Configuration::set('dingedi_per_request', $perRequest);

        foreach ($langsToTranslate as $langDest) {
            $destLangId = \Language::getIdByIso($langDest);

            if (\Validate::isLoadedObject(new \Language($destLangId)) === false) {
                throw new \Exception(sprintf('%s is not a valid iso code.', $langDest));
            }

            foreach ($tables as $tableName => $fields) {
                $table = $module->getContentTable($tableName);

                $requests = ceil($table->getTotalItems() / $perRequest);

                $_POST['translation_data'] = array();

                if($range !== 'off') {
                    $ids = explode(':', $range);

                    $_POST['translation_data']['plage_enabled'] = 'true';
                    $_POST['translation_data']['start_id'] = $ids[0];
                    $_POST['translation_data']['end_id'] = $ids[1];
                }

                if (is_array($fields)) {
                    $_POST['translation_data']['selected_fields'] = $fields;
                }

                for ($i = 1; $i <= $requests; $i++) {
                    $module->translateContentTable($tableName, $fromLangId, $destLangId, 0, $overwrite, $i);
                }
            }
        }
    }

    private static function parseTables($tables)
    {
        if ($tables === "*") {
            return self::getAllTablesList();
        }

        $r = [];

        foreach (explode('|', $tables) as $table) {
            $e = explode(':', $table);

            if (isset($e[1])) {
                $r[$e[0]] = explode(',', $e[1]);
            } else {
                $r[$e[0]] = '';
            }
        }

        return $r;
    }

    /**
     * @return mixed[]
     */
    private static function getAllTablesList()
    {
        $tables = [];

        $contentTablesGroups = \Module::getInstanceByName('dgtranslationall')->getContentTables();

        foreach ($contentTablesGroups as $group) {
            foreach ($group['tables'] as $table) {
                $tables[$table->getTableName(false)] = '';
            }
        }

        return $tables;
    }

}
