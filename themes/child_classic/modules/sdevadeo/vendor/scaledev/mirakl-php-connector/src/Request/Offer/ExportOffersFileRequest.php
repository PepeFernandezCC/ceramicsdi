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

namespace Scaledev\MiraklPhpConnector\Request\Offer;

use Scaledev\MiraklPhpConnector\Collection\OfferCollection;
use Scaledev\MiraklPhpConnector\Converter\PascalToSnakeFieldConverter;
use Scaledev\MiraklPhpConnector\Core\Request\FileExporter\AbstractFileExporterRequest;
use Scaledev\MiraklPhpConnector\Exception\BadModelPropertyException;
use Scaledev\MiraklPhpConnector\Generator\Offer\OfferFileGenerator;
use Scaledev\MiraklPhpConnector\Response\Offer\ExportOffersFileResponse;
use Scaledev\MiraklPhpConnector\Validator\Type\StringTypeValidator;
use Scaledev\MiraklPhpConnector\Validator\ValueValidator;

/**
 * Class ExportOffersFileRequest
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ExportOffersFileRequest extends AbstractFileExporterRequest
{
    /**
     * Defines the response class to use.
     */
    const RESPONSE_CLASS = ExportOffersFileResponse::class;

    /**
     * Defines the method to use for the API call.
     */
    const METHOD = 'POST';

    /**
     * @inheritdoc
     */
    protected $fileGenerator = OfferFileGenerator::class;

    /**
     * Collection to handle
     *
     * @var OfferCollection
     */
    protected $collection;

    /**
     * Mode of importation (see OF01)
     *
     * @var string
     */
    private $import_mode;

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
     * @throws BadModelPropertyException
     * @throws \Scaledev\MiraklPhpConnector\Exception\BadClassThrownException
     */
    public function execute($apiKey, $isTestMode = false, $queryParameter = false)
    {
        //$this->prepare();

        // Validate the field and throw error if required
        if (!StringTypeValidator::validate($this->import_mode)) {
            throw (
                new BadModelPropertyException(
                    get_class($this),
                    PascalToSnakeFieldConverter::convert('import_mode'),
                    StringTypeValidator::class
                )
            );
        }
        if (!ValueValidator::validate($this->import_mode, array('NORMAL', 'PARTIAL_UPDATE', 'REPLACE'))) {
            throw (
            new BadModelPropertyException(
                get_class($this),
                PascalToSnakeFieldConverter::convert('import_mode'),
                ValueValidator::class
            )
            );
        }

        $postFields = array(
            'file' => new \CurlFile(
                $this->filepath,
                mime_content_type($this->filepath),
                pathinfo($this->filepath, PATHINFO_BASENAME)
            ),
            'import_mode' => $this->import_mode
        );

        $this
            ->callToApi($apiKey, $isTestMode, null, $postFields)
            ->initResponse();

        return $this;
    }

    /**
     * @return OfferCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param OfferCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportMode()
    {
        return $this->import_mode;
    }

    /**
     * @param string $import_mode
     * @return $this
     */
    public function setImportMode($import_mode)
    {
        $this->import_mode = $import_mode;
        return $this;
    }
}
