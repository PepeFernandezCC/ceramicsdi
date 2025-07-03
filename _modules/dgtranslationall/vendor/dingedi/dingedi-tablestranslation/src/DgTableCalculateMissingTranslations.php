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
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\TablesTranslation;

use Dingedi\PsTranslationsApi\DgSameTranslations;

const BIG_TABLE_LIMIT = 6000;

class DgTableCalculateMissingTranslations
{

    /** @var DgSameTranslations $dgSameTranslations
     * @readonly */
    private $dgSameTranslations;

    /** @var int $default_language_id
     * @readonly */
    private $default_language_id;

    /** @var int $id_shop
     * @readonly */
    private $id_shop;
    /**
     * @readonly
     * @var \Dingedi\TablesTranslation\DgTableTranslatable16
     */
    private $dgTableTranslatable;

    /**
     * DgTableCalculateMissingTranslations constructor.
     */
    public function __construct(DgTableTranslatable16 $dgTableTranslatable)
    {
        $this->dgTableTranslatable = $dgTableTranslatable;
        $this->dgSameTranslations = new \Dingedi\PsTranslationsApi\DgSameTranslations('tables-' . $dgTableTranslatable->getTableName(false));
        $this->default_language_id = \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId();
        $this->id_shop = (int)\Context::getContext()->shop->id;
    }

    /**
     * @throws \Exception
     * @param int|false $current
     * @param mixed[] $language
     * @return mixed[]
     */
    public function getTranslationsPercent($language, $current = false)
    {
        $defaultLanguage = \Language::getLanguage((int)$this->default_language_id);

        // set default source language
        if (empty($defaultLanguage['id_lang'])) {
            $defaultLanguage = \Language::getLanguage((int)\Language::getLanguages(false)[0]['id_lang']);
        }

        if ((int)$language['id_lang'] === (int)$defaultLanguage['id_lang']) {
            return array('skip' => true);
        }

        $limit = null;
        $offset = null;

        if (is_int($current)) {
            $limit = BIG_TABLE_LIMIT;
            $offset = ($current - 1) * BIG_TABLE_LIMIT;
        }

        // source items
        $defaultLanguageItems = $this->dgTableTranslatable->findAll(array(
            'id_lang' => (int)$defaultLanguage['id_lang']
        ), $limit, $offset);

        $fields = $this->dgTableTranslatable->getFields();

        // total fields
        $total = count($fields) * (is_array($defaultLanguageItems) || $defaultLanguageItems instanceof \Countable ? count($defaultLanguageItems) : 0);

        // language items
        $languageItems = $this->dgTableTranslatable->findAll(array(
            'id_lang' => (int)$language['id_lang']
        ), $limit, $offset);

        $translated = 0;

        $tableKey = $this->dgTableTranslatable->getPrimaryKey();
        $columns = array_column($defaultLanguageItems, $tableKey);

        $missingCharactersToTranslate = 0;
        $totalCharacters = 0;

        foreach ($languageItems as $k => $v) {
            $defaultItem = $defaultLanguageItems[array_search($v[$tableKey], $columns)];

            foreach ($fields as $field) {

                // column doesnt exist
                if (!array_key_exists($field, $defaultItem) || !array_key_exists($field, $v)) {
                    $total--;
                    continue;
                }

                $vfield = $v[$field];
                $dfield = $defaultItem[$field];

                $totalCharacters += \Tools::strlen($dfield);

                // empty source/dest doesnt count it
                if (($vfield === "" && $dfield === "") || (trim((string)$vfield) === "" && trim((string)$dfield) === "")) {
                    $total--;
                    continue;
                }

                // different value, possible translated
                if ($vfield !== $dfield && $vfield !== "") {
                    $translated++;
                    continue;
                }

                if (
                    !$this->dgSameTranslations->needTranslation($v[$tableKey], $field . '-' . md5($dfield), array($defaultLanguage['id_lang'], $language['id_lang']))
                    ||
                    !$this->dgSameTranslations->needTranslation($v[$tableKey], $field, array($defaultLanguage['id_lang'], $language['id_lang'])) // temp fix for percentage display
                ) {
                    $translated++;
                    continue;
                }

                if (is_numeric($vfield) && is_numeric($dfield) && (int)$vfield === (int)$dfield) {
                    $translated++;
                    continue;
                }

                $missingCharactersToTranslate += \Tools::strlen($dfield);
            }
        }

        if ($total === 0) {
            $translationsPercent = 100;
        } else {
            $translationsPercent = @(($total - ($total - $translated)) / $total) * 100;
        }

        $percent = round($translationsPercent, 2);

        if ($percent > 100) {
            $percent = 100;
        }

        return array(
            'id_lang'            => (int)$language['id_lang'],
            'locale'             => $language['iso_code'],
            'percent'            => $percent,
            'class_name'         => $this->getPercentClassName($percent),
            'missing_characters' => $missingCharactersToTranslate,
            'total_characters'   => $totalCharacters
        );
    }

    /**
     * Get classname for percent
     * @param int $percent
     * @return string
     */
    private function getPercentClassName($percent)
    {
        $percent = (int) $percent;
        $className = 'success';

        if ($percent < 20) {
            $className = 'danger';
        } else if ($percent < 40) {
            $className = 'danger-hover';
        } else if ($percent < 70) {
            $className = 'warning-hover';
        } else if ($percent < 100) {
            $className = 'success-hover';
        }

        return $className;
    }
}
