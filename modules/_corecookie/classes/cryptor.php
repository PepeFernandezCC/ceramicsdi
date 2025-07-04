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
     * If you need help please <attechteams@gmail.com>
     *
     * @author    Alpha Tech <attechteams@gmail.com>
     * @copyright 2022 Alpha Tech
     * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
     */

    class CoreCookieCryptor
    {
        private static $ciphering = 'AES-128-CTR';
        private static $options = 0;
        private static $key = 'corecookie';

        public static function encrypt($data)
        {
            return openssl_encrypt ($data, self::$ciphering,
                self::$key, self::$options, 1111111111111111);
        }

        public static function decrypt($data)
        {
            return openssl_decrypt ($data, self::$ciphering,
                self::$key, self::$options, 1111111111111111);
        }

        public static function encryptLink($id_customer, $email, $request) {
            $data = self::$key.'#'.date('Y-m-d H:i').'#'.$id_customer.'#'.$email.'#'.$request->id.'#'.$request->metatype;
            return self::encrypt($data);
        }

        public static function getDataByLink($hash) {
            $data = self::decrypt($hash);
            $data = explode('#', $data);
            if( !isset($data[0]) || // Key
                !isset($data[1]) || // Date
                !isset($data[2]) || // $id_customer
                !isset($data[3]) || // $email
                !isset($data[4]) || // $request->id
                !isset($data[5]) || // $request->metatype
                self::$key != $data[0]
            ){
                return false;
            }
            $moduleObj = Module::getInstanceByName(self::$key);
            $setting = json_decode($moduleObj->name::getConfiguration('GLOBAL_SETTINGS'), true);

            $time_expired = (int)$setting['removal_of_personal_data_expired'];
            if($data[5] != CoreCookieConsentLog::$METATYPE_DELETION_REQUEST) {
                $time_expired = (int)$setting['access_to_personal_data_expired'];
            }

            $time = strtotime(date('Y-m-d H:i')) - strtotime($data[1]);
            if($time > $time_expired * 60 * 60) {
                return false;
            }
            return [
                'id_customer' => $data[2],
                'id_request' => $data[4],
            ];
        }
    }
?>
