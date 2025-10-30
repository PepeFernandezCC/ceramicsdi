<?php
/**
 * UrlRedirects
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2023 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AllBrokenLinks extends ObjectModel
{
    public $id_fmm_404_pages;
    public $redirect_type;
    public $broken_url;
    public $redirection_url;
    public $id_shop;
    public $id_shop_group;

    public static $definition = [
        'table' => 'fmm_404_pages',
        'primary' => 'id_fmm_404_pages',
        'fields' => [
            'redirect_type' => ['type' => self::TYPE_STRING, 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT],
            'id_shop_group' => ['type' => self::TYPE_INT],
            'broken_url' => ['type' => self::TYPE_STRING, 'required' => true],
            'redirection_url' => ['type' => self::TYPE_STRING, 'required' => true],
        ],
    ];

    public static function savePageNotFound($new_uri)
    {
        $id_shop = Context::getContext()->shop->id;
        $id_shop_group = Context::getContext()->shop->id_shop_group;
        if (!empty($new_uri)) {
            foreach ($new_uri as $uri) {
                Db::getInstance()->insert('fmm_404_pages', [
                    'broken_url' => pSQL($uri),
                    'id_shop' => (int) $id_shop,
                    'id_shop_group' => (int) $id_shop_group,
                ]);
            }
        }

        return false;
    }

    public static function savePnf($uri, $id_shop, $id_shop_group)
    {
        Db::getInstance()->insert('fmm_404_pages', [
            'broken_url' => pSQL($uri),
            'id_shop' => (int) $id_shop,
            'id_shop_group' => (int) $id_shop_group,
        ]);
    }

    public function getPagesNotFound()
    {
        $id_shop = Context::getContext()->shop->id;
        $id_shop_group = Context::getContext()->shop->id_shop_group;
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('pagenotfound');
        $sql->where('id_shop=' . (int) $id_shop);
        $sql->where('id_shop_group=' . (int) $id_shop_group);

        return Db::getInstance()->executeS($sql);
    }

    public function isExists($uri)
    {
        $id_shop = Context::getContext()->shop->id;
        $id_shop_group = Context::getContext()->shop->id_shop_group;
        $sql = new DbQuery();
        $sql->select('broken_url');
        $sql->from('fmm_404_pages');
        $sql->where('id_shop=' . (int) $id_shop);
        $sql->where('id_shop_group=' . (int) $id_shop_group);
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            $unique_element = [];
            foreach ($result as $res) {
                array_push($unique_element, $res['broken_url']);
            }
            $all = array_diff($uri, $unique_element);
            self::savePageNotFound($all);
        } else {
            self::savePageNotFound($uri);
        }
    }

    public function getAllPnfRedirects()
    {
        $id_shop = Context::getContext()->shop->id;
        $id_shop_group = Context::getContext()->shop->id_shop_group;
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fmm_404_pages');
        $sql->where('id_shop=' . (int) $id_shop);
        $sql->where('id_shop_group=' . (int) $id_shop_group);
        $sql->orderBy('id_fmm_404_pages');

        return Db::getInstance()->executeS($sql);
    }

    public static function updateAllPnfUrls($type, $new_url)
    {
        if (!empty($type) && !empty($new_url)) {
            $id_shop = Context::getContext()->shop->id;
            $id_shop_group = Context::getContext()->shop->id_shop_group;

            return Db::getInstance()->update(
                'fmm_404_pages',
                [
                    'redirect_type' => pSQL($type),
                    'redirection_url' => pSQL($new_url),
                    'id_shop' => (int) $id_shop,
                    'id_shop_group' => (int) $id_shop_group,
                ]
            );
        }
    }

    public function dropAll($rows)
    {
        foreach ($rows as $row) {
            Db::getInstance()->execute('
			DELETE FROM ' . _DB_PREFIX_ . 'fmm_404_pages
			WHERE id_fmm_404_pages = ' . (int) $row);
        }
    }

    public function deleteSelectedRecord($id)
    {
        return Db::getInstance()->delete(
            'fmm_404_pages',
            'id_fmm_404_pages=' . (int) $id
        );
    }

    public static function checkUriExistance($request_uri)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `redirection_url`
		FROM `' . _DB_PREFIX_ . 'fmm_404_pages`
		WHERE `broken_url` = "' . pSQL($request_uri) . '"');
        if (is_array($result) && !empty($result)) {
			return $result['redirection_url'];
		}
		else {
			return false;
		}
    }

    public static function clearPnfHtaccessFile()
    {
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $content = Tools::file_get_contents($path);
        if (preg_match('#^(.*)\#begin_404url.*\#end_404url[^\n]*(.*)$#s', $content, $m)) {
            if ($write_fd = @fopen($path, 'w')) {
                fwrite($write_fd, $m[1]);
                fclose($write_fd);
            }
        }

        return true;
    }

    public static function updateUrlRedirects()
    {
        $return = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fmm_404_pages`(
			`id_fmm_404_pages`	int(10) NOT NULL auto_increment,
			`redirect_type`		varchar(20),
			`id_shop` int(11) NOT NULL,
			`id_shop_group` int(11) NOT NULL,
			`broken_url`			varchar(255) NOT NULL,
			`redirection_url`	    varchar(500) NOT NULL,
			PRIMARY KEY			(`id_fmm_404_pages`))');

        return $return;
    }
}
