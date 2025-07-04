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

namespace Dingedi\PsTranslationsApi;

class TranslationRequest
{

    /**
     * @return mixed[]
     */
    public static function getAllTranslationData()
    {
        $data = \Tools::getValue('translation_data');

        if ($data === false) {
            return [];
        }

        return $data;
    }

    /**
     * @param mixed $default
     * @return mixed
     * @param string $item
     */
    public static function get($item, $default = false)
    {
        $data = self::getAllTranslationData();

        if (!isset($data[$item])) {
            return $default;
        }

        return $data[$item];
    }

    /**
     * @return mixed
     */
    public static function getSourceLangId()
    {
        return self::get('id_lang_from');
    }

    /**
     * @return bool
     */
    public static function isRegenerateLinksOnly()
    {
        return self::get('regenerateLinks') === "true";
    }

    /**
     * @return bool
     */
    public static function isCopyTextOnly()
    {
        return self::get('copyText') === "true";
    }
}
