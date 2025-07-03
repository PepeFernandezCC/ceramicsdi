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

class DgModuleTranslatable16 extends AbstractLangFileTranslationSource
{
    /**
     * @var mixed[]
     */
    public $filesArray;

    /**
     * @param string $name
     */
    public function __construct($name, array $langTo)
    {
        $name = (string) $name;
        parent::__construct($name, $langTo, 'modules');
    }

    /**
     * @throws \Exception
     * @return mixed[]
     */
    public function getTranslations()
    {
        $filePath = _PS_MODULE_DIR_ . $this->name . '/translations/' . $this->langTo['iso_code'] . '.php';

        if (!file_exists($filePath) || (file_exists($filePath) && trim(\Tools::file_get_contents($filePath)) === "")) {
            file_put_contents($filePath, "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n");
        }

        $_POST['type'] = 'modules';
        $_POST['module'] = $this->name;
        $_POST['iso_code'] = $this->langTo['iso_code'];
        $_POST['theme'] = \Dingedi\PsTools\DgShopInfos::getDefaultTheme();
        $GLOBALS['_MODULES'] = array();


        $admin_translations_controller = new \AdminTranslationsController();
        $reflection = new \ReflectionClass('AdminTranslationsController');

        $admin_translations_controller->getInformations();

        $get_all_modules_files_method = $reflection->getMethod('getAllModuleFiles');
        $get_all_modules_files_method->setAccessible(true);

        $this->filesArray = $get_all_modules_files_method->invokeArgs(
            $admin_translations_controller,
            array(
                [$this->name],
                null,
                $this->langTo['iso_code'],
                true
            )
        );

        $findAndFillTranslationsMethod = $reflection->getMethod('findAndFillTranslations');
        $findAndFillTranslationsMethod->setAccessible(true);

        foreach ($this->filesArray as $value) {
            if ($value['module'] !== $this->name) {
                continue;
            }

            $findAndFillTranslationsMethod->invokeArgs(
                $admin_translations_controller,
                array(
                    $value['files'],
                    $value['theme'],
                    $value['module'],
                    $value['dir']
                )
            );
        }

        $translations_prop = $reflection->getProperty('modules_translations');
        $translations_prop->setAccessible(true);
        $translationsTabs = $translations_prop->getValue($admin_translations_controller);

        $translations = array();
        $missing = array();

        foreach ($translationsTabs as $themeName => $theme) {
            foreach ($theme as $moduleName => $module) {
                foreach ($module as $templateName => $string) {
                    foreach ($string as $key => $value) {
                        $encodedKey = \Tools::strtolower($moduleName);

                        if ($themeName) {
                            $encodedKey .= '_' . \Tools::strtolower($themeName);
                        }

                        $encodedKey .= '_' . \Tools::strtolower($templateName);

                        $encodedKey .= '_' . md5($key);
                        $encodedKey = md5($encodedKey);

                        $translations[] = array(
                            'key' => $encodedKey,
                            'value' => \Tools::stripslashes(html_entity_decode($key, ENT_COMPAT, 'UTF-8')),
                            'trad' => \Tools::stripslashes(html_entity_decode($value['trad'], ENT_COMPAT, 'UTF-8'))
                        );
                    }
                }
            }
        }

        $isoLangTo = \Dingedi\PsTools\DgTools::getLocale($this->langTo);

        if (!in_array($isoLangTo, array('en', 'gb'))) {
            foreach ($translations as $translation) {
                if (($this->sameTranslations->needTranslation($this->name, $translation['key'], [-1, (int)$this->langTo['id_lang']]) && $translation['trad'] === $translation['value'])
                    || in_array($translation['trad'], array(null, ''))) {
                    $missing[] = $translation;
                }
            }
        }

        return array(
            'all' => $translations,
            'missing' => $missing,
            'tabs' => $translationsTabs
        );
    }

    /**
     * @param mixed[] $translations
     * @return bool
     */
    public function saveMissingTranslations($translations)
    {
        $currentTranslations = $this->getTranslations();
        $translationsToSave = array();

        foreach ($currentTranslations['all'] as $translation) {
            $translationsToSave[$translation['key']] = str_replace(
                array('\"', "\'"),
                array('"', "'"),
                $translation['trad']);
        }

        foreach ($translations as $translation) {
            $translationsToSave[$translation['key']] = $translation['trad'];
        }

        unset($_POST);

        foreach ($translationsToSave as $k => $v) {
            $_POST[$k] = $v;
        }

        $str_write = [];
        $array_check_duplicate = [];
        $cache_file = [];

        foreach ($this->filesArray as $value) {
            $file_name = $value['file_name'];
            $files = $value['files'];
            $theme_name = $value['theme'];
            $module_name = $value['module'];

            if (!isset($cache_file[$theme_name . '-' . $file_name])) {
                $str_write[$file_name] = '';
                $cache_file[$theme_name . '-' . $file_name] = true;

                $str_write[$file_name] .= "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
                $array_check_duplicate = [];
            }

            foreach ($files as $file) {
                if (preg_match('/^(.*)\.(tpl|php)$/', $file) && !in_array($file, ['.', '..', '.svn', '.git', '.htaccess', 'index.php'])) {
                    // Get content for this file

                    $template_name = substr(basename($file), 0, -4);
                    // Get file type
                    $matches = isset($currentTranslations['tabs'][$theme_name][$module_name][$template_name]) ? $currentTranslations['tabs'][$theme_name][$module_name][$template_name] : false;

                    if($matches === false) {
                        continue;
                    }

                    // Write each translation on its module file
                    foreach ($matches as $key => $v) {
                        if ($theme_name) {
                            $post_key = md5(strtolower($module_name) . '_' . strtolower($theme_name) . '_' . strtolower($template_name) . '_' . md5($key));
                            $pattern = '\'<{' . strtolower($module_name) . '}' . strtolower($theme_name) . '>' . strtolower($template_name) . '_' . md5($key) . '\'';
                        } else {
                            $post_key = md5(strtolower($module_name) . '_' . strtolower($template_name) . '_' . md5($key));
                            $pattern = '\'<{' . strtolower($module_name) . '}prestashop>' . strtolower($template_name) . '_' . md5($key) . '\'';
                        }
                        if (array_key_exists($post_key, $_POST) && !in_array($pattern, $array_check_duplicate)) {
                            if ($_POST[$post_key] == '') {
                                continue;
                            }
                            $array_check_duplicate[] = $pattern;
                            $str_write[$file_name] .= '$_MODULE[' . $pattern . '] = \'' . pSQL(str_replace(["\r\n", "\r", "\n"], ' ', $_POST[$post_key])) . '\';' . "\n";
                        }
                    }
                }
            }
        }

        foreach ($str_write as $k => $v) {
            if ($k && !file_exists($k)) {
                if (!file_exists(dirname($k)) && !mkdir(dirname($k), 0777, true)) {
                    throw new \Exception(sprintf('Directory "%s" cannot be created', dirname($k)));
                } elseif (!touch($k)) {
                    throw new \Exception(sprintf(\Tools::displayError('File "%s" cannot be created'), $k));
                }
            }

            file_put_contents($k, $v);
        }

        return true;
    }
}
