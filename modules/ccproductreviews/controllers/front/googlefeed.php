<?php

class CcProductReviewsGooglefeedModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        require_once _PS_MODULE_DIR_.$this->module->name.'/classes/GoogleProductReviewsFeed.php';
        CcprGoogleProductReviewsFeed::output($this->context);
    }
}
