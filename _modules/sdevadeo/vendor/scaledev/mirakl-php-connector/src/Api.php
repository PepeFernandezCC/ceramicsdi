<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector;

use Scaledev\MiraklPhpConnector\Exception\BadRequestException;
use Scaledev\MiraklPhpConnector\Request\Offer\ExportOffersFileRequest;
use Scaledev\MiraklPhpConnector\Request\Offer\GetOfferExportErrorReportRequest;
use Scaledev\MiraklPhpConnector\Request\Offer\GetOfferExportInformationRequest;
use Scaledev\MiraklPhpConnector\Request\Order\AcceptOrderRequest;
use Scaledev\MiraklPhpConnector\Request\Order\GetOrdersListRequest;
use Scaledev\MiraklPhpConnector\Request\Order\UpdateTrackingRequest;
use Scaledev\MiraklPhpConnector\Request\Order\ValidateShipmentRequest;
use Scaledev\MiraklPhpConnector\Request\Platform\CheckEndpointHealthRequest;
use Scaledev\MiraklPhpConnector\Request\Platform\GetCarrierListRequest;
use Scaledev\MiraklPhpConnector\Request\Platform\GetLogisticClassListRequest;
use Scaledev\MiraklPhpConnector\Request\Platform\GetShippingMethodListRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetCategoryListRequest;
use Scaledev\MiraklPhpConnector\Request\Product\ExportProductFileRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetOperatorValueListRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductAttributesRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportErrorReportRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportReportRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportInformationRequest;
use Scaledev\MiraklPhpConnector\Request\Shop\GetShopInformationRequest;

/**
 * Class Api
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
class Api
{
    /**
     * Defines the URL to use in the production environment.
     */
    const URL_PROD = 'https://adeo-marketplace.mirakl.net';

    /**
     * Defines the URL to use in the test environment.
     */
    const URL_TEST = 'https://adeo2-preprod.mirakl.net';

    /**
     * The URLs list to call.
     *
     * @var string[]
     * @see \Scaledev\MiraklPhpConnector\Api::getUrl()
     */
    private static $urlsList = array(
        // Offer url
        ExportOffersFileRequest::class => '/api/offers/imports',
        GetOfferExportInformationRequest::class => '/api/offers/imports/%s',
        GetOfferExportErrorReportRequest::class => '/api/offers/imports/%s/error_report',

        // Order url
        GetOrdersListRequest::class => '/api/orders',
        // @placeholder $order_id
        AcceptOrderRequest::class => '/api/orders/%s/accept',
        ValidateShipmentRequest::class => '/api/orders/%s/ship',
        UpdateTrackingRequest::class => '/api/orders/%s/tracking',

        // Platform url
        CheckEndpointHealthRequest::class => '/api/version',
        GetCarrierListRequest::class => '/api/shipping/carriers',
        GetShippingMethodListRequest::class => '/api/shipping/types',
        GetLogisticClassListRequest::class => '/api/shipping/logistic_classes',

        // Product url
        ExportProductFileRequest::class => '/api/products/imports',
        GetCategoryListRequest::class => '/api/hierarchies',
        GetProductExportInformationRequest::class => '/api/products/imports/%s',
        GetProductExportReportRequest::class => '/api/products/imports/%s/new_product_report',
        GetProductExportErrorReportRequest::class => '/api/products/imports/%s/error_report',
        GetProductAttributesRequest::class => '/api/products/attributes',
        GetOperatorValueListRequest::class => '/api/values_lists',

        // Store url
        GetShopInformationRequest::class => '/api/account'
    );

    /**
     * Get the API URL to call.
     *
     * @param string $request The API request.
     * @param bool $isTestModeEnabled Defines whether to use the test mode.
     * @return string
     * @throws BadRequestException
     * @see \Scaledev\MiraklPhpConnector\Api::$urls
     */
    public static function getUrl($request, $isTestModeEnabled = false, $GetParameter = null)
    {
        $url = (bool)$isTestModeEnabled
            ? self::URL_TEST
            : self::URL_PROD
        ;

        if ($GetParameter == null) {
            return $url . self::$urlsList[$request];
        }

        if (strpos(self::$urlsList[$request], '%s')) {
            if (!isset($GetParameter['placeholder'])) {
                throw new BadRequestException();
            }
            $url = sprintf(
                $url . self::$urlsList[$request],
                urlencode($GetParameter['placeholder'])
            );
            unset($GetParameter['placeholder']);
        } else {
            $url .= self::$urlsList[$request];
        }
        
        if (!empty($GetParameter)) {
            $index = 0;
            foreach ($GetParameter as $key => $value) {
                $url .= $index == 0
                    ? '?'
                    : '&';
                $url .= urlencode($key).'='.urlencode($value);
                $index++;
            }
        }

        return $url;
    }
}
