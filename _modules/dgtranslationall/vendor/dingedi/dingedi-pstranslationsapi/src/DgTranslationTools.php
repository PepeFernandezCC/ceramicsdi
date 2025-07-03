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

namespace Dingedi\PsTranslationsApi;

use Dingedi\PsTranslationsApi\Configuration\AutomaticTranslationConfiguration;
use Dingedi\PsTranslationsApi\TranslationsProviders\AbstractTranslationProvider;
use Dingedi\PsTranslationsApi\TranslationsProviders\ChatGPTV1;
use Dingedi\PsTranslationsApi\TranslationsProviders\DeepLTranslateV2;
use Dingedi\PsTranslationsApi\TranslationsProviders\DingediFreeOffer;
use Dingedi\PsTranslationsApi\TranslationsProviders\DingediTranslateV1;
use Dingedi\PsTranslationsApi\TranslationsProviders\GoogleTranslateV2;
use Dingedi\PsTranslationsApi\TranslationsProviders\LectoAIV1;
use Dingedi\PsTranslationsApi\TranslationsProviders\LibreTranslateV1;
use Dingedi\PsTranslationsApi\TranslationsProviders\MicrosoftTranslateV3;
use Dingedi\PsTranslationsApi\TranslationsProviders\YandexTranslateV15;
use Dingedi\PsTranslationsApi\TranslationsProviders\YandexTranslateV2;
use Dingedi\PsTools\DgTools;

class DgTranslationTools
{
    static $modulesName = array('dgcontenttranslation', 'dgcreativeelementstranslation', 'dgtranslationall');

    /**
     *  Return api key of default provider if none provider is pass in parameter
     *
     * @param string $provider api provider
     * @return string
     */
    public static function getApiKey($provider = null)
    {
        if ($provider === null) {
            $provider = self::getProvider();
        }

        return DgTools::getValue('dingedi_provider_' . $provider);
    }

    /**
     *  Get current translation provider
     * @return \Dingedi\PsTranslationsApi\TranslationsProviders\AbstractTranslationProvider|string
     * @param bool $asString
     */
    public static function getProvider($asString = true)
    {
        $provider = DgTools::getValue('dingedi_provider_name');

        if ($asString) {
            return $provider;
        } else {
            foreach (self::getProvidersList() as $p) {
                if ($p->key === $provider) {
                    return $p;
                }
            }
        }
    }

    /**
     * @param mixed $default_value
     * @return mixed
     * @param string $key
     */
    public static function getValue($key, $default_value = '')
    {
        if (\Dingedi\PsTools\DgShopInfos::isPrestaShop17()) {
            return \Configuration::get($key, null, null, null, $default_value);
        } else {
            $value = \Configuration::get($key, null, null, null);

            if ($value === false) {
                return $default_value;
            }

            return $value;
        }
    }

    /**
     *  Get list of translation providers with API Key if defines
     *
     * @return array<AbstractTranslationProvider>
     */
    public static function getProvidersList()
    {
        $providers = array(
            ProviderSingleton::getInstance()->get(new DingediTranslateV1()),
            ProviderSingleton::getInstance()->get(new DeepLTranslateV2()),
            ProviderSingleton::getInstance()->get(new GoogleTranslateV2()),
            ProviderSingleton::getInstance()->get(new ChatGPTV1()),
            ProviderSingleton::getInstance()->get(new MicrosoftTranslateV3()),
            ProviderSingleton::getInstance()->get(new LibreTranslateV1()),
            ProviderSingleton::getInstance()->get(new YandexTranslateV2()),
            ProviderSingleton::getInstance()->get(new LectoAIV1()),
            ProviderSingleton::getInstance()->get(new YandexTranslateV15()),
        );

        $dingediFreeOffer = new DingediFreeOffer();

        if (
            (DgLeaveReview::getApiKey() === "")
            ||
            (DgLeaveReview::getApiKey() !== "" && (DgLeaveReview::canReview() || (DgLeaveReview::hasReview() && DgLeaveReview::hasFreeChars())))
        ) {
            $providers = array_merge([ProviderSingleton::getInstance()->get($dingediFreeOffer)], $providers);
        } else if (self::getProvider() === $dingediFreeOffer->key) {
            \Configuration::updateValue('dingedi_provider_name', 'dingedi_v1');
        }

        return $providers;
    }

