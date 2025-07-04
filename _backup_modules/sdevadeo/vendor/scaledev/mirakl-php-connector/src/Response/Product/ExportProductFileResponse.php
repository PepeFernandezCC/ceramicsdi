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

namespace Scaledev\MiraklPhpConnector\Response\Product;

use Scaledev\MiraklPhpConnector\Builder\Product\ExportProductFileBuilder;
use Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;

/**
 * Class ImportProductsListResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class ExportProductFileResponse extends AbstractResponse
{
    /**
     * Defines the builder class to use.
     */
    const BUILDER_CLASS = ExportProductFileBuilder::class;

    /**
     * @var integer
     */
    private $import_number;

    /**
     * @return int
     */
    public function getImportNumber()
    {
        return $this->import_number;
    }

    /**
     * @param int $import_number
     * @return $this
     */
    public function setImportNumber($import_number)
    {
        $this->import_number = $import_number;
        return $this;
    }

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $builder = self::BUILDER_CLASS;

        $this->setImportNumber(
            (new $builder(
                $resultRequest
            ))->getBuilt()
        );

        return $this;
    }
}
