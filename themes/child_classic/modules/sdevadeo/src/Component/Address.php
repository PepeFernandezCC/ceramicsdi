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
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Component;
if (!defined('_PS_VERSION_')) {
    exit;
}

use \Address as PsAddress;
use Country;
use Db;
use Scaledev\Adeo\Core\Tools;
use function pSQL;
use const _DB_PREFIX_;

/**
 * Class Scaledev\Adeo\Component\SdevAdeoAddress
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Address extends PsAddress
{
    public function createAlias($address)
    {
        if (!is_array($address) || !$address) {
            return false;
        }

        $content = null;

        foreach ($address as $value) {
            if (is_string($value)) {
                $content .= $value;
            }
        }

        return md5($content);
    }

    public function createOrGetAddress($address, $id_customer,  $dni = null, $validateOnly = true)
    {
        $alias = $this->createAlias($address);

        foreach ($address as &$value) {
            if (is_array($value) && !$value) {
                $value = '';
            }
        }

        // Mode validation uniquement, on force sur adresse introuvable
        if ($validateOnly) {
            $address_exists = false;
        } else {
            $address_exists = Db::getInstance()->getValue(
                'SELECT `id_address`
                FROM `' . _DB_PREFIX_ . 'address`
                WHERE `id_customer` = ' . (int)$id_customer . '
                    AND `alias` = \'' . pSQL($alias) . '\''
            );
        }

        if ($address_exists) {
            return (int)$address_exists;
        } else {
            $this->alias = $alias;
            $this->id_customer = $id_customer;
            $this->company = '';
            $this->firstname = Tools::cleanName($address['firstname']);
            $this->lastname = Tools::cleanName($address['lastname']);
            $street = $address['street1'] ? Tools::cleanAddress($address['street1']) : '';
            $street .= $address['street2'] ? ' '.Tools::cleanAddress($address['street2']) : '';
            $this->address1 = Tools::cleanAddress(trim($street));

            $city = !empty($address['city']) ? $address['city'] : 'nocity';
            if (!empty($address['phone'])) {
                $phone_number = $address['phone'];
            } elseif (!empty($address['phone_secondary'])) {
                $phone_number = $address['phone_secondary'];
            } else {
                $phone_number = '0000000000';
            }

            if (
                array_key_exists('phone_secondary', $address) && $address['phone_secondary']
                && (
                    ($address['country_iso_code'] == 'FR' && Tools::substr($address['phone_secondary'], 0, 4) == '+336')
                    || Tools::substr($address['phone_secondary'], 0, 4) == '+337'
                )
            ) {
                $this->phone_mobile = $address['phone_secondary'];
            } else if (($address['country_iso_code'] == 'FR' && Tools::substr($phone_number, 0, 4) == '+336')
                || Tools::substr($phone_number, 0, 4) == '+337') {
                $this->phone_mobile = $phone_number;
            } else {
                $this->phone = $phone_number;
            }

            $this->other = (array_key_exists('other', $address) && !empty($address['other'])) ? $address['other'] : '';
            $this->postcode = !empty($address['zip_code']) ? $address['zip_code'] : '00000';
            $this->city = Tools::cleanCity($city);
            $this->id_country = Country::getByIso(!empty($address['country']) ? $address['country'] : 'FR');

            if ($dni) {
                $this->dni = $dni;
            }

            if ($validateOnly) {
                return $this->validateController();
            }

            $this->add();

            return $this->id;
        }
    }
}
