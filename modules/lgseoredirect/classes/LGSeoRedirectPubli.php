<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LGSeoRedirectPubli
{
    const MODULE_NAME = 'lgseoredirect';
    private $module;
    private $iso_langs = ['es', 'en', 'fr', 'it', 'de'];

    private static $instance;

    protected function __construct()
    {
        $this->module = Module::getInstanceByName(self::MODULE_NAME);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new LGSeoRedirectPubli();
        }
        return self::$instance;
    }

    public function getHeader()
    {
        return $this->getP('top');
    }

    public function getFooter()
    {
        return $this->getP('bottom');
    }

    protected function getP($template)
    {
        $context = Context::getContext();
        $current_iso_lang = $context->language->iso_code;
        $iso = (in_array($current_iso_lang, $this->iso_langs)) ? $current_iso_lang : 'en';

        $context->smarty->assign(
            [
                'lgpublicidad_iso' => $iso,
                'lgpublicidad_base_url' => _MODULE_DIR_ . self::MODULE_NAME,
            ]
        );

        return $this->module->display(
            $this->module->getLocalPath(),
            'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }
}
