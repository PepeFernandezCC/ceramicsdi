<?php

/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__) . '/../vendor/datatables/autoload.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/CorreosOficialUtils.php';

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Ozdemir\Datatables\DB\PSAdapter;

class CorreosOficialUtilitiesDataTable
{
    protected $dt;
    protected $context;
    // Contexto en el que se encuentra el admin
    private $shopContext;


    public function __construct()
    {
        $this->dt = new Datatables(new PSAdapter([]));
        $this->context = Context::getContext();
        $this->shopContext = Shop::getContext();
    }

    public function loadColumnContent( $getColumn ) {

		switch ($getColumn) {
			case 'reference':
				$this->dt->edit('reference', function ( $data ) {
					return $data['reference'];
				});
				break;
            case 'products':
                $this->dt->edit('products', function ($data) {
                    return $this->getProductsNames($data);
                });
                break;
            case 'carrier_type':
                $this->dt->add('carrier_type', function ( $data ) {
                    return $this->getCarrierType($data);
                });
                break;
            case 'pickup_number':
                $this->dt->add('pickup_number', function ( $data ) {
                    return $this->getPickupOrReturnPickupNumber($data);
                });
                break;
			case 'order_state':
					$this->dt->edit('order_state', function ( $data ) {
                        $order = new Order($data['id_order']);
                        $order_state = new OrderState($order->getCurrentState(), $order->id_lang);
                        return $order_state->name;
					});
				break;
            case 'customer_name':
                $this->dt->edit($getColumn, function ($data) {
                    return $this->getCustomerName($data);
                });
                break;
            default:
                throw new LogicException('Código Error 17030: No se ha encontrado el filtro ha utilizar');
		}
	}

    public function getProductsNames($data) {
        $order = new Order($data['id_order']);
        $products = $order->getProducts();
        $productName = '';

        foreach ($products as $product) {
            $decodedString = html_entity_decode($product['product_name'], ENT_QUOTES, 'UTF-8');
            $productName .= $decodedString . ' ';
        }
        return strlen($productName) > 30 ? mb_substr($productName, 0, 27, 'UTF-8') . '...' : $productName;
    }

    public function getCarrierType($data) {
        if ($data['shipping_number']) {
            $function = new CorreosOficialDAO();
            $company = $function->getRecordsWithQuery($this->getSavedOrderCarrier($data['shipping_number']), true);
            return $company[0]['company'];
        }
        return $data['company'];
    }

    public function getPickupOrReturnPickupNumber ($data) {
        if (isset ($data['pickup_number']) && $data['pickup_number'] != '') {
            return $data['pickup_number'];
        }

        if (isset ($data['pickup_return']) && $data['pickup_return'] != '') {
            return $data['pickup_return'];
        }

        return '';
    }

    public function getCustomername($data) {

        $order = new Order($data['id_order']);

        if ($order) {
            $address = new Address($order->id_address_delivery);
            if ($address) {
                $firstName = $address->firstname;
                $lastName = $address->lastname;
                return $firstName . ' ' . $lastName;
            }
        }
    }

    // Filtro por creación de pedido
	public function loadDateFilter( $from, $to ) {

		if ($from == '1970-01-01' && $to == '1970-01-01') {
			$from = gmdate('Y-m-d');
			$to = gmdate('Y-m-d');
		}

		$this->dt->filter('date_add', function () use ( $from, $to ) {

			$value = $this->searchValue() ? $this->searchValue() : '';

			if (!$value) {
				return $this->between($from . ' 00:00:00', $to . ' 23:59:59');
			}
		});
	}

	// Filtro por etiquetado
	public function loadByDateFilter( $field, $from, $to ) {

		if ($from == '1970-01-01' && $to == '1970-01-01') {
			$from = gmdate('Y-m-d');
			$to = gmdate('Y-m-d');
		}

		$this->dt->filter($field, function () use ( $from, $to ) {
			$value = $this->searchValue() ? $this->searchValue() : '';

			if (!$value) {
				return $this->between($from . ' 00:00:00', $to . ' 23:59:59');
			}
		});
	}

