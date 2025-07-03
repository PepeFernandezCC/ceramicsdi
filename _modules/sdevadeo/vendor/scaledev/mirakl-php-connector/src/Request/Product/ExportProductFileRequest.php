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

namespace Scaledev\MiraklPhpConnector\Request\Product;

use Scaledev\MiraklPhpConnector\Collection\ProductCollection;
use Scaledev\MiraklPhpConnector\Core\Request\FileExporter\AbstractFileExporterRequest;
use Scaledev\MiraklPhpConnector\Exception\BadClassThrownException;
use Scaledev\MiraklPhpConnector\Generator\Product\ProductFileGenerator;
use Scaledev\MiraklPhpConnector\Response\Product\ExportProductFileResponse;

/**
 * Class ImportProductsListRequest
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class ExportProductFileRequest extends AbstractFileExporterRequest
{
    /**
     * Defines the response class to use.
     */
    const RESPONSE_CLASS = ExportProductFileResponse::class;

    /**
     * Defines the method to use for the API call.
     */
    const METHOD = 'POST';

    /**
     * @inheritdoc
     */
    protected $fileGenerator = ProductFileGenerator::class;

    /**
     * Collection to handle
     *
     * @var ProductCollection
     */
    protected $collection;

    /**
     * Initialization of the Response object
     *
     * @return $this
     */
    protected function initResponse()
    {
        $responseClassName = self::RESPONSE_CLASS;
        $this->setResponse(
            new $responseClassName(
                json_decode($this->clientResult,1)['import_id']
            )
        );

        return $this;
    }

    /**
     * @return ProductCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param ProductCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }
}
