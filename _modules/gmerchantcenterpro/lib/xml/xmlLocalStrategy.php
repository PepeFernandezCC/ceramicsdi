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

use Gmerchantcenterpro\ModuleLib\moduleTools;

class xmlLocalStrategy extends baseProductStrategy
{
    /**
     * @var bool : define if the product has well added
     */
    protected $bProductProcess = false;

    /**
     * hasProductProcessed() method define if the current product has been processed or refused for some not requirements matching
     *
     * @return bool
     */
    public function hasProductProcessed()
    {
        return $this->bProductProcess;
    }

    /**
     * setProductData() method store into the matching object the product and combination
     *
     * @param obj $oData
     * @param \Product $oProduct
     * @param array $aCombination
     *
     * @return mixed
     */
    public function setProductData(&$oData, $oProduct, $aCombination)
    {
        $this->data->p = $oProduct;
        $this->data->c = $aCombination;
    }

    /**
     * buildProductXml() method construct the XML content
     *
     * @param obj $oData
     * @param \Product $oProduct
     * @param array $aCombination
     */
    public function buildProductXml($oData, $oProduct, $aCombination)
    {
        try {
            // load the product and combination into the matching object
            $this->setProductData($oData, $oProduct, $aCombination);

            if (\GMerchantCenterPro::$conf['GMCP_INC_STOCK'] == 1 && $this->data->p->quantity <= 0) {
                return false;
            }

            // exclude if ean13 gtin is empty
            if (!empty(\GMerchantCenterPro::$conf['GMCP_EXC_NO_EAN']) && empty($this->data->p->ean13)) {
                return false;
            }

            // exclude if mpn is empty
            if (!empty(\GMerchantCenterPro::$conf['GMCP_EXC_NO_MREF']) && !\GMerchantCenterPro::$conf['GMCP_INC_ID_EXISTS'] && empty($this->data->p->supplier_reference)) {
                return false;
            }

            if (!empty($this->data->c)) {
                if (isset($this->data->c['quantity'])) {
                    if (\GMerchantCenterPro::$conf['GMCP_INC_STOCK'] == 1 && (int)$this->data->c['quantity'] <= 0) {
                        return false;
                    }
                }

                if (isset($this->data->c['ean13'])) {
                    if (\GMerchantCenterPro::$conf['GMCP_EXC_NO_EAN'] == 1 && empty($this->data->c['ean13'])) {
                        return false;
                    }
                }

                if (isset($this->data->c['supplier_reference'])) {
                    if (!empty(\GMerchantCenterPro::$conf['GMCP_EXC_NO_MREF']) && !\GMerchantCenterPro::$conf['GMCP_INC_ID_EXISTS'] && empty($this->data->c['supplier_reference'])) {
                        return false;
                    }
                }
            }

            $description = moduleTools::getProductDesc($this->data->p->description_short, $this->data->p->description, $this->data->p->meta_description);
            if (empty($description)) {
                return false;
            }

            // handle both price and discounted price
            if (isset($this->aParams['bUseTax'])) {
                $bUseTax = !empty($this->aParams['bUseTax']) ? true : false;
            } else {
                $bUseTax = true;
            }

            // handle both price and discounted price for simple product and combination product
            if (empty($this->data->c['id_product_attribute'])) {
                $this->data->p->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6);
                $this->data->p->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6, null, false, false);
                $this->data->p->price = number_format(moduleTools::round($this->data->p->price_raw), 2, '.', '') . ' ' . $this->data->currency->iso_code;
                $this->data->p->price_no_discount = number_format(moduleTools::round($this->data->p->price_raw_no_discount), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            } else {
                $this->data->p->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, $this->data->c['id_product_attribute'], 6);
                $this->data->p->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, $this->data->c['id_product_attribute'], 6, null, false, false);
                $this->data->p->price = number_format(moduleTools::round($this->data->p->price_raw), 2, '.', '') . ' ' . $this->data->currency->iso_code;
                $this->data->p->price_no_discount = number_format(moduleTools::round($this->data->p->price_raw_no_discount), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            }

