<?php
/**
* WhatsApp Chat
*
* ISC License
*
* Copyright (c) 2023 idnovate.com
* idnovate is a Registered Trademark & Property of idnovate.com, innovación y desarrollo SCP
*
* Permission to use, copy, modify, and/or distribute this software for any
* purpose with or without fee is hereby granted, provided that the above
* copyright notice and this permission notice appear in all copies.
*
* THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
* REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
* AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
* INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
* LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
* OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
* PERFORMANCE OF THIS SOFTWARE.
*
* @author    idnovate
* @copyright 2024 idnovate
* @license   https://www.isc.org/licenses/ https://opensource.org/licenses/ISC ISC License
*/

if (!defined('_PS_VERSION_')) { exit; }

class WhatsappChatBlockAgent extends ObjectModel
{
    public $id_whatsappchatblock_agent;
    public $id_whatsappchatblock;
    public $name;
    public $department;
    public $mobile_phone;
    public $image;
    public $position;
    public $active;
    public $schedule;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'whatsappchatblock_agent',
        'primary' => 'id_whatsappchatblock_agent',
        'multilang' => true,
        'fields' => array(
            'id_whatsappchatblock' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'name'                 => array('type' => self::TYPE_STRING),
            'department'           => array('type' => self::TYPE_STRING, 'lang' => true),
            'mobile_phone'         => array('type' => self::TYPE_STRING),
            'image'                => array('type' => self::TYPE_STRING),
            'position'             => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active'               => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'schedule'             => array('type' => self::TYPE_STRING),
        ),
    );

    public function add($autodate = true, $null_values = true)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
        return parent::add($autodate, $null_values);
    }

    public function getWhatsappChatAgents($id_whatsappchatblock = false, $active = false)
    {
        $langID = Context::getContext()->language->id;
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '` LEFT JOIN `'
            . _DB_PREFIX_ . bqSQL($this->def['table']) . '_lang` ON (`' . _DB_PREFIX_ . bqSQL($this->def['table'])
            . '`.`id_whatsappchatblock_agent` = `' . _DB_PREFIX_ . bqSQL($this->def['table'])
            . '_lang`.`id_whatsappchatblock_agent` AND `id_lang` = ' . (int)$langID.')'
            . ' WHERE 1 = 1'
            . ($id_whatsappchatblock ? ' AND `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '`.`id_whatsappchatblock` = ' . (int)$id_whatsappchatblock : '')
            . ($active ? ' AND `active` = 1' : '')
            . ' ORDER BY position';
        return Db::getInstance()->executeS($sql);
    }

    public static function getNbObjects()
    {
        $sql = 'SELECT COUNT(w.`id_whatsappchatblock_agent`) AS nb
                FROM `' . _DB_PREFIX_ . 'whatsappchatblock_agent` w';
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
