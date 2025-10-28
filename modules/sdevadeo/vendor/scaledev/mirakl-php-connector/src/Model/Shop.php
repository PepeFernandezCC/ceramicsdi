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

use DateTimeInterface;
use Scaledev\MiraklPhpConnector\Collection\ShippingCollection;
use Scaledev\MiraklPhpConnector\Collection\AdditionalFieldCollection;
use Scaledev\MiraklPhpConnector\Collection\TaxCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;
use Scaledev\MiraklPhpConnector\Model\Shop\ShopKyc;
use Scaledev\MiraklPhpConnector\Model\Shop\ShopAddress;

/**
 * Class Shop
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Shop extends AbstractModel
{
    /**
     * List of the shop's applicable taxes
     * Api data: applicables_taxes
     *
     * @var TaxCollection
     */
    private $applicable_taxes;

    /**
     * Average time for a shop to accept or refuse an order (in seconds)
     * Api data: approval_delay
     *
     * @var int
     */
    private $approval_delay;

    /**
     * Rate of accepted orders
     * Api data: approval_rate
     *
     * @var number
     */
    private $approval_rate;

    /**
     * Url of the shop's banner image
     * Api data: banner
     *
     * @var string
     */
    private $banner;

    /**
     * List of the channel codes associated to the shop
     * Api data: channels
     *
     * @require true
     * @var array
     */
    private $channels;

    /**
     * The start date of the closing of the shop
     * Api data: closed_from
     *
     * @var DateTimeInterface
     */
    private $closed_from;

    /**
     * The end date of the closing of the shop
     * Api data: closed_to
     *
     * @var DateTimeInterface
     */
    private $closed_to;

    /**
     * Contact information
     * Api data: contact_informations
     *
     * @require true
     * @var ShopAddress
     */
    private $contact_informations;

    /**
     * The currency of the shop (iso format)
     * Api data: currency_iso_code
     *
     * @require true
     * @var string
     */
    private $currency_iso_code;

    /**
     * Creation date of the shop
     * Api data: date_created
     *
     * @require true
     * @var string
     */
    private $date_created;

    /**
     * The description of the shop, max length: 3000 characters.
     * Api data: description
     *
     * @var string
     */
    private $description;

    /**
     * Shop domains (waiting 'PRODUCT' or 'SERVICE')
     * Api data: domains
     *
     * @require true
     * @var array
     */
    private $domains;

    /**
     * Number of evaluations for the shop
     * Api data: evaluations_count
     *
     * @require true
     * @var int
     */
    private $evaluations_count;

    /**
     * Whether this shop offers free shipping to customers
     * Api data: free_shipping
     *
     * @require true
     * @var bool
     */
    private $free_shipping;

    /**
     * Global grade of the shop
     * Api data: grade
     *
     * @var number
     */
    private $grade;

    /**
     * Whether or not the shop is professional
     * Api data: is_professional
     *
     * @require true
     * @var bool
     */
    private $is_professional;

    /**
     * KYC information (only supported when KYC feature is enabled)
     * Api data: kyc
     *
     * @var ShopKyc
     */
    private $kyc;

    /**
     * The date of the last modification of the shop
     * Api data: last_updated_date
     *
     * @require true
     * @var DateTimeInterface
     */
    private $last_updated_date;

    /**
     * Url of the shop's logo image
     * Api data: logo
     *
     * @var string
     */
    private $logo;

    /**
     * Total count of offers of the shop
     * Api data: offers_count
     *
     * @require true
     * @var int
     */
    private $offers_count;

    /**
     * Average time for a shop to answer to an order message (in seconds)
     * Api data: order_messages_response_delay
     *
     * @var int
     */
    private $order_messages_response_delay;

    /**
     * Total count of orders of the shop
     * Api data: orders_count
     *
     * @require true
     * @var int
     */
    private $orders_count;

    /**
     * Whether the shop is premium
     * Api data: premium
     *
     * @require true
     * @var bool
     */
    private $premium;

    /**
     * Payment details
     * Api data: payment_details
     *
     * @require true
     * @var Company
     */
    private $payment_details;

    /**
     * List of the eco-contribution producers Ids, max length of each element : 255 characters - only available if the operator setting Activate data collection related to circular economy regulations has been enabled.
     * Api data: producer_ids
     *
     * @var array
     */
    private $producer_ids;

    /**
     * Recycling policy - only available if the operator setting Activate data collection related to circular economy regulations has been enabled.
     * Api data: recycling_policy
     *
     * @var string
     */
    private $recycling_policy;

    /**
     * Terms of restitution, max length: 5000 characters.
     * Api data: return_policy
     *
     * @var string
     */
    private $return_policy;

    /**
     * Code ISO 3166-1 alpha-3 of the shipping country
     * Api data: shipping_country
     *
     * @var string
     */
    private $shipping_country;

    /**
     * All the pair shipping zone / shipping type accepted by the shop
     * Api data: shippings
     *
     * @require true
     * @var ShippingCollection
     */
    private $shippings;


    /**
     * List of additional fields
     * Api data: shop_additional_fields
     *
     * @require true
     * @var AdditionalFieldCollection
     */
    private $shop_additional_fields;

    /**
     * The identifier of the shop
     * Api data: shop_id
     *
     * @require true
     * @var int
     */
    private $shop_id;

    /**
     * The name of the shop
     * Api data: shop_name
     *
     * @require true
     * @var string
     */
    private $shop_name;

    /**
     * State shop : OPEN or CLOSE (waiting: 'OPEN', 'CLOSE', 'SUSPENDED')
     * Api data: shop_state
     *
     * @require true
     * @var string
     */
    private $shop_state;

    /**
     * Shop suspension type, current possible values:
     * MANUAL: Shop account has been manually suspended in the back-office.
     * PENDING_APPROVAL: Shop account has been suspended at creation.
     * AUTOMATIC_SUSPENSION: Shop account has been suspended by Mirakl Quality Control.
     * INCOMPLETE_PROFILE: Shop account is suspended because some mandatory fields are missing in its profile.
     * Api data: suspension_type
     *
     * @var string
     */
    private $suspension_type;

    /**
     * @return TaxCollection
     */
    public function getApplicableTaxes()
    {
        return $this->applicable_taxes;
    }

    /**
     * @param TaxCollection $applicable_taxes
     * @return $this
     */
    public function setApplicableTaxes($applicable_taxes)
    {
        $this->applicable_taxes = $applicable_taxes;
        return $this;
    }

    /**
     * @return int
     */
    public function getApprovalDelay()
    {
        return $this->approval_delay;
    }

    /**
     * @param int $approval_delay
     * @return $this
     */
    public function setApprovalDelay($approval_delay)
    {
        $this->approval_delay = $approval_delay;
        return $this;
    }

    /**
     * @return number
     */
    public function getApprovalRate()
    {
        return $this->approval_rate;
    }

    /**
     * @param number $approval_rate
     * @return $this
     */
    public function setApprovalRate($approval_rate)
    {
        $this->approval_rate = $approval_rate;
        return $this;
    }

    /**
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param string $banner
     * @return $this
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param array $channels
     * @return $this
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getClosedFrom()
    {
        return $this->closed_from;
    }

    /**
     * @param DateTimeInterface $closed_from
     * @return $this
     */
    public function setClosedFrom($closed_from)
    {
        $this->closed_from = $closed_from;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getClosedTo()
    {
        return $this->closed_to;
    }

    /**
     * @param DateTimeInterface $closed_to
     * @return $this
     */
    public function setClosedTo($closed_to)
    {
        $this->closed_to = $closed_to;
        return $this;
    }

    /**
     * @return ShopAddress
     */
    public function getContactInformations()
    {
        return $this->contact_informations;
    }

    /**
     * @param ShopAddress $contact_informations
     * @return $this
     */
    public function setContactInformations($contact_informations)
    {
        $this->contact_informations = $contact_informations;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyIsoCode()
    {
        return $this->currency_iso_code;
    }

    /**
     * @param string $currency_iso_code
     * @return $this
     */
    public function setCurrencyIsoCode($currency_iso_code)
    {
        $this->currency_iso_code = $currency_iso_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param string $date_created
     * @return $this
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param array $domains
     * @return $this
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * @return int
     */
    public function getEvaluationsCount()
    {
        return $this->evaluations_count;
    }

    /**
     * @param int $evaluations_count
     * @return $this
     */
    public function setEvaluationsCount($evaluations_count)
    {
        $this->evaluations_count = $evaluations_count;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFreeShipping()
    {
        return $this->free_shipping;
    }

    /**
     * @param bool $free_shipping
     * @return $this
     */
    public function setFreeShipping($free_shipping)
    {
        $this->free_shipping = $free_shipping;
        return $this;
    }

    /**
     * @return number
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param number $grade
     * @return $this
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIsProfessional()
    {
        return $this->is_professional;
    }

    /**
     * @param bool $is_professional
     * @return $this
     */
    public function setIsProfessional($is_professional)
    {
        $this->is_professional = $is_professional;
        return $this;
    }

    /**
     * @return ShopKyc
     */
    public function getKyc()
    {
        return $this->kyc;
    }

    /**
     * @param ShopKyc $kyc
     * @return $this
     */
    public function setKyc($kyc)
    {
        $this->kyc = $kyc;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getLastUpdatedDate()
    {
        return $this->last_updated_date;
    }

    /**
     * @param DateTimeInterface $last_updated_date
     * @return $this
     */
    public function setLastUpdatedDate($last_updated_date)
    {
        $this->last_updated_date = $last_updated_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffersCount()
    {
        return $this->offers_count;
    }

    /**
     * @param int $offers_count
     * @return $this
     */
    public function setOffersCount($offers_count)
    {
        $this->offers_count = $offers_count;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderMessagesResponseDelay()
    {
        return $this->order_messages_response_delay;
    }

    /**
     * @param int $order_messages_response_delay
     * @return $this
     */
    public function setOrderMessagesResponseDelay($order_messages_response_delay)
    {
        $this->order_messages_response_delay = $order_messages_response_delay;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdersCount()
    {
        return $this->orders_count;
    }

    /**
     * @param int $orders_count
     * @return $this
     */
    public function setOrdersCount($orders_count)
    {
        $this->orders_count = $orders_count;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPremium()
    {
        return $this->premium;
    }

    /**
     * @param bool $premium
     * @return $this
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;
        return $this;
    }

    /**
     * @return Company
     */
    public function getPaymentDetails()
    {
        return $this->payment_details;
    }

    /**
     * @param Company $payment_details
     * @return $this
     */
    public function setPaymentDetails($payment_details)
    {
        $this->payment_details = $payment_details;
        return $this;
    }

    /**
     * @return array
     */
    public function getProducerIds()
    {
        return $this->producer_ids;
    }

    /**
     * @param array $producer_ids
     * @return $this
     */
    public function setProducerIds($producer_ids)
    {
        $this->producer_ids = $producer_ids;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecyclingPolicy()
    {
        return $this->recycling_policy;
    }

    /**
     * @param string $recycling_policy
     * @return $this
     */
    public function setRecyclingPolicy($recycling_policy)
    {
        $this->recycling_policy = $recycling_policy;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnPolicy()
    {
        return $this->return_policy;
    }

    /**
     * @param string $return_policy
     * @return $this
     */
    public function setReturnPolicy($return_policy)
    {
        $this->return_policy = $return_policy;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->shipping_country;
    }

    /**
     * @param string $shipping_country
     * @return $this
     */
    public function setShippingCountry($shipping_country)
    {
        $this->shipping_country = $shipping_country;
        return $this;
    }

    /**
     * @return ShippingCollection
     */
    public function getShippings()
    {
        return $this->shippings;
    }

    /**
     * @param ShippingCollection $shippings
     * @return $this
     */
    public function setShippings($shippings)
    {
        $this->shippings = $shippings;
        return $this;
    }

    /**
     * @return AdditionalFieldCollection
     */
    public function getAdditionalFieldCollection()
    {
        return $this->AdditionalFieldCollection;
    }

    /**
     * @param AdditionalFieldCollection $AdditionalFieldCollection
     * @return $this
     */
    public function setAdditionalFieldCollection($AdditionalFieldCollection)
    {
        $this->AdditionalFieldCollection = $AdditionalFieldCollection;
        return $this;
    }

    /**
     * @param int $shop_id
     * @return $this
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shop_name;
    }

    /**
     * @param string $shop_name
     * @return $this
     */
    public function setShopName($shop_name)
    {
        $this->shop_name = $shop_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getShopState()
    {
        return $this->shop_state;
    }

    /**
     * @param string $shop_state
     * @return $this
     */
    public function setShopState($shop_state)
    {
        $this->shop_state = $shop_state;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuspensionType()
    {
        return $this->suspension_type;
    }

    /**
     * @param string $suspension_type
     * @return $this
     */
    public function setSuspensionType($suspension_type)
    {
        $this->suspension_type = $suspension_type;
        return $this;
    }

    /**
     * @return AdditionalFieldCollection
     */
    public function getShopAdditionalFields()
    {
        return $this->shop_additional_fields;
    }

    /**
     * @param AdditionalFieldCollection $shop_additional_fields
     * @return $this
     */
    public function setShopAdditionalFields($shop_additional_fields)
    {
        $this->shop_additional_fields = $shop_additional_fields;
        return $this;
    }

}
