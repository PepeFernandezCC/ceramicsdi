<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Xml;

if (!defined('_PS_VERSION_')) {
    exit;
}
class xmlGenerateReviews extends baseXml
{
    /**
     * @param array $aParams
     */
    public function __construct($aParams = [])
    {
        $this->data = new \stdClass();
        $this->sContent = '';
        $this->aParams = $aParams;
        $this->bOutput = true;
    }

    /**
     * generate generate the current data feed for reviews
     */
    public function generate()
    {
        try {
            $aAssign = [];

            $oReviewsXml = new xmlReviews();

            $aParams = [
                'iLangId' => !empty(\Tools::getValue('gmcp_lang_id')) ? \Tools::getValue('gmcp_lang_id') : \Tools::getValue('id_lang'),
                'bOutput' => 1,
                'sType' => \Tools::getValue('feed_type'),
            ];

            // set the header
            $oReviewsXml->header($aParams);

            // Build the content of the data feed
            $oReviewsXml->buildReviewsXml($aParams);

            // set footer
            $oReviewsXml->footer($aParams);

            return [
                'tpl' => 'admin/feed-generate-output.tpl',
                'assign' => $aAssign,
            ];
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
