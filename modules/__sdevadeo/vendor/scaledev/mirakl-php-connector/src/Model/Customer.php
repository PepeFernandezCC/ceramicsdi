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
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerAccountingContact;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerBillingAddress;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerDeliveryContact;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerOrganization;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerShippingAddress;

/**
 * Class Customer
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Customer extends AbstractModel
{
    /**
     * Data related to the accounting contact in the organization
     *
     * @var CustomerAccountingContact
     */
    private $accounting_contact;

    /**
     * Customer's billing address
     *
     * @var CustomerBillingAddress
     */
    private $billing_address;

    /**
     * Customer's civility
     *
     * @var string
     */
    private $civility;

    /**
     * Customer's identifier
     *
     * @var string
     */
    private $customer_id;

    /**
     * Data related to the delivery contact in the organization
     *
     * @var CustomerDeliveryContact
     */
    private $delivery_contact;

    /**
     * Customer's first name
     *
     * @var string
     */
    private $firstname;

    /**
     * Customer's last name
     *
     * @var string
     */
    private $lastname;

    /**
     * Customer's locale
     *
     * @var string
     */
    private $locale;

    /**
     * Data related to the organization that created the order (B2B transactions)
     *
     * @var CustomerOrganization
     */
    private $organization;

    /**
     * Customer's Shipping address
     *
     * @var CustomerShippingAddress
     */
    private $shipping_address;

    /**
     * @return CustomerAccountingContact
     */
    public function getAccountingContact()
    {
        return $this->accounting_contact;
    }

    /**
     * @param CustomerAccountingContact $accounting_contact
     * @return $this
     */
    public function setAccountingContact($accounting_contact)
    {
        $this->accounting_contact = $accounting_contact;
        return $this;
    }

    /**
     * @return CustomerBillingAddress
     */
    public function getBillingAddress()
    {
        return $this->billing_address;
    }

    /**
     * @param CustomerBillingAddress $billing_address
     * @return $this
     */
    public function setBillingAddress($billing_address)
    {
        $this->billing_address = $billing_address;
        return $this;
    }

    /**
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     * @return $this
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
        return $this;
    }

    /**
     * @return CustomerDeliveryContact
     */
    public function getDeliveryContact()
    {
        return $this->delivery_contact;
    }

    /**
     * @param CustomerDeliveryContact $delivery_contact
     * @return $this
     */
    public function setDeliveryContact($delivery_contact)
    {
        $this->delivery_contact = $delivery_contact;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return CustomerOrganization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param CustomerOrganization $organization
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return CustomerShippingAddress
     */
    public function getShippingAddress()
    {
        return $this->shipping_address;
    }

    /**
     * @param CustomerShippingAddress $shipping_address
     * @return $this
     */
    public function setShippingAddress($shipping_address)
    {
        $this->shipping_address = $shipping_address;
        return $this;
    }
}
