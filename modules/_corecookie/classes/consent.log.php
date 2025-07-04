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

    class CoreCookieConsentLog extends ObjectModel {
        private static $_module = null;
        private static $ITEM_PER_PAGE = 20;

        public $id;
        public $customer_id;
        public $email;
        public $metatype;
        public $request_source;
        public $customer_ip_address;
        public $files;
        public $content;
        public $since;
        public $last_update;
        public $date_add;
        public $status;

        public static $STATUS_DRAFT = 'draft';
        public static $STATUS_PENDING = 'pending';
        public static $STATUS_DONE = 'done';

        public static $SOURCE_OF_REQUEST_GDPR = 'gdpr_of_request';
        public static $SOURCE_OF_REQUEST_CCPA = 'ccpa_of_request';
        public static $SOURCE_OF_REQUEST_APPI = 'appi_of_request';
        public static $SOURCE_OF_REQUEST_PIPEDA = 'pipeda_of_request';

        public static $METATYPE_GDPR_REQUEST = 'gdpr_request';
        public static $METATYPE_PERSONAL_INFORMATION = 'personal_information';
        public static $METATYPE_REPORT_REQUEST = 'report_request';
        public static $METATYPE_DELETION_REQUEST = 'deletion_request';
        public static $METATYPE_CCPA_REQUEST = 'ccpa_request';
        public static $METATYPE_DO_NOT_SELL_REQUEST = 'do_not_sell_request';
        public static $METATYPE_APPI_REQUEST = 'appi_request';
        public static $METATYPE_PIPEDA_REQUEST = 'pipeda_request';

        public static $LIST_METATYPE = ['gdpr_request', 'personal_information', 'report_request', 'deletion_request', 'ccpa_request', 'do_not_sell_request', 'appi_request', 'pipeda_request'];
        public static $LIST_SOURCE_OF_REQUEST = ['gdpr_of_request', 'ccpa_of_request', 'appi_of_request', 'pipeda_of_request'];

        public static $definition = array(
            'table' => 'at_cookie_requests',
            'primary' => 'id',
            'multilang' => false,
            'multishop' => false,
            'fields' => array(
                'customer_id' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'email' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'metatype' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'request_source' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'customer_ip_address' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'status' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'files' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'content' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'since' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'last_update' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
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

        public static function byPaginated($metatype = "") {
            $module = self::getModule();
            $count_sql = "SELECT COUNT(id) FROM ".self::getModule()::getPrefixTable()."requests WHERE 1 ";
            $sql = "SELECT * FROM ".self::getModule()::getPrefixTable()."requests WHERE 1
            AND status <> '".self::$STATUS_DRAFT."' ";
            if ($metatype != "") {
                $sql .= " AND metatype = '".self::$METATYPE_DELETION_REQUEST."' ";
            } else {
                $sql .= " AND metatype <> '".self::$METATYPE_DELETION_REQUEST."' ";
            }

            if (Tools::isSubmit('statuses') && ($statuses = Tools::getValue('statuses', '')) != '') {
                $spilt_statuses = explode('-', $statuses);
                $sql .= " AND status IN ('" . implode( "', '", $spilt_statuses ) . "') ";
            }
            if (Tools::isSubmit('metatypes') && ($metatypes = Tools::getValue('metatypes', '')) != '') {
                $spilt_metatypes = explode('-', $metatypes);
                $sql .= " AND metatype IN ('" . implode( "', '", $spilt_metatypes ) . "') ";
            }
            if (Tools::isSubmit('sources') && ($sources = Tools::getValue('sources', '')) != '') {
                $spilt_sources = explode('-', $sources);
                $sql .= " AND request_source IN ('" . implode( "', '", $spilt_sources ) . "') ";
            }
            if (Tools::isSubmit('customers') && ($customers = Tools::getValue('customers', '')) != '') {
                $spilt_customers = explode('-', $customers);
                $sql .= " AND customer_id IN ('" . implode( "', '", $spilt_customers ) . "') ";
            }
            if (Tools::isSubmit('create_at')) {
                $split_create_at = explode("-", Tools::getValue('create_at'));
                $start = $module->beginOfDay($split_create_at[0]);
                $end = $module->midnight($split_create_at[1]);
                $sql .= " AND (since >= {$start} AND since <= {$end}) ";
            }


            $sql .= " ORDER BY id DESC ";

            $total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($count_sql);
            self::getModule()->name::$config['_CLIENT_CLASS_NAME']::pageData('total_count', $total);
            $sql .= self::getModule()->name::$config['_TEMPLATE_CLASS_NAME']::paginated(self::$ITEM_PER_PAGE);
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }

        public static function checkMetaTypePendingExists($metaType, $customer_id)
        {
            $sql = 'SELECT COUNT(id) FROM ' . self::getModule()::getPrefixTable() . 'requests WHERE metatype="' . pSQL($metaType) . '" AND customer_id=' . (int)$customer_id . ' AND status = "pending"';
            return Db::getInstance()->getValue($sql);
        }

        /**
         * @desc Get all customers
         */
        public static function getAllCustomers()
        {
            $module = self::getModule();
            $sql = "SELECT `id_customer`, `firstname`, `lastname`, `email` FROM "._DB_PREFIX_."customer
            WHERE id_shop = ".(int) $module->_shop->id." ";

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }
?>
