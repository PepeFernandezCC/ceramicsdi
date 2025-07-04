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

namespace Dingedi\PsTranslationsApi\Configuration;

use Dingedi\PsTools\DgConfiguration;
use Dingedi\PsTranslationsApi\DgTranslationTools;

class AutomaticTranslationConfiguration extends DgConfiguration
{
    /**
     * @param string $key
     */
    public function __construct($key = '', array $params = [])
    {
        $key = (string) $key;
        parent::__construct('automatic_translation', [
            'enabled' => false,
            'translate_all' => true,
            'enabled_for_update' => true,
            'enabled_for_addition' => true,
            'translate_tables' => '[]',
            'id_lang_from' => DgTranslationTools::getDefaultLangId(),
            'ids_langs_to' => ''
        ]);
    }

    /**
     * @return mixed[]
     */
    protected function beforeSave($params)
    {
        $params['translate_tables'] = json_encode(json_decode((string)$params['translate_tables'], true, 512, 0), 0);
        $params['ids_langs_to'] = implode(',', $params['ids_langs_to']);

        return $params;
    }

    /**
     * @return mixed[]
     */
    protected function beforeSerialize($params)
    {
        $params['translate_tables'] = json_decode((string)$params['translate_tables'], true, 512, 0);

        return $params;
    }
}
