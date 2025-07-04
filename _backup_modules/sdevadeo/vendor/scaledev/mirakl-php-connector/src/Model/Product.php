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

namespace Scaledev\MiraklPhpConnector\Model;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Product
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Product extends AbstractModel
{
    /**
     * Product category
     *
     * @var string
     */
    private $category;

    /**
     * Product ID
     *
     * @var string
     */
    private $product_id;

    /**
     * Product ID type
     *
     * @var string
     */
    private $product_id_type;

    /**
     * Product SKU
     *
     * @var string
     */
    private $product_sku;

    /**
     * Product title
     *
     * @var string
     */
    private $product_title;

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param string $product_id
     * @return $this
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductIdType()
    {
        return $this->product_id_type;
    }

    /**
     * @param string $product_id_type
     * @return $this
     */
    public function setProductIdType($product_id_type)
    {
        $this->product_id_type = $product_id_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->product_sku;
    }

    /**
     * @param string $product_sku
     * @return $this
     */
    public function setProductSku($product_sku)
    {
        $this->product_sku = $product_sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductTitle()
    {
        return $this->product_title;
    }

    /**
     * @param string $product_title
     * @return $this
     */
    public function setProductTitle($product_title)
    {
        $this->product_title = $product_title;
        return $this;
    }
}
