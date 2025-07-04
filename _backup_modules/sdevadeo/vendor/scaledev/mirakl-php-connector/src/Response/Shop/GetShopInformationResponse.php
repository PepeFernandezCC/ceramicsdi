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

namespace Scaledev\MiraklPhpConnector\Response\Shop;

use Scaledev\MiraklPhpConnector\Builder\Shop\GetShopInformationBuilder;
use Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;
use Scaledev\MiraklPhpConnector\Model\Shop;
use Scaledev\MiraklPhpConnector\Request\Shop\GetShopInformationRequest;

/**
 * Class GetShopInformationResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GetShopInformationResponse extends AbstractResponse
{
    /**
     * Defines the builder class to use.
     */
    const BUILDER_CLASS = GetShopInformationBuilder::class;

    /**
     * Shop model
     *
     * @var Shop
     */
    private $shop;

    /**
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     * @return $this
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
        return $this;
    }

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $builder = self::BUILDER_CLASS;
        $this->setShop(
            (new $builder(
                $resultRequest
            ))->getBuilt()
        );

        return $this;
    }
}