    public function loadGestionDatatableSelectors() {

		$senders = $this->getSenders();

		$this->dt->add('senders', function ( $data ) use ( $senders ) {

			$disable = '';

			if ($data['first_shipping_number']) {
				$disable = 'disabled';
			}

			$select = "<select id='sender_option_" . $data['id_order']
				. "' name='sender_option_" . $data['id_order']
				. "' class='custom-select select_sender' required " . $disable . '>';

			foreach ($senders as $sender) {
				$selected = '';

				if ($data['saved_sender'] == $sender['sender_id'] || $sender['sender_data']['default'] == 1) {
					$selected = 'selected';
				}

				$select .= "<option data-iso='" . $sender['sender_data']['sender_iso_code']
					. "' data-scope='" . $sender['sender_data']['company']
					. "' value='" . $sender['sender_id'] . "' " . $selected . '>'
					. $sender['sender_data']['name'] . '</option>';
			}

			$select .= '</select>';
			return $select;
		});
	}

	public function getSenders() {
		// Lista de remitentes
		$senders = array();

		foreach (CorreosOficialSendersDao::getSendersWithCodes() as $sender) {

			$senderCompany = '';
			if ($sender['correos_code'] != 0 && $sender['cex_code'] == 0) {
				$senderCompany = 'Correos';
			} elseif ($sender['cex_code'] != 0 && $sender['correos_code'] == 0) {
				$senderCompany = 'CEX';
			} elseif ($sender['correos_code'] != 0 && $sender['cex_code'] != 0) {
				$senderCompany = 'all';
			}

			$senders[] = array(
				'sender_id' => $sender['id'],
				'sender_data' => array(
					'name' => $sender['sender_name'],
					'company' => $senderCompany,
					'sender_iso_code' => $sender['sender_iso_code_pais'],
					'default' => $sender['sender_default'],
				),
			);
		}

		return $senders;
	}