    /**
     * @return int
     */
    public static function getDefaultLangId()
    {
        $defaultLangId = (int)DgTools::getValue('dingedi_default_lang', DgTools::getValue('PS_LANG_DEFAULT'));

        if (is_array(\Language::getLanguage($defaultLangId))) {
            return $defaultLangId;
        }

        return Language::getLanguages()[0]['id_lang'];
    }

    /**
     * @throws \Exception
     * @return void
     */
    public static function saveSettings()
    {
        $data = \Tools::getValue('translation_data');

        $config = array();

        if (isset($data['exclude'])) {
            $config['dingedi_exclude'] = (string)$data['exclude'];
        }

        if (isset($data['excluded'])) {
            $excluded = (!is_array($data['excluded'])) ? [] : $data['excluded'];
            $excluded = implode(',', array_unique(array_map(function ($i) {
                return trim((string)$i);
            }, $excluded)));
            $config['dingedi_excluded'] = $excluded;
        }

        if (isset($data['per_request'])) {
            $config['dingedi_per_request'] = (string)$data['per_request'];
        }

        if (isset($data['translation_filter'])) {
            $config['dingedi_translation_filter'] = (string)$data['translation_filter'];
        }

        if (isset($data['automatic_translation'])) {
            $config['dingedi_automatic_translation'] = $data['automatic_translation'] === "true";
        }

        if (isset($data['translation_modal_enabled'])) {
            $config['dingedi_translation_modal_enabled'] = $data['translation_modal_enabled'] === "true";
        }

        if (isset($data['translation_fields_enabled'])) {
            $config['dingedi_translation_fields_enabled'] = $data['translation_fields_enabled'] === "true";
        }

        if (isset($data['module_only_load_installed'])) {
            $config['dingedi_module_only_load_installed'] = $data['module_only_load_installed'] === "true";
        }

        if (isset($data['module_only_load_enabled'])) {
            $config['dingedi_module_only_load_enabled'] = $data['module_only_load_enabled'] === "true";
        }

        if (isset($data['auto_backup'])) {
            $config['dingedi_auto_backup'] = $data['auto_backup'] === "true" ? "1" : "0";
        }

        if (isset($data['translation_fields_always_enabled'])) {
            $config['dingedi_translation_fields_always_enabled'] = $data['translation_fields_always_enabled'] === "true";
        }

        if (isset($data['automatic_translation'])) {
            $automaticTranslationData = $data['automatic_translation'];
            (new AutomaticTranslationConfiguration)->updateFromArray($automaticTranslationData);
        }

        if (isset($data['smart_dictionary'])) {
            $config['dingedi_smart_dictionary'] = json_encode($data['smart_dictionary'], 0);
        }

        if (!empty($config)) {
            self::saveConfigurationArray($config);
        }
    }