            if (
                !empty($this->data->p->id)
                && !empty($this->data->p->name)
                && !empty($description)
                && $this->data->p->visibility != 'none'
            ) {
                $sContent = '';

                $sContent .= "\t" . '<item>' . "\n";

                if (!empty(\GMerchantCenterPro::$conf['GMCP_STORE_CODE'])) {
                    $sContent .= "\t\t" . '<g:store_code>' . \GMerchantCenterPro::$conf['GMCP_STORE_CODE'] . '</g:store_code>' . "\n";
                }

                if (!empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
                    if (!empty($this->data->c['id_product_attribute'])) {
                        if (empty(\GMerchantCenterPro::$conf['GMCP_SIMPLE_PROD_ID'])) {
                            $sContent .= "\t\t" . '<g:id>' . \Tools::strtoupper(\GMerchantCenterPro::$conf['GMCP_ID_PREFIX']) . $this->aParams['sCountryIso'] . $this->data->p->id . \GMerchantCenterPro::$conf['GMCP_COMBO_SEPARATOR'] . $this->data->c['id_product_attribute'] . '</g:id>' . "\n";
                        } else {
                            $sContent .= "\t\t" . '<g:id>' . $this->data->p->id . \GMerchantCenterPro::$conf['GMCP_COMBO_SEPARATOR'] . $this->data->c['id_product_attribute'] . '</g:id>' . "\n";
                        }
                    } else {
                        if (empty(\GMerchantCenterPro::$conf['GMCP_SIMPLE_PROD_ID'])) {
                            $sContent .= "\t\t" . '<g:id>' . \Tools::strtoupper(\GMerchantCenterPro::$conf['GMCP_ID_PREFIX']) . $this->aParams['sCountryIso'] . $this->data->p->id . '</g:id>' . "\n";
                        } else {
                            $sContent .= "\t\t" . '<g:id>' . $this->data->p->id . '</g:id>' . "\n";
                        }
                    }
                } else {
                    if (empty(\GMerchantCenterPro::$conf['GMCP_SIMPLE_PROD_ID'])) {
                        $sContent .= "\t\t" . '<g:id>' . \Tools::strtoupper(\GMerchantCenterPro::$conf['GMCP_ID_PREFIX']) . $this->aParams['sCountryIso'] . $this->data->p->id . '</g:id>' . "\n";
                    } else {
                        $sContent .= "\t\t" . '<g:id>' . $this->data->p->id . '</g:id>' . "\n";
                    }
                }

                if ($this->data->p->price_raw < $this->data->p->price_raw_no_discount) {
                    $sContent .= "\t\t" . '<g:price>' . $this->data->p->price_no_discount . '</g:price>' . "\n";
                    $sContent .= "\t\t" . '<g:sale_price>' . $this->data->p->price . '</g:sale_price>' . "\n";
                } else {
                    $sContent .= "\t\t" . '<g:price>' . $this->data->p->price . '</g:price>' . "\n";
                }

                $iQty = is_array($this->data->c) ? $this->data->c['combo_quantity'] : $this->data->p->quantity;
                $sContent .= "\t\t" . '<g:quantity>' . $iQty . '</g:quantity>' . "\n";

                if ($iQty > 0) {
                    $sContent .= "\t\t" . '<g:availability> in stock </g:availability>' . "\n";
                } else {
                    $sContent .= "\t\t" . '<g:availability>out of stock</g:availability>' . "\n";
                }

                if (!empty(\GMerchantCenterPro::$conf['GMCP_LIA_PICKUP'])) {
                    $sContent .= "\t\t" . '<g:pickup_method>' . \GMerchantCenterPro::$conf['GMCP_LIA_PICKUP'] . '</g:pickup_method>' . "\n";
                }

                if (!empty(\GMerchantCenterPro::$conf['GMCP_LIA_PICKUP_SLA'])) {
                    $sContent .= "\t\t" . '<g:pickup_sla>' . \GMerchantCenterPro::$conf['GMCP_LIA_PICKUP_SLA'] . '</g:pickup_sla>' . "\n";
                }

                $sContent .= "\t" . '</item>' . "\n";

                // increase counter
                ++$this->iCounter;

                // manage output parameters
                if (!empty($this->bOutput)) {
                    echo $sContent;
                } else {
                    return $this->sContent .= $sContent;
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }

    /**
     * create() method creates singleton
     *
     * @param array $sType
     * @param array $aParams
     *
     * @return xmlLocalStrategy
     */
    public static function create($sType, array $aParams = [])
    {
        static $oXml;

        if (null === $oXml) {
            $oXml = new xmlLocalStrategy($sType);
        }

        return $oXml;
    }
}
