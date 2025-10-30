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
class RedirectUrl extends ObjectModel
{
    public $id_redirecturl;
    public $redirect_type;
    public $old_uri;
    public $new_url;
    public $_join;
    public $_where;

    public static $definition = [
        'table' => 'redirecturl',
        'primary' => 'id_redirecturl',
        'fields' => [
            'old_uri' => ['type' => self::TYPE_STRING, 'required' => true],
            'new_url' => ['type' => self::TYPE_STRING, 'required' => true],
            'redirect_type' => ['type' => self::TYPE_STRING, 'required' => true],
        ],
    ];

    public function add($autodate = true, $null_values = false)
    {
        Shop::addTableAssociation('redirecturl', ['type' => 'shop']);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'redirecturl_shop` sa ON (a.`id_redirecturl` = sa.`id_redirecturl` AND sa.id_shop = ' .
            (int) Context::getContext()->shop->id . ') ';
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }
        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        return true;
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return true;
        }

        return false;
    }

    public function getAllRedirects()
    {
        $id_shop = (int) Context::getContext()->shop->id;

        return Db::getInstance()->executeS('
		SELECT * FROM `' . _DB_PREFIX_ . 'redirecturl` r
		LEFT JOIN `' . _DB_PREFIX_ . 'redirecturl_shop` s ON (r.`id_redirecturl` = s.`id_redirecturl`)
		WHERE s.`id_shop` = ' . (int) $id_shop . '
		ORDER BY r.`id_redirecturl`');
    }

    public function dropAll($rows)
    {
        foreach ($rows as $row) {
            Db::getInstance()->execute('
			DELETE FROM ' . _DB_PREFIX_ . 'redirecturl
			WHERE id_redirecturl = ' . (int) $row);
        }
    }

    public static function isExists($old_uri, $id_redirecturl = null)
    {
        $sql1 = new DbQuery();
        $sql1->select('id_redirecturl');
        $sql1->from('redirecturl');
        $result = Db::getInstance()->executeS($sql1);
        if (!empty($result)) {
            foreach ($result as $res) {
                if ($id_redirecturl != $res['id_redirecturl']) {
                    $id_shop = (int) Context::getContext()->shop->id;
                    $sql = new DbQuery();
                    $sql->select('*');
                    $sql->from('redirecturl', 'r');
                    $sql->leftJoin('redirecturl_shop', 's', 'r.`id_redirecturl` = s.`id_redirecturl`');
                    $sql->where('r.old_uri =   "' . pSQL($old_uri) . '" ');
                    $sql->where('s.id_shop =' . (int) $id_shop);
                    $sql->where('r.id_redirecturl !=' . (int) $id_redirecturl);

                    return (bool) Db::getInstance()->executeS($sql);
                }
            }
        } else {
            $id_shop = (int) Context::getContext()->shop->id;
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('redirecturl', 'r');
            $sql->leftJoin('redirecturl_shop', 's', 'r.`id_redirecturl` = s.`id_redirecturl`');
            $sql->where('r.old_uri =   "' . pSQL($old_uri) . '" ');
            $sql->where('s.id_shop =' . (int) $id_shop);

            return (bool) Db::getInstance()->executeS($sql);
        }
    }
}
