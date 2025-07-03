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
 * @copyright Copyright 2022 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */


namespace Dingedi\PsTranslationsApi\models;


use Dingedi\PsTranslationsApi\Exception\FailedTranslationException;

class FailedTranslation extends \ObjectModel implements \JsonSerializable
{
    public $id_dg_failed_translation;
    public $id_lang_source;
    public $id_shop;
    public $content_type;
    public $error_type;
    public $request;
    public $log;
    public $date_add;

    public static $definition = array(
        'table' => 'dg_failed_translation',
        'primary' => 'id_dg_failed_translation',
        'fields' => array(
            'id_lang_source' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ),
            'content_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            'request' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isString',
                'required' => true,
            ),
            'error_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            'log' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->id_shop = \Context::getContext()->shop->id;

        $langId = \Dingedi\PsTranslationsApi\TranslationRequest::getSourceLangId();

        if ($langId !== false) {
            $this->id_lang_source = (int)$langId;
        } else {
            $this->id_lang_source = 1;
        }

        if ($this->request !== null) {
            $this->request = json_decode((string)$this->request, JSON_OBJECT_AS_ARRAY === null ?: JSON_OBJECT_AS_ARRAY, 512, 0);
        }
    }

    /**
     * @throws \JsonException
     * @param \Exception|\Dingedi\PsTranslationsApi\Exception\FailedTranslationException $exception
     * @param string $contentType
     * @return \Dingedi\PsTranslationsApi\models\FailedTranslation
     */
    public static function addNew($exception, $contentType)
    {
        $translation_data = \Dingedi\PsTranslationsApi\TranslationRequest::getAllTranslationData();

        $failedTranslation = self::getEmptyInstance();

        if (isset($translation_data['translations'])) {
            $groupped = [];

            foreach ($translation_data['translations'] as $item) {
                foreach ($item as $subItem) {
                    $groupped[] = $subItem;
                }
            }

            $translation_data['translations'] = $groupped;
        }

        $failedTranslation->content_type = $contentType;
        $failedTranslation->request = json_encode($translation_data, 0);

        if ($exception instanceof FailedTranslationException && method_exists($exception, 'getType')) {
            $failedTranslation->error_type = $exception->getType();
        } else {
            $failedTranslation->error_type = 'global';
        }

        $failedTranslation->log = $exception->getMessage();

        $exist = self::getByDatas($failedTranslation);

        if ($exist instanceof FailedTranslation) {
            return $exist;
        }

        $failedTranslation->add();

        return self::getEmptyInstance((int)$failedTranslation->id);
    }

    /**
     * @return \DbQuery
     */
    private static function getBaseQuery(FailedTranslation $failedTranslation)
    {
        $query = new \DbQuery();
        $query->select('*')
            ->from(self::$definition['table'])
            ->where('id_shop = ' . (int)$failedTranslation->id_shop);

        if ($failedTranslation->id_lang_source !== 0) {
            $query->where('id_lang_source = ' . (int)$failedTranslation->id_lang_source);
        }

        return $query;
    }

    public static function getEmptyInstance($id = null)
    {

        if (\Dingedi\PsTools\DgShopInfos::isPrestaShop16()) {
            require_once 'FailedTranslation16.php';
            return new \FailedTranslation16($id);
        }

        return new FailedTranslation($id);
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @return \Dingedi\PsTranslationsApi\models\FailedTranslation|false
     * @param \Dingedi\PsTranslationsApi\models\FailedTranslation $newFailedTranslation
     */
    public static function getByDatas($newFailedTranslation)
    {
        $query = self::getBaseQuery($newFailedTranslation);

        $query->where('content_type = "' . \pSQL($newFailedTranslation->content_type) . '"');

        $result = \Db::getInstance()->executeS($query->build())[0];

        if ($result === null) {
            return false;
        }

        $failedTranslation = self::getEmptyInstance($result[self::$definition['primary']]);

        if ($newFailedTranslation->request !== $result['request']) {
            $failedTranslation->delete();
            return false;
        }

        return $failedTranslation;
    }

    /**
     * @param string $content_type
     * @return mixed[]
     */
    public static function getAll($content_type)
    {
        $obj = self::getEmptyInstance();

        $obj->id_lang_source = \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId();

        if (in_array($content_type, ['modules', 'themes'])) {
            $obj->id_lang_source = 0;
        }

        $query = self::getBaseQuery($obj);
        $query->where('content_type LIKE "' . \pSQL($content_type) . '-%"');

        $result = \Db::getInstance()->executeS($query->build());

        if (empty($result)) {
            return array();
        }

        return array_map(function ($item) {
            return self::getEmptyInstance($item[self::$definition['primary']]);
        }, $result);
    }

    /**
     * @return string
     */
    public static function getInstallSql()
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '` (
            `id_dg_failed_translation` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_lang_source` int(11) NOT NULL,
            `id_shop` int(11) UNSIGNED NOT NULL DEFAULT 1,
            `content_type` VARCHAR(255) NOT NULL,
            `request` LONGTEXT NOT NULL,
            `error_type` VARCHAR(255) NOT NULL,
            `log` LONGTEXT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_dg_failed_translation`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
    }

    /**
     * @return string
     */
    public static function getUninstallSql()
    {
        return 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array(
            'id' => (int)$this->id,
            'id_lang_source' => (int)$this->id_lang_source,
            'id_shop' => (int)$this->id_shop,
            'content_type' => $this->content_type,
            'error_type' => $this->error_type,
            'request' => $this->request,
            'log' => $this->log,
            'date_add' => $this->date_add
        );
    }
}
