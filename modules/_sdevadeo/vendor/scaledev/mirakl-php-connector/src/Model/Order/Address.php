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

namespace Scaledev\MiraklPhpConnector\Model\Order;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Address
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Address extends AbstractModel
{
    /**
     * Mandatory only if the corresponding option is enabled for the orders created after the activation date. Country ISO 3166-1 code of the address, used by Mirakl to format the address (back office, delivery bill, invoices...) and to adapt the address validation to the country (see zip_code field)
     *
     * @var string
     */
    private $country_iso_code;

    /**
     * City of the contact
     * Api data: city
     *
     * @require true
     * @var string
     */
    private $city;

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
