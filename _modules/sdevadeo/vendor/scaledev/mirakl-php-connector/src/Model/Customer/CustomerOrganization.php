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

namespace Scaledev\MiraklPhpConnector\Model\Customer;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerOrganization\CustomerOrganizationAddress;

/**
 * Class CustomerOrganization
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class CustomerOrganization extends AbstractModel
{
    /**
     * Address of the organization. Required for new organizations.
     *
     * @var CustomerOrganizationAddress
     */
    private $address;

    /**
     * Number used to identify the customer organization as an established business in a country. E.g: SIRET number in France, NIF in Spain. Required for new organizations.
     *
     * @var string
     */
    private $identification_number;

    /**
     * Name of the organization. Required for new organizations.
     *
     * @var string
     */
    private $name;

    /**
     * Customer's organization id (from the operator's system)
     *
     * @var string
     */
    private $organization_id;

    /**
     * Tax identification number of the organization.
     *
     * @var string
     */
    private $tax_identification_number;

    /**
     * @return CustomerOrganizationAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param CustomerOrganizationAddress $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationNumber()
    {
        return $this->identification_number;
    }

    /**
     * @param string $identification_number
     * @return $this
     */
    public function setIdentificationNumber($identification_number)
    {
        $this->identification_number = $identification_number;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * @param string $organization_id
     * @return $this
     */
    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumber()
    {
        return $this->tax_identification_number;
    }

    /**
     * @param string $tax_identification_number
     * @return $this
     */
    public function setTaxIdentificationNumber($tax_identification_number)
    {
        $this->tax_identification_number = $tax_identification_number;
        return $this;
    }
}
