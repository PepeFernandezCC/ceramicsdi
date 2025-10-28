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

use Scaledev\MiraklPhpConnector\Builder\Product\GetProductAttributesBuilder;
use Scaledev\MiraklPhpConnector\Collection\AttributeCollection;
use Scaledev\MiraklPhpConnector\Converter\AttributesNamingConverter;
use Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;

/**
 * Class GetProductAttributesResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GetProductAttributesResponse extends AbstractResponse
{
    /**
     * Defines the builder class to use.
     */
    const BUILDER_CLASS = GetProductAttributesBuilder::class;

    /**
     * @var AttributeCollection
     */
    private $attributeCollection;

    /**
     * @return AttributeCollection
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollection;
    }

    /**
     * @param AttributeCollection $attributeCollection
     * @return $this
     */
    public function setAttributeCollection($attributeCollection)
    {
        $this->attributeCollection = $attributeCollection;
        return $this;
    }

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $builder = self::BUILDER_CLASS;

        $this->setAttributeCollection(
            (new $builder(
                $resultRequest
            ))->getBuilt()
        );

        return $this;
    }
}