    /**
     * @param mixed[] $array
     * @param bool $temporary
     * @return void
     */
    public static function saveConfigurationArray($array, $temporary = false)
    {
        if (isset($array['dingedi_smart_dictionary']) || isset($array['dingedi_excluded'])) {
            \Dingedi\PsTranslationsApi\TranslationsCache::clearCache();
        }
        if (\Shop::isFeatureActive() && \Shop::getContextShopGroup()->id === null) {
            foreach ($array as $k => $v) {
                if ($temporary) {
                    \Configuration::set($k, $v, 0, 0);
                } else {
                    \Configuration::updateGlobalValue($k, $v);
                }
            }
        } else {
            foreach ($array as $k => $v) {
                if ($temporary) {
                    \Configuration::set($k, $v);
                } else {
                    \Configuration::updateValue($k, $v);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public static function isAutoBackupEnabled()
    {
        return DgTools::getValue('dingedi_auto_backup', '0') === "1";
    }

    /**
     * @return bool
     */
    public static function automaticTranslationTranslateAll()
    {
        return (new AutomaticTranslationConfiguration())->get('translate_all');
    }

    /**
     * @return bool
     */
    public static function automaticTranslationForAddition()
    {
        return (new AutomaticTranslationConfiguration())->get('enabled_for_addition');
    }

    /**
     * @return bool
     */
    public static function automaticTranslationForUpdate()
    {
        return (new AutomaticTranslationConfiguration())->get('enabled_for_update');
    }

    /**
     * @return mixed[]|bool
     * @param string $tableName
     */
    public static function automaticTranslationGetFields($tableName)
    {
        $automaticTranslationConfiguration = (new AutomaticTranslationConfiguration);

        $tables = json_decode((string)$automaticTranslationConfiguration->get('translate_tables'), JSON_OBJECT_AS_ARRAY === null ?: JSON_OBJECT_AS_ARRAY, 512, 0);

        if (!isset($tables[$tableName])) {
            return false;
        }

        $fields = $tables[$tableName];

        if (empty($fields)) {
            return true;
        }

        return $fields;
    }

    /**
     * Save apikeys
     * @param bool $temporary
     * @return void
     */
    public static function saveApiKeys($temporary = false)
    {
        $data = \Tools::getValue('translation_data');

        if (\Dingedi\PsTools\DgTools::hasParameters($data, array('apiKeys', 'defaultProvider'))) {
            $apikeys = $data['apiKeys'];
            $provider = (string)$data['defaultProvider'];

            $config = [
                'dingedi_provider_name' => $provider
            ];

            foreach ($apikeys as $apikey) {
                $config['dingedi_provider_' . (string)$apikey['key']] = (string)$apikey['api_key'];

                if (isset($apikey['configuration'])) {
                    foreach ($apikey['configuration'] as $k => $v) {
                        $config['dingedi_provider_' . $apikey['key'] . '_' . $k] = $v;
                    }
                }
            }

            self::saveConfigurationArray($config, $temporary);
        }
    }

    /**
     * @return bool
     */
    public static function install()
    {
        \Configuration::updateValue('dingedi_smart_dictionary', DgTools::getValue('dingedi_smart_dictionary', '[]'));
        \Configuration::updateValue('dingedi_translation_filter', DgTools::getValue('dingedi_translation_filter', '2'));
        \Configuration::updateValue('dingedi_secret_key', sha1(uniqid(rand(0, mt_getrandmax()), true)) . rand(0, mt_getrandmax()));
        \Configuration::updateValue('dingedi_per_request', DgTools::getValue('dingedi_per_request', '10'));
        \Configuration::updateValue('dingedi_resume_tr', DgTools::getValue('dingedi_resume_tr', 'false'));
        \Configuration::updateValue('dingedi_default_lang', DgTools::getValue('dingedi_default_lang', (int)DgTools::getValue('PS_LANG_DEFAULT')));

        \Configuration::updateValue('dingedi_automatic_translation', DgTools::getValue('dingedi_automatic_translation', 0));
        \Configuration::updateValue('dingedi_translation_modal_enabled', DgTools::getValue('dingedi_translation_modal_enabled', 1));
        \Configuration::updateValue('dingedi_translation_fields_enabled', DgTools::getValue('dingedi_translation_fields_enabled', 1));
        \Configuration::updateValue('dingedi_translation_fields_always_enabled', DgTools::getValue('dingedi_translation_fields_always_enabled', 0));
        \Configuration::updateValue('dingedi_module_only_load_installed', DgTools::getValue('dingedi_module_only_load_installed', 1));
        \Configuration::updateValue('dingedi_module_only_load_enabled', DgTools::getValue('dingedi_module_only_load_enabled', 1));
        \Configuration::updateValue('dingedi_auto_backup', DgTools::getValue('dingedi_auto_backup', "1"));

        (new AutomaticTranslationConfiguration)->install();

        $providers = self::getProvidersList();
        foreach ($providers as $provider) {
            $provider->configuration->install();
            \Configuration::updateValue('dingedi_provider_' . $provider->key, DgTools::getValue('dingedi_provider_' . $provider->key, ''));
        }

        \Configuration::updateValue('dingedi_provider_name', DgTools::getValue('dingedi_provider_name', (new DingediTranslateV1())->key));

        \Configuration::updateValue('dingedi_exclude', DgTools::getValue('dingedi_exclude', 'true'));
        \Configuration::updateValue('dingedi_excluded', DgTools::getValue('dingedi_excluded', implode(',', self::getShopManufacturers())));

        $sql = array();

        $sql[] = \Dingedi\PsTranslationsApi\models\FailedTranslation::getInstallSql();
        $sql[] = \Dingedi\PsTranslationsApi\DgSameTranslations::getInstallSql();

        foreach ($sql as $s) {
            if (!\Db::getInstance()->execute($s)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed[]
     */
    public static function getShopManufacturers()
    {
        $manufacturers = array_map(function ($manufacturer) {
            return $manufacturer['name'];
        }, \Manufacturer::getManufacturers());

        return array_values(array_unique($manufacturers));
    }

    /**
     * @param string $module_name
     * @return bool
     */
    public static function uninstall($module_name)
    {
        if (self::isOtherTranslationsModuleInstalled($module_name)) {
            return true;
        }

        \Configuration::deleteByName('dingedi_smart_dictionary');
        \Configuration::deleteByName('dingedi_translation_filter');
        \Configuration::deleteByName('dingedi_secret_key');
        \Configuration::deleteByName('dingedi_per_request');
        \Configuration::deleteByName('dingedi_resume_tr');
        \Configuration::deleteByName('dingedi_default_lang');

        $providers = self::getProvidersList();

        foreach ($providers as $provider) {
            $provider->configuration->uninstall();
            \Configuration::deleteByName('dingedi_provider_' . $provider->key);
        }

        \Configuration::deleteByName('dingedi_provider_name');

        \Configuration::deleteByName('dingedi_exclude');
        \Configuration::deleteByName('dingedi_excluded');

        \Configuration::deleteByName('dingedi_automatic_translation');
        \Configuration::deleteByName('dingedi_translation_modal_enabled');
        \Configuration::deleteByName('dingedi_translation_fields_enabled');
        \Configuration::deleteByName('dingedi_translation_fields_always_enabled');
        \Configuration::deleteByName('dingedi_module_only_load_installed');
        \Configuration::deleteByName('dingedi_module_only_load_enabled');
        \Configuration::deleteByName('dingedi_auto_backup');

        (new AutomaticTranslationConfiguration)->uninstall();

        $sql = array();
        $sql[] = 'SET foreign_key_checks = 0;';
        $sql[] = \Dingedi\PsTranslationsApi\models\FailedTranslation::getUninstallSql();
        $sql[] = \Dingedi\PsTranslationsApi\DgSameTranslations::getUninstallSql();
        $sql[] = 'SET foreign_key_checks = 1;';

        foreach ($sql as $s) {
            if (!\Db::getInstance()->execute($s)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $module_name
     * @return bool
     */
    public static function isOtherTranslationsModuleInstalled($module_name)
    {
        try {
            $modulesList = array_map(function ($module) {
                return $module['name'];
            }, \Module::getModulesInstalled());

            if (!array_intersect(array_diff($modulesList, array($module_name)), self::$modulesName)) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return mixed[]
     */
    public static function getTranslationsConfiguration()
    {
        return array(
            'providers' => self::getProvidersList(),
            'default' => self::getProvider(),
            'manufacturers' => self::getShopManufacturers(),
            'exclude' => self::getExcludeStatut(),
            'excluded' => self::getExcludedWords(),
            'per_request' => self::getPerRequest(),
            'auto_backup' => self::isAutoBackupEnabled(),
            'resume_tr' => self::getResumeTr(),
            'translation_fields_enabled' => self::getTranslationFieldsEnabled(),
            'translation_fields_always_enabled' => self::getTranslationFieldsAlwaysEnabled(),
            'translation_modal_enabled' => self::getTranslationModalEnabled(),
            'module_only_load_installed' => self::getModuleOnlyLoadInstalled(),
            'module_only_load_enabled' => self::getModuleOnlyLoadEnabled(),
            'translation_filter' => self::getTranslationFilter(),
            'automatic_translation' => self::getAutomaticTranslation(),
            'smart_dictionary' => self::getSmartDictionary()
        );
    }

    /**
     * @return bool
     */
    public static function getExcludeStatut()
    {
        return DgTools::getValue('dingedi_exclude') === 'true';
    }

    /**
     * @return mixed[]
     */
    public static function getSmartDictionary()
    {
        $smartDictionary = json_decode((string)DgTools::getValue('dingedi_smart_dictionary', '[]'), true, 512, 0);

        if (!is_array($smartDictionary)) {
            $smartDictionary = [];
        }

        return $smartDictionary;
    }

    /**
     * @return mixed[]
     */
    public static function getAutomaticTranslation()
    {
        return (new AutomaticTranslationConfiguration)->jsonSerialize();
    }

    /**
     * @return string[]|string
     */
    public static function getExcludedWords()
    {
        $excluded = DgTools::getValue('dingedi_excluded', '');

        $excluded = array_values(array_unique(explode(',', (string)$excluded)));

        return (!empty($excluded)) ? $excluded : '';
    }

    /**
     * @return int
     */
    public static function getPerRequest()
    {
        $per_request = (int)DgTools::getValue('dingedi_per_request');

        if ($per_request < 1) {
            $per_request = 1;
        }

        return $per_request;
    }

    /**
     * @return string|false
     */
    public static function getResumeTr()
    {
        $resumeTr = DgTools::getValue('dingedi_resume_tr');

        if ($resumeTr === false) {
            return false;
        }

        return json_decode((string)$resumeTr, true, 512, 0);
    }

    /**
     * @return int
     */
    public static function getTranslationModalEnabled()
    {
        return (int)DgTools::getValue('dingedi_translation_modal_enabled');
    }

    /**
     * @return int
     */
    public static function getTranslationFieldsEnabled()
    {
        return (int)DgTools::getValue('dingedi_translation_fields_enabled');
    }

    /**
     * @return int
     */
    public static function getModuleOnlyLoadInstalled()
    {
        return (int)DgTools::getValue('dingedi_module_only_load_installed', '1');
    }

    /**
     * @return int
     */
    public static function getModuleOnlyLoadEnabled()
    {
        return (int)DgTools::getValue('dingedi_module_only_load_enabled', '1');
    }

    /**
     * @return int
     */
    public static function getTranslationFieldsAlwaysEnabled()
    {
        return (int)DgTools::getValue('dingedi_translation_fields_always_enabled', '0');
    }

    /**
     * @return int
     */
    public static function getTranslationFilter()
    {
        return (int)DgTools::getValue('dingedi_translation_filter');
    }

    /**
     * @return mixed[]
     */
    public static function getShopConfig()
    {
        return array(
            'PS_REWRITING_SETTINGS' => DgTools::getValue('PS_REWRITING_SETTINGS') == "1",
            'PS_ALLOW_ACCENTED_CHARS_URL' => DgTools::getValue('PS_ALLOW_ACCENTED_CHARS_URL') == "1",
            'HAS_LANGUAGES_REQUIRE_ACCENTED_CHARS' => !empty(self::getLanguagesRequireAccentedCharsUrl()),
            'LANGUAGES_REQUIRE_ACCENTED_CHARS' => self::getLanguagesRequireAccentedCharsUrl()
        );
    }

    /**
     * @return mixed[]
     */
    public static function getLanguagesRequireAccentedCharsUrl()
    {
        $default_list = array('el', 'zh', 'tw');

        $languages = array();

        foreach (\Language::getLanguages(false) as $language) {
            $name = trim(explode('(', (string)$language['name'])[0]);

            $isNonLatin = \Tools::strlen(\Tools::link_rewrite($name)) !== \Tools::strlen($name);

            if ($isNonLatin || in_array($language['iso_code'], $default_list)) {
                $languages[] = $language;
            }
        }

        return $languages;
    }

    /**
     * @param int $id_lang Language Id
     *
     * @throws \Exception
     * @return mixed[]
     */
    public static function getLanguage($id_lang)
    {
        $language = \Language::getLanguage($id_lang);

        if ($language === false) {
            throw new \Exception('Invalid language ID: ' . $id_lang);
        }

        return $language;
    }
}
