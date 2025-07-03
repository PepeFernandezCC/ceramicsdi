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

use Dingedi\PsTranslationsApi\DgSameTranslations;

abstract class AbstractLangFileTranslationSource implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed[]
     */
    protected $langTo;

    /**
     * @var \Dingedi\PsTranslationsApi\DgSameTranslations
     */
    protected $sameTranslations;

    /**
     * AbstractLangFileTranslationSource constructor.
     * @throws \Exception
     * @param string $name
     * @param string $type
     */
    public function __construct($name, array $langTo, $type)
    {
        $name = (string) $name;
        $type = (string) $type;
        $this->langTo = $langTo;
        $this->name = $name;
        $this->type = $type;

        $this->sameTranslations = new DgSameTranslations($type . '-' . $name);
    }

    /**
     * @return mixed[]
     */
    abstract public function getTranslations();

    /**
     * @param mixed[] $translations
     * @param int $idLangFrom
     * @param int $latin
     * @return bool
     */
    public function translateMissingTranslations($translations, $idLangFrom, $latin = 0)
    {
        if ($idLangFrom === -1) {
            $isos = \Language::getIsoIds(false);
            $isoENExist = !empty(array_filter($isos, function ($i) {
                return $i['iso_code'] === 'en';
            }));
            $isoGBExist = !empty(array_filter($isos, function ($i) {
                return $i['iso_code'] === 'gb';
            }));

            $isoLangFrom = 'en';

            if ($isoGBExist && !$isoENExist) {
                $isoLangFrom = 'gb';
            }
        } else {
            $isoLangFrom = \Dingedi\PsTools\DgTools::getLocale(\Dingedi\PsTranslationsApi\DgTranslationTools::getLanguage($idLangFrom));
        }

        $isoLangTo = \Dingedi\PsTools\DgTools::getLocale($this->langTo);

        foreach ($translations as &$translation) {
            if (in_array($isoLangFrom, array('en', 'gb')) && in_array($isoLangTo, array('en', 'gb'))) {
                $translated = $translation['value'];
            } else {
                $translated = \Dingedi\PsTranslationsApi\DgTranslateApi::translate($translation['value'], $isoLangFrom, $isoLangTo, $latin);
            }

            $translation['trad'] = $translated;

            if ($translated === $translation['value']) {
                $this->sameTranslations->addTranslations(array(
                    'i' => $this->name,
                    'f' => $translation['key'],
                    'langs' => array($idLangFrom, (int)$this->langTo['id_lang'])
                ));
            }
        }

        return $this->saveMissingTranslations($translations);
    }

    /**
     * @param mixed[] $translations
     * @return bool
     */
    abstract public function saveMissingTranslations($translations);

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $translations = $this->getTranslations();

        $missingTranslationsTotal = count($translations['missing']);
        $translationsTotal = count($translations['all']);

        if ($translationsTotal === 0) {
            $translationsPercent = 100;
        } else {
            $translationsPercent = @(($translationsTotal - $missingTranslationsTotal) / $translationsTotal) * 100;
        }

        if ($translationsPercent < 0) $translationsPercent = 0;
        if ($translationsPercent > 100) $translationsPercent = 100;

        return array(
            'type' => $this->type,
            'name' => $this->name,
            'id_lang' => (int)$this->langTo['id_lang'],

            'translations' => $translations['all'],
            'translations_total' => $translationsTotal,
            'percent' => round($translationsPercent, 2),
            'missing_translations_total' => $missingTranslationsTotal,
            'missing_translations' => $translations['missing']
        );
    }
}
