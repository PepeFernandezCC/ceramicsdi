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

namespace Scaledev\MiraklPhpConnector\Model\Export\ProductExportStatus;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class IntegrationDetails
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class IntegrationDetails extends AbstractModel
{
    /**
     * @var integer
     */
    private $invalid_products;

    /**
     * @var integer
     */
    private $products_not_accepted_in_time;

    /**
     * @var integer
     */
    private $products_not_synchronized_in_time;

    /**
     * @var integer
     */
    private $products_reimported;

    /**
     * @var integer
     */
    private $products_successfully_synchronized;

    /**
     * @var integer
     */
    private $products_with_synchronization_issues;

    /**
     * @var integer
     */
    private $products_with_wrong_identifiers;

    /**
     * @var integer
     */
    private $rejected_products;

    /**
     * @return int
     */
    public function getInvalidProducts()
    {
        return $this->invalid_products;
    }

    /**
     * @param int $invalid_products
     * @return $this
     */
    public function setInvalidProducts($invalid_products)
    {
        $this->invalid_products = $invalid_products;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsNotAcceptedInTime()
    {
        return $this->products_not_accepted_in_time;
    }

    /**
     * @param int $products_not_accepted_in_time
     * @return $this
     */
    public function setProductsNotAcceptedInTime($products_not_accepted_in_time)
    {
        $this->products_not_accepted_in_time = $products_not_accepted_in_time;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsNotSynchronizedInTime()
    {
        return $this->products_not_synchronized_in_time;
    }

    /**
     * @param int $products_not_synchronized_in_time
     * @return $this
     */
    public function setProductsNotSynchronizedInTime($products_not_synchronized_in_time)
    {
        $this->products_not_synchronized_in_time = $products_not_synchronized_in_time;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsReimported()
    {
        return $this->products_reimported;
    }

    /**
     * @param int $products_reimported
     * @return $this
     */
    public function setProductsReimported($products_reimported)
    {
        $this->products_reimported = $products_reimported;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsSuccessfullySynchronized()
    {
        return $this->products_successfully_synchronized;
    }

    /**
     * @param int $products_successfully_synchronized
     * @return $this
     */
    public function setProductsSuccessfullySynchronized($products_successfully_synchronized)
    {
        $this->products_successfully_synchronized = $products_successfully_synchronized;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsWithSynchronizationIssues()
    {
        return $this->products_with_synchronization_issues;
    }

    /**
     * @param int $products_with_synchronization_issues
     * @return $this
     */
    public function setProductsWithSynchronizationIssues($products_with_synchronization_issues)
    {
        $this->products_with_synchronization_issues = $products_with_synchronization_issues;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductsWithWrongIdentifiers()
    {
        return $this->products_with_wrong_identifiers;
    }

    /**
     * @param int $products_with_wrong_identifiers
     * @return $this
     */
    public function setProductsWithWrongIdentifiers($products_with_wrong_identifiers)
    {
        $this->products_with_wrong_identifiers = $products_with_wrong_identifiers;
        return $this;
    }

    /**
     * @return int
     */
    public function getRejectedProducts()
    {
        return $this->rejected_products;
    }

    /**
     * @param int $rejected_products
     * @return $this
     */
    public function setRejectedProducts($rejected_products)
    {
        $this->rejected_products = $rejected_products;
        return $this;
    }
}