    public function getDataFromDataTables($from, $to)
    {
        $defaultPackage = CorreosOficialConfigDao::getConfigValue('DefaultPackages');

        $find_by_shop = ($this->shopContext == 1) ? 'AND po.id_shop='.$this->context->shop->id : '';

        $sql = "
            SELECT
                'null' as c0,
                po.id_order as id_order,
                po.reference as reference,
                GROUP_CONCAT(DISTINCT pl.name SEPARATOR ', ') as products,
                cos.exp_number as first_shipping_number,
                IF (prd.company IS NULL, (SELECT name FROM " . _DB_PREFIX_ . "carrier WHERE id_carrier = po.id_carrier LIMIT 1), prd.company) as carrier_type,
                osl.name as order_state,
                concat(a.firstname, ' ', a.lastname) as customer_name,
                po.date_add as date_add,
                cor.reference_code as office,
                IF (prd.name IS NULL, (SELECT name FROM " . _DB_PREFIX_ . "correos_oficial_products WHERE id = cprd.id_product LIMIT 1), prd.name) as prdname,
                'null' as sender,
                IF (coo.id_product IS NULL, (SELECT id FROM " . _DB_PREFIX_ . "correos_oficial_products WHERE id_carrier = po.id_carrier LIMIT 1), coo.id_product) as id_product,
                IFNULL(coo.bultos,1) as bultos,
                coo.AT_code as AT_code,
                po.id_shop,
                po.id_carrier as carrier,
                po.current_state as current_state,
                coo.shipping_number as shipping_number,
                coo.id_sender as saved_sender,
                prd.company as company,
                prd.max_packages as max_packages,
                prd.codigoProducto as codigoProducto,
                prd.product_type as product_type,
                cprd.id_product as id_product_custom,
                IF (prd.max_packages IS NULL, (SELECT max_packages FROM " . _DB_PREFIX_ . "correos_oficial_products WHERE id = cprd.id_product LIMIT 1), null) as max_packages_custom,
                c.iso_code as delivery_iso_code,
                c.id_zone as id_zone,
                IF(coo.shipping_number !='',
                    (SELECT sender_iso_code_pais FROM " . _DB_PREFIX_ . "correos_oficial_senders WHERE id=coo.id_sender  AND id_shop=".$this->context->shop->id." LIMIT 1),
                    (SELECT sender_iso_code_pais FROM " . _DB_PREFIX_ . "correos_oficial_senders WHERE sender_default=1  AND id_shop=".$this->context->shop->id." LIMIT 1)
                ) as sender_iso_code
            FROM
                " . _DB_PREFIX_ . "orders po
            LEFT JOIN
                " . _DB_PREFIX_ . "order_detail od ON od.id_order = po.id_order
            LEFT JOIN
                " . _DB_PREFIX_ . "product_lang pl ON pl.id_product = od.product_id AND pl.id_lang = 1 AND pl.id_shop=po.id_shop
            LEFT JOIN
                " . _DB_PREFIX_ . "address a ON (a.id_address = po.id_address_delivery)
            LEFT JOIN
                " . _DB_PREFIX_ . "country c ON (c.id_country = a.id_country)
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_orders coo ON (po.id_order = coo.id_order)
            LEFT JOIN
                " . _DB_PREFIX_ . "customer cus ON (cus.id_customer = po.id_customer)
            LEFT JOIN
                " . _DB_PREFIX_ . "order_state_lang osl ON (osl.id_order_state = po.current_state)
            LEFT JOIN
                " . _DB_PREFIX_ . "lang la ON (la.id_lang = osl.id_lang)
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_products prd ON ( IF (coo.id_product IS NULL, po.id_carrier = prd.id_carrier, coo.id_product = prd.id) )
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_carriers_products cprd ON ( cprd.id_carrier = po.id_carrier ) AND ( cprd.id_zone = c.id_zone ) AND cprd.id_shop=".$this->context->shop->id."
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_senders cose ON (cose.id = coo.id_sender) AND cose.id_shop=".$this->context->shop->id."
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_requests cor ON (cor.id_cart = po.id_cart)
            WHERE
                la.iso_code = 'es'" . "
                AND TIMESTAMPDIFF(MONTH, po.date_add, now()) < 2
                ".$find_by_shop."
            GROUP BY
                po.id_order, reference_code, osl.name, prd.company, prd.name, cprd.id_product";
        
        $this->dt->query($sql);

        $this->loadColumnContent('reference');
		$this->loadColumnContent('order_state');
		$this->loadColumnContent('customer_name');
        $this->loadColumnContent('products');

        unset($sql);
        
        $context = $this->context;
		$this->dt->filter('order_state', function () use ($context) {
			if ($this->searchValue()) {
				$bestMatch = null;
				$bestMatchCount = 0;
				$return = "order_state LIKE '%" . $this->searchValue() . "%'";

                // Buscamos concordancias con las traducciones de los estados
                foreach (OrderState::getOrderStates($context->language->id) as $key => $state) {
                    $position = stripos($state['name'], $this->searchValue());
                    if ($position !== false) {
                        $matchCount = substr_count(strtolower($state['name']), strtolower($this->searchValue()));
                        if ($matchCount > $bestMatchCount) {
                            $bestMatchCount = $matchCount;
                            $bestMatch = $key;
                        }
                    }
                }

				if ($bestMatch) {
					$return .= " OR order_state LIKE '%" . $bestMatch . "%'";
				}

				return $return;
			}
		});

		$this->loadDateFilter($from, $to);
		$this->loadGestionDatatableSelectors();

		$this->dt->edit('bultos', function ( $data ) use ( $defaultPackage ) {
			if (!$data['bultos']) {
				return $defaultPackage;
			}
			return $data['bultos'];
		});

        exit($this->dt->generate());
    }

