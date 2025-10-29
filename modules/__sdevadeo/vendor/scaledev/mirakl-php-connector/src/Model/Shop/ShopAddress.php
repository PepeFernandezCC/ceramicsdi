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

namespace Scaledev\MiraklPhpConnector\Model\Shop;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Address
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShopAddress extends AbstractModel
{
    /**
     * Email of the contact
     * Api data: email
     *
     * @required true
     * @var string
     */
    private $email;

    /**
     * Fax of the contact
     * Api data: fax
     *
     * @required false
     * @var string
     */
    private $fax;

    /**
     * Phone number of the contact
     * Api data: phone
     *
     * @required false
     * @var string
     */
    private $phone;

    /**
     * Additional phone number of the contact
     * Api data: phone_secondary
     *
     * @required false
     * @var string
     */
    private $phone_secondary;

    /**
     * Website address of the contact
     * Api data: web_site
     *
     * @required false
     * @var string
     */
    private $web_site;

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
     * Code ISO 3166-1 alpha-3 of the contact's country
     * API data: country
     *
     * @require true
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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
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
    public function getWebSite()
    {
        return $this->web_site;
    }

    /**
     * @param string $web_site
     * @return $this
     */
    public function setWebSite($web_site)
    {
        $this->web_site = $web_site;
        return $this;
    }
}
