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

class DgThemeTranslatable16 extends AbstractLangFileTranslationSource
{
    /**
     * @var mixed[]
     */
    public $language;

    /**
     * @var mixed[]
     */
    public $translations;

    /**
     * @param string $name
     */
    public function __construct($name, array $langTo)
    {
        $name = (string) $name;
        parent::__construct($name, $langTo, 'themes');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTranslations()
    {
        $_POST['type'] = 'front';
        $_POST['theme'] = $this->name;
        $_POST['iso_code'] = $this->langTo['iso_code'];

        $admin_translations_obj = new \AdminTranslationsController();
        $admin_translations_obj->getInformations();
        $admin_translations_obj->initContent();

        $translationsTabs = $admin_translations_obj->tpl_view_vars['tabsArray'];

        $translations = array();
        $missing = array();

        foreach ($translationsTabs as $tk => $translationsTab) {
            foreach ($translationsTab as $key => $value) {
                $encodedKey = \Tools::strtolower($tk) . '_' . md5($key);
                $translations[] = array(
                    'key'   => $encodedKey,
                    'value' => \Tools::stripslashes(html_entity_decode($key, ENT_COMPAT, 'UTF-8')),
                    'trad'  => \Tools::stripslashes(html_entity_decode($value['trad'], ENT_COMPAT, 'UTF-8'))
                );
            }
        }

        $isoLangTo = \Dingedi\PsTools\DgTools::getLocale($this->langTo);

        if (!in_array($isoLangTo, array('en', 'gb'))) {
            foreach ($translations as $translation) {
                if (($this->sameTranslations->needTranslation($this->name, $translation['key'], [-1, (int)$this->langTo['id_lang']]) && $translation['trad'] === $translation['value'])
                    || in_array($translation['trad'], array(null, ''))
                ) {
                    $missing[] = $translation;
                }
            }
        }

        return array(
            'all'     => $translations,
            'missing' => $missing
        );
    }

    /**
     * @throws \Exception
     * @param mixed[] $translations
     * @return bool
     */
    public function saveMissingTranslations($translations)
    {
        $translationsToSave = array();

        foreach ($this->getTranslations()['all'] as $translation) {
            $translationsToSave[$translation['key']] = $translation['trad'];
        }

        foreach ($translations as $translation) {
            $translationsToSave[$translation['key']] = $translation['trad'];
        }

        $file_path = _PS_ALL_THEMES_DIR_ . $this->name . '/lang/' . $this->langTo['iso_code'] . '.php';

        return $this->writeTranslationFile($file_path, '_LANG', $translationsToSave);
    }

    /**
     * @source AdminTranslationsController::writeTranslationFile
     * @throws \Exception
     * @param string $file_path
     * @param string $tab
     * @return bool
     */
    private function writeTranslationFile($file_path, $tab, array $translations)
    {
        $file_path = (string) $file_path;
        $tab = (string) $tab;
        if ($file_path && !file_exists($file_path)) {
            if (!file_exists(dirname($file_path)) && !mkdir(dirname($file_path), 0777, true)) {
                throw new \Exception(sprintf('Directory "%s" cannot be created', dirname($file_path)));
            } elseif (!touch($file_path)) {
                throw new \Exception(sprintf(\Tools::displayError('File "%s" cannot be created'), $file_path));
            }
        }

        if ($fd = fopen($file_path, 'w')) {
            fwrite($fd, "<?php\n\nglobal \$" . $tab . ";\n\$" . $tab . " = array();\n");

            foreach ($translations as $key => $value) {
                fwrite($fd, '$' . $tab . '[\'' . \pSQL($key, true) . '\'] = \'' . \pSQL($value, true) . '\';' . "\n");
            }

            fwrite($fd, "\n?>");
            fflush($fd);
            ftruncate($fd, ftell($fd));
            fclose($fd);
        } else {
            throw new \Exception(sprintf(\Tools::displayError('Cannot write this file: "%s"'), $file_path));
        }

        return true;
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();

        $arr['is_default'] = $this->name === \Dingedi\PsTools\DgShopInfos::getDefaultTheme();

        return $arr;
    }
}