    public function getDataFromDataTablesForReprintAndPickups($from, $to, $datatable='labelsdatatable', $onlyCorreos = false) {
        $find_by_shop = ($this->shopContext == 1) ? 'AND po.id_shop='.$this->context->shop->id : '';
        $pickupMode = '';
        $products = '';

        // Recogidas es solo para Correos
        if ($onlyCorreos) {
            $pickupMode = ' AND prd.company = "Correos"';
        }


        /**
         * El orden de campos es diferente para Reimpresión que para Recogidas
         */
        if ($datatable == 'labelsdatatable') { // Campos para pestaña de Reimpresión de Etiquetas
            $products = "GROUP_CONCAT(DISTINCT pl.name SEPARATOR ', ') as products, ";
            $id_shop_for_reprint = ' po.id_shop, ';
            $id_shop_for_pickups = '';
        } else { // Campos para Recogidas
            $id_shop_for_reprint = '';
            $id_shop_for_pickups = ' po.id_shop, ';
        }

        $sql = "SELECT
                'null' AS c0,
                po.id_order AS id_order,
                po.reference AS reference,
                $products
                cos.exp_number AS first_shipping_number,
                prd.company AS company,
                CONCAT(adr.firstname, ' ', adr.lastname) AS customer_name,
                adr.address1 AS customer_address,
                COALESCE(copr.pickup_date, po.date_add) AS date_add,
                $id_shop_for_reprint
                coo.bultos AS bultos,
                coo.package_size AS package_size,
                coo.print_label AS label,
                coo.pickup_number AS pickup_number,
                $id_shop_for_pickups
                coo.id_product AS id_product,
                prd.codigoProducto AS codigoProducto,
                coo.last_status AS last_status,
                coo.status AS status,
                coo.shipping_number AS shipping_number
            FROM
                " . _DB_PREFIX_ . "orders po
            LEFT JOIN
                " . _DB_PREFIX_ . "order_detail od ON od.id_order = po.id_order
            LEFT JOIN
                " . _DB_PREFIX_ . "product_lang pl ON pl.id_product = od.product_id AND pl.id_lang = 1 AND pl.id_shop = po.id_shop
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_orders coo ON po.id_order = coo.id_order
            LEFT JOIN
                " . _DB_PREFIX_ . "address adr ON adr.id_address = po.id_address_delivery
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_products prd ON coo.id_product = prd.id
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_saved_orders cos ON coo.shipping_number = cos.exp_number
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_pickups_returns copr ON copr.id_order = po.id_order
            WHERE
                coo.shipping_number != ''
                AND TIMESTAMPDIFF(MONTH, COALESCE(copr.pickup_date, po.date_add), NOW()) < 2
                " . $pickupMode . "
                " . $find_by_shop . "
            GROUP BY
                shipping_number, pickup_number, id_order";
                    
        $this->dt->query($sql);
        unset($sql);
        
		$this->loadColumnContent('reference');
		$this->loadColumnContent('customer_name');

        // El modo pickup no tiene array de productos. Solo si NO es $pickupMode
        if (empty($pickupMode)) {
            $this->loadColumnContent('products');
         }

		$this->loadByDateFilter('date_add', $from, $to);
		
		exit($this->dt->generate());

    }

