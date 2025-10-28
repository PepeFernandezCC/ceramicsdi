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
 * Class ShopBankAccount
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShopBankAccount extends AbstractModel
{
    /**
     * Type of payment.
     * Expected values : ABA, AUBSB, BRAZILIAN, CANADIAN, COLOMBIAN, HK, IBAN,
     * INDIAN, JAPANESE, MEXICAN, NUBAN, NZBSB, TAIWANESE, THAI, UK, URUGUAYAN
     * API data: type
     *
     * @var string
     */
    private $type;

    /**
     * Bank account number
     * API data: bank_account_number | auban | account_number
     *
     * @var string
     */
    private $bankAccountNumber;

    /**
     * Bank city
     * API data: bank_city
     *
     * @var string
     */
    private $bankCity;

    /**
     * Bank name
     * API data: bank_name
     *
     * @var string
     */
    private $bankName;

    /**
     * Bank street name
     * API data: bank_street
     *
     * @var string
     */
    private $bankStreet;

    /**
     * Bank zip code
     * API data: bank_zip
     *
     * @var string
     */
    private $bankZipcode;

    /**
     * Bank account BIC code (aka Swift Code)
     * API data: bic
     *
     * @var string
     */
    private $bicCode;

    /**
     * Name of the owner of the payment information
     * API data: owner
     *
     * @var string
     */
    private $owner;

    /**
     * Routing Number of the bank, (aka ABA code)
     * API data: routing_number
     *
     * @var string
     */
    private $routingNumber;

    /**
     * Branch code
     * API data: aubsb | branch_code
     *
     * @var string
     */
    private $branchCode;

    /**
     * Branch name
     * API data: branch_name
     *
     * @var string
     */
    private $branchName;

    /**
     * Account Type
     * API data: bank_account_type
     *
     * @var string
     */
    private $bankAccountType;

    /**
     * Bank institution number
     * API data: institution_number
     *
     * @var string
     */
    private $institutionNumber;

    /**
     * Bank account swift code
     * API data: swift_code
     *
     * @var string
     */
    private $swiftCode;

    /**
     * Bank transit number
     * API data: transit_number
     *
     * @var string
     */
    private $transitNumber;

    /**
     * Bank account IBAN
     * API data: iban
     *
     * @var string
     */
    private $iban;

    /**
     * Bank account NUBAN
     * API data: nuban
     *
     * @var string
     */
    private $nuban;

    /**
     * Indian financial system code
     * API data: indian_financial_system_code
     *
     * @var string
     */
    private $indianSystemCode;

    /**
     * Clave Bancaria Estandarizada (see type=MEXICAN)
     * API data: clabe
     *
     * @var string
     */
    private $clabe;

    /**
     * Bank Sort Code (Size must be 6 digits)
     * API data: bank_sort_code
     *
     * @var string
     */
    private $sortCode;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountNumber()
    {
        return $this->bankAccountNumber;
    }

    /**
     * @param string $bankAccountNumber
     * @return $this
     */
    public function setBankAccountNumber($bankAccountNumber)
    {
        $this->bankAccountNumber = $bankAccountNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankCity()
    {
        return $this->bankCity;
    }

    /**
     * @param string $bankCity
     * @return $this
     */
    public function setBankCity($bankCity)
    {
        $this->bankCity = $bankCity;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return $this
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankStreet()
    {
        return $this->bankStreet;
    }

    /**
     * @param string $bankStreet
     * @return $this
     */
    public function setBankStreet($bankStreet)
    {
        $this->bankStreet = $bankStreet;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankZipcode()
    {
        return $this->bankZipcode;
    }

    /**
     * @param string $bankZipcode
     * @return $this
     */
    public function setBankZipcode($bankZipcode)
    {
        $this->bankZipcode = $bankZipcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getBicCode()
    {
        return $this->bicCode;
    }

    /**
     * @param string $bicCode
     * @return $this
     */
    public function setBicCode($bicCode)
    {
        $this->bicCode = $bicCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

    /**
     * @param string $routingNumber
     * @return $this
     */
    public function setRoutingNumber($routingNumber)
    {
        $this->routingNumber = $routingNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @param string $branchCode
     * @return $this
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * @param string $branchName
     * @return $this
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountType()
    {
        return $this->bankAccountType;
    }

    /**
     * @param string $bankAccountType
     * @return $this
     */
    public function setBankAccountType($bankAccountType)
    {
        $this->bankAccountType = $bankAccountType;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstitutionNumber()
    {
        return $this->institutionNumber;
    }

    /**
     * @param string $institutionNumber
     * @return $this
     */
    public function setInstitutionNumber($institutionNumber)
    {
        $this->institutionNumber = $institutionNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getSwiftCode()
    {
        return $this->swiftCode;
    }

    /**
     * @param string $swiftCode
     * @return $this
     */
    public function setSwiftCode($swiftCode)
    {
        $this->swiftCode = $swiftCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransitNumber()
    {
        return $this->transitNumber;
    }

    /**
     * @param string $transitNumber
     * @return $this
     */
    public function setTransitNumber($transitNumber)
    {
        $this->transitNumber = $transitNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     * @return $this
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }

    /**
     * @return string
     */
    public function getNuban()
    {
        return $this->nuban;
    }

    /**
     * @param string $nuban
     * @return $this
     */
    public function setNuban($nuban)
    {
        $this->nuban = $nuban;
        return $this;
    }

    /**
     * @return string
     */
    public function getIndianSystemCode()
    {
        return $this->indianSystemCode;
    }

    /**
     * @param string $indianSystemCode
     * @return $this
     */
    public function setIndianSystemCode($indianSystemCode)
    {
        $this->indianSystemCode = $indianSystemCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getClabe()
    {
        return $this->clabe;
    }

    /**
     * @param string $clabe
     * @return $this
     */
    public function setClabe($clabe)
    {
        $this->clabe = $clabe;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortCode()
    {
        return $this->sortCode;
    }

    /**
     * @param string $sortCode
     * @return $this
     */
    public function setSortCode($sortCode)
    {
        $this->sortCode = $sortCode;
        return $this;
    }

    public function getBuilderParameter($type)
    {
        $variableArray = array();
        switch ($type) {
            case 'ABA':
                $variableArray = array(
                    'bank_account_number' => array(
                        'constraints' => array(
                            'is_required' => 1,
                            'type' => 'string',
                            'length' => [3, 17]
                        ),
                        'model_method' => 'setBankAccountNumber',
                    ),
                    'bic' => array(
                        'constraints' => array(
                            'is_required' => 0,
                            'type' => 'string',
                        ),
                        'model_method' => 'setBicCode',
                    ),
                    'routing_member' => array(
                        'constraints' => array(
                            'is_required' => 1,
                            'type' => 'string',
                        ),
                        'model_method' => 'setRoutingMember',
                    )
                );
                break;
            case 'AUBSB':
                $variableArray = array(
                    'auban' => array(
                        'constraints' => array(
                            'is_required' => 1,
                            'type' => 'string',
                            'length' => [6, 10]
                        ),
                        'model_method' => 'setBankAccountNumber',
                    ),
                    'aubsb' => array(
                        'constraints' => array(
                            'is_required' => 1,
                            'type' => 'string',
                        ),
                        'model_method' => 'setBranchCode',
                    ),
                );
                break;
        }
        return array_merge($variableArray, $this->getConstantParameters());
    }

    private function getConstantParameters()
    {
        return array(
            'owner' => array(
                'constraints' => array(
                    'is_required' => 1,
                    'type' => 'string',
                    'length' => [3, 100]
                ),
                'model_method' => 'setOwner',
            ),
            'bank_city' => array(
                'constraints' => array(
                    'is_required' => 0,
                    'type' => 'string',
                    'length' => [3, 100]
                ),
                'model_method' => 'setBankCity',
            ),
            'bank_name' => array(
                'constraints' => array(
                    'is_required' => 1,
                    'type' => 'string',
                    'length' => [0, 255]
                ),
                'model_method' => 'setBankName',
            ),
            'bank_street' => array(
                'constraints' => array(
                    'is_required' => 0,
                    'type' => 'string',
                    'length' => [3, 100]
                ),
                'model_method' => 'setBankStreet',
            ),
            'bank_zip' => array(
                'constraints' => array(
                    'is_required' => 0,
                    'type' => 'string',
                    'length' => [0, 255]
                ),
                'model_method' => 'setBankZipCode',
            )
        );
    }
}
