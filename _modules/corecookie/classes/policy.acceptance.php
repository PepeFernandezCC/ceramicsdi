<?php
    /**
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Commercial License and is not open source.
     * Each license that you purchased is only available for 1 website only.
     * You can't distribute, modify or sell this code.
     * If you want to use this file on more websites, you need to purchase additional licenses.
     *
     * DISCLAIMER
     *
     * Do not edit or add to this file.
     * If you need help please contact <attechteams@gmail.com>
     *
     * @author    Alpha Tech <attechteams@gmail.com>
     * @copyright 2022 Alpha Tech
     * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
     */

    class CoreCookiePolicyAcceptance extends ObjectModel {
        private static $_module = null;
        private static $ITEM_PER_PAGE = 20;

        public $id;
        public $email;
        public $customer_id;
        public $accepted_page;
        public $given_consent;
        public $ip_address;
        public $interaction;
        public $since;

        public static $definition = array(
            'table' => 'at_cookie_policyAcceptances',
            'primary' => 'id',
            'multilang' => false,
            'multishop' => false,
            'fields' => array(
                'email' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'customer_id' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'accepted_page' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'given_consent' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'ip_address' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'interaction' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'since' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            )
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);
        }

        /**
         * @desc Get module instance
         * @return |null
         */
        private static function getModule() {
            if (self::$_module) {
                return self::$_module;
            }
            self::$_module = Module::getInstanceByName('corecookie');
            return self::$_module;
        }

        /**
         * @desc Get list cookie by paginated
         */
        public static function byPaginated() {
            $count_sql = "SELECT COUNT(id) FROM ".self::getModule()::getPrefixTable()."policyAcceptances WHERE 1 ";
            $sql = "SELECT * FROM ".self::getModule()::getPrefixTable()."policyAcceptances WHERE 1";
            $sql .= " ORDER BY id DESC ";

            $total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($count_sql);
            self::getModule()->name::$config['_CLIENT_CLASS_NAME']::pageData('total_count', $total);
            $sql .= self::getModule()->name::$config['_TEMPLATE_CLASS_NAME']::paginated(self::$ITEM_PER_PAGE);
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }
?>