    // se crea una función única para Resumen ya que se añaden nuevos campos a la tabla y para el ordenado hay que respetar ese orden en el select
    public function getDataFromDataTablesForResumen($from, $to, $searchByLabelingDate, $searchBySender) {

        $find_by_shop = ($this->shopContext == 1) ? 'AND po.id_shop='.$this->context->shop->id : '';

        $querySearchSender = ($searchBySender !== false && $searchBySender !== '') ? " AND sender = ". (int) $searchBySender : '';

        $sql = "SELECT
            'null' AS c0,
            po.id_order,
            po.reference,
            (SELECT GROUP_CONCAT(shipping_number) 
            FROM " . _DB_PREFIX_ . "correos_oficial_saved_orders 
            WHERE id_order = po.id_order) AS package_code,
            cos.exp_number AS shipping_number,
            prd.company AS company,
            IF(prd.company LIKE 'CEX',
                (SELECT CEXCustomer FROM " . _DB_PREFIX_ . "correos_oficial_codes WHERE id = cose.cex_code),
                (SELECT CorreosCustomer FROM " . _DB_PREFIX_ . "correos_oficial_codes WHERE id = cose.correos_code)
            ) AS customer_code,
            CONCAT(adr.firstname, ' ', adr.lastname) AS customer_name,
            adr.address1 AS customer_address,
            adr.postcode AS cpostal,
            COALESCE(copr.pickup_date, po.date_add) AS date_add,
            coo.date_add as labeling_date,
            CASE WHEN coo.manifest_date IS NOT NULL THEN 'S' ELSE 'N' END AS manifested,
            coo.manifest_date AS manifest_date,
            po.id_shop,
            COALESCE(coo.pickup_number, copr.pickup_number) AS pickup_number,
            coo.id_sender AS sender

        FROM
            " . _DB_PREFIX_ . "orders po
        LEFT JOIN
            " . _DB_PREFIX_ . "order_detail od ON od.id_order = po.id_order
        LEFT JOIN
            " . _DB_PREFIX_ . "product_lang pl ON pl.id_product = od.product_id AND pl.id_lang = 1 AND pl.id_shop = po.id_shop
        LEFT JOIN
            " . _DB_PREFIX_ . "correos_oficial_orders coo ON po.id_order = coo.id_order
        LEFT JOIN
            " . _DB_PREFIX_ . "address adr ON adr.id_address = po.id_address_delivery
        LEFT JOIN
            " . _DB_PREFIX_ . "correos_oficial_products prd ON coo.id_product = prd.id
        LEFT JOIN
            " . _DB_PREFIX_ . "correos_oficial_saved_orders cos ON cos.id_order = coo.id_order
        LEFT JOIN
            " . _DB_PREFIX_ . "correos_oficial_pickups_returns copr ON copr.id_order = po.id_order
        LEFT JOIN
            " . _DB_PREFIX_ . "correos_oficial_senders cose ON coo.id_sender = cose.id
        WHERE
            coo.shipping_number != ''
            AND TIMESTAMPDIFF(MONTH, COALESCE(copr.pickup_date, po.date_add), NOW()) < 2
            " . $querySearchSender . "
            " . $find_by_shop . "
        GROUP BY
            coo.shipping_number, pickup_number, po.id_order";
  
        $this->dt->query($sql);
        unset($sql);

        // si está activado el check labeling date utilizamos esa fecha, si no la del pedido de PS
        if ($searchByLabelingDate) {
			$this->loadByDateFilter('labeling_date', $from, $to);
		} else {
			$this->loadByDateFilter('date_add', $from, $to);
		}

		$this->dt->filter('sender', function () use ( $searchBySender ) {
			if ($searchBySender == '0') {
				return "sender LIKE '%%'";
			}
			return "sender = '" . $searchBySender . "'";
		});

		exit($this->dt->generate());

    }

    public function getDataFroShippingCustomDoc($from, $to) {

        $find_by_shop = ($this->shopContext == 1) ? 'AND po.id_shop='.$this->context->shop->id : '';

        $sql = "
            SELECT
                'null' as c0,
                po.id_order as id_order,
                po.reference as reference,
                cos.exp_number as first_shipping_number,
                prd.company as company,
                concat(adr.firstname,' ',adr.lastname) as customer_name,
                adr.address1 as customer_address,
                ctry.iso_code as customer_country,
                po.date_add as date_add,
                po.id_shop,
                coo.shipping_number as shipping_number,
                coo.bultos as bultos,
                coo.require_customs_doc as custom_doc
            FROM
                " . _DB_PREFIX_ . "orders po
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_orders coo ON (po.id_order = coo.id_order)
            LEFT JOIN
                " . _DB_PREFIX_ . "address adr ON (adr.id_address = po.id_address_delivery)
            LEFT JOIN
                " . _DB_PREFIX_ . "country ctry ON (ctry.id_country = adr.id_country)
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_products prd ON (coo.id_product = prd.id)
            LEFT JOIN
                " . _DB_PREFIX_ . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            WHERE
                coo.require_customs_doc = 1 AND coo.shipping_number != '' AND prd.company='Correos'" . "
                AND TIMESTAMPDIFF(MONTH, po.date_add, now()) < 2
                ".$find_by_shop."
            GROUP BY
                po.id_order";

        $this->dt->query($sql);
        unset($sql);

        $this->loadColumnContent('reference');
		$this->loadColumnContent('customer_name');

		$this->loadDateFilter($from, $to);

		$this->dt->filter('custom_doc', function () {
			return $this->greaterThan(1);
		});

		exit($this->dt->generate());
    }

	private function getSavedOrderCarrier( $shippingNumber ) {
		return "SELECT cop.name as 'product_name', cop.company as company FROM " . _DB_PREFIX_ . 'correos_oficial_orders coo
        LEFT JOIN ' . _DB_PREFIX_ . "correos_oficial_products cop ON (cop.id = coo.id_product) WHERE coo.shipping_number='" . $shippingNumber . "'";
	}

}
