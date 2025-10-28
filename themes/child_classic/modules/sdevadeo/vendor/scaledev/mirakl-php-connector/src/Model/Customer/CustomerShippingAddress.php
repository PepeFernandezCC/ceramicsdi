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

/**
 * Class CustomerShippingAddress
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class CustomerShippingAddress extends AbstractModel
{
    /**
     * Additional information for the shipping address
     *
     * @var string
     */
    private $additional_info;

    /**
     * Company of the person associated with the address
     *
     * @var string
     */
    private $company;

    /**
     * Mandatory only if the corresponding option is enabled for the orders created after the activation date. Country ISO 3166-1 code of the address, used by Mirakl to format the address (back office, delivery bill, invoices...) and to adapt the address validation to the country (see zip_code field)
     *
     * @var string
     */
    private $country_iso_code;

    /**
     * Phone
     *
     * @var string
     */
    private $phone;

    /**
     * Phone Secondary
     *
     * @var string
     */
    private $phone_secondary;

    /**
     * City of the contact
     * Api data: city
     *
     * @require true
     * @var string
     */
    private $city;

    /**
     * Civility of the contact (Mr, Mrs, Miss or Neutral)
     * Api data: civility
     *
     * @var string
     */
    private $civility;

    /**
     * Address Country
     *
     * @var string
     */
    private $country;

    /**
     * First name of the contact.
     * Api data: firstname
     *
     * @var string
     */
    private $firstname;

    /**
     * Last name of the contact
     * Api data: lastname
     *
     * @require true
     * @var string
     */
    private $lastname;

    /**
     * State (address) of the contact
     * Api data: state
     *
     * @var string
     */
    private $state;

    /**
     * First line of the address of the contact
     * Api data: street1
     *
     * @require true
     * @var string
     */
    private $street1;

    /**
     * Second line of the address of the contact
     * Api data: street2
     *
     * @var string
     */
    private $street2;

    /**
     * Zip code of the contact
     * Api data: zip_code
     *
     * @require true
     * @var string
     */
    private $zip_code;

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additional_info;
    }

    /**
     * @param string $additional_info
     * @return $this
     */
    public function setAdditionalInfo($additional_info)
    {
        $this->additional_info = $additional_info;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryIsoCode()
    {
        return $this->country_iso_code;
    }

    /**
     * @param string $country_iso_code
     * @return $this
     */
    public function setCountryIsoCode($country_iso_code)
    {
        $this->country_iso_code = $country_iso_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneSecondary()
    {
        return $this->phone_secondary;
    }

    /**
     * @param string $phone_secondary
     * @return $this
     */
    public function setPhoneSecondary($phone_secondary)
    {
        $this->phone_secondary = $phone_secondary;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
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
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * @param string $street1
     * @return $this
     */
    public function setStreet1($street1)
    {
        $this->street1 = $street1;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @param string $street2
     * @return $this
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * @param string $zip_code
     * @return $this
     */
    public function setZipCode($zip_code)
    {
        $this->zip_code = $zip_code;
        return $this;
    }
}
