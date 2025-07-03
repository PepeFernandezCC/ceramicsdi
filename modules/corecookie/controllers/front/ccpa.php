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

    class CorecookieCcpaModuleFrontController extends ModuleFrontController {
        public $auth = false;
        public function __construct() {
            $this->context = Context::getContext();
            $this->display_column_left = false;
            $this->display_column_right = false;
            parent::__construct();
        }

        public function initContent() {
            parent::initContent();
            $lang_id = $this->module->id_lang;
            $this->context->smarty->assign([
                'content_page' => $this->module->getConfiguration("CONTENT_CCPA_PAGE", $lang_id)
            ]);
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/gdpr.tpl');
        }
    }
?>
