<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2022 SAS Comptoir du Code
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @package   cdc_googletagmanager
 */

/**
 * Represent GTM Ecommerce
 */
class Gtm_Ecommerce
{
    /**
     * Gtm_Ecommerce constructor.
     * @param Gtm_DataLayer $datalayer
     * @param null $currencyCode
     */
	public function __construct($datalayer, $currencyCode = null)
	{
	    if($datalayer->eeEnabled()) {
            $this->currencyCode = $currencyCode;
        }

        if($datalayer->ga4Enabled()) {
            $this->currency = $currencyCode;
        }
    }

    /**
     * Merge object
     * @param $object
     */
	public function mergeObject($object) {
	    if(is_object($object)) {
	        $attributes = get_object_vars($object);
	        foreach ($attributes as $attribute => $value) {
	            $this->$attribute = $value;
            }
        }
    }
}
