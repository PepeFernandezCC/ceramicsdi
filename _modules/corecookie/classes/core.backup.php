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
     * @author    AT Tech <attechteams@gmail.com>
     * @copyright 2022 AT Tech
     * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
     */

    class APCookie
    {
        public static function loadFramework($config)
        {
            return $config['_TEMPLATE_CLASS_NAME'];
        }
    }

    class CSSCookie
    {
        public static function display($path, $mode)
        {
            return $path . $mode;
        }
    }

    class JSCookie
    {
        public static function display($path, $mode)
        {
            return $path . $mode;
        }
    }

    class APTemplateCookie
    {
        public static function parseHTML($name_file, $mode, $content)
        {
            return $name_file . $mode . $content;
        }
    }
?>
