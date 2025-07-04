<?php

/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2007-2017 PrestaShop SA
 *  @version  Release: $Revision: 13573 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 *
 * @since 1.5.0
 */
require_once dirname ( __FILE__ ) . '/../../ApiRedsysREST/initRedsysApi.php';

class RedsyspurSecurePaymentV2ModuleFrontController extends ModuleFrontController {
	public function initContent() {
		if (session_status () != PHP_SESSION_ACTIVE)
			session_start ();
		
		if (! $this->module->active || ((! isset ( $_SESSION ["REDSYS_creq"] ) || ! isset ( $_SESSION ["REDSYS_urlacs"] )) && (! isset ( $_POST ["cres"] )))) {
			header ( $_SERVER ['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400 );
			die ( $_SERVER ['SERVER_PROTOCOL'] . ' 400 Bad Request: disabled payment module or incomplete request' );
		}
		
		$this->businessLogic ();
	}
	private function businessLogic() {
		$rds=new Redsyspur();
		if (! isset ( $_POST ["cres"] )) {
?>

			<iframe name="redsys_iframe_acs" name="redsys_iframe_acs" src=""
					id="redsys_iframe_acs" target="_parent" referrerpolicy="origin"
					sandbox="allow-same-origin allow-scripts allow-top-navigation allow-forms"
					height="95%" width="100%" style="border: none; display: none;"></iframe>
			
			<form name="redsysAcsForm" id="redsysAcsForm"
				action="<?php echo $_SESSION['REDSYS_urlacs'] ?>" method="POST"
				target="redsys_iframe_acs" style="border: none;">
				<table name="dataTable" border="0" cellpadding="0">
					<input type="hidden" name="creq"
						value="<?php echo $_SESSION ["REDSYS_creq"] ?>">
					<br>
					<p
						style="font-family: Arial; font-size: 16; font-weight: bold; color: black; align: center;">
						Conectando con el emisor...</p>
				</table>
			</form>
					
			<script>
				window.onload = function () {
					document.getElementById("redsys_iframe_acs").onload = function() {
						document.getElementById("redsysAcsForm").style.display="none";
						document.getElementById("redsys_iframe_acs").style.display="inline";
					}
					document.redsysAcsForm.submit();
				}
			</script>
<?php
			unset ( $_SESSION ['REDSYS_urlacs'] );
			unset ( $_SESSION ['REDSYS_creq'] );

			die ();
		} else {
			$response = array (
				"redir" => false,
				"url" => "" 
			);	
			
			$idLog = iniciarLog( Configuration::get( 'REDSYS_LOG' ), $_GET ["idLog"] );
			escribirLog("DEBUG", $idLog, "Proceso de autenticación V2", null, __METHOD__);
			escribirLog("DEBUG", $idLog, "La URL contiene parámetros para el pedido " . $_GET['order'] . " (" . $_GET ["idCart"] . ")", null, __METHOD__);

			$request = new RESTAuthenticationRequestMessage ();
			
			$request->setOrder ( $_GET['order'] );
			$request->setAmount ( $_GET['amount'] );
			$request->setCurrency ( $_GET['currency'] );
			$request->setMerchant ( Configuration::get ( 'REDSYS_FUC_TARJETA_INSITE' ) );
			$request->setTerminal ( Configuration::get ( 'REDSYS_TERMINAL_TARJETA_INSITE' ) );
			$request->setTransactionType ( $_GET['transactionType'] );
			$request->addEmvParameter ( RESTConstants::$RESPONSE_JSON_THREEDSINFO_ENTRY , RESTConstants::$RESPONSE_3DS_CHALLENGE_RESPONSE );
			$request->addEmvParameter ( RESTConstants::$RESPONSE_JSON_PROTOCOL_VERSION_ENTRY , $_GET['protocolVersion'] );
			$request->addEmvParameter ( RESTConstants::$RESPONSE_MERCHANT_EMV3DS_CRES , $_POST ["cres"] );
			
			$service = new RESTAuthenticationRequestService ( Configuration::get ( 'REDSYS_CLAVE256_TARJETA_INSITE' ), Configuration::get ( 'REDSYS_URLTPV_INSITE' ) );
			$result = $service->sendOperation ( $request, $idLog );

			$urlOK = Configuration::get( 'REDSYS_URLOK' );
			$urlKO = Configuration::get( 'REDSYS_URLKO' );
			$urlDst = $urlKO ? $urlKO : $this->module->_endpoint_paymentko;
			
			$params = $this->createParameters ( $_GET['order'], $_GET ["idCart"], false );
			$cart = new Cart ( $_GET['idCart'] );
			$customer = new Customer ( $cart->id_customer );
			
			$resultCode = $result->getResult ();
			$apiCode = $result->getApiCode ();
			$authCode = $result->getAuthCode ();

			$respuestaSIS = $rds->checkRespuestaSIS($apiCode, $authCode);

			if ($result->getResult () == RESTConstants::$RESP_LITERAL_OK) {

				$urlDst = $rds->validateCart ( $cart, $params ["merchant_order"], $_GET['order'], $customer, $params ["amount"], $params ["idCurrency"], $result->getOperation ()->getMerchantIdentifier (), $result->getOperation ()->getCardNumber (), $result->getOperation()->getCardBrand(), $result->getOperation()->getCardType(), $authCode, $respuestaSIS[1], $idLog, $result->getOperation ()->getTransactionType ());
				$urlDst = $urlOK ? $urlOK : $urlDst;

				escribirLog("INFO ", $idLog, "Orden validada", null, __METHOD__);
				escribirLog("INFO ", $idLog, $respuestaSIS[0]);
			} else { //FLUJO KO
				if (Configuration::get ( 'REDSYS_URLTPV_INSITE' ) == "1")
					setcookie ( "redsys" . $_POST ["idCart"], "N", time () + (3600 * 24), __PS_BASE_URI__ );

				if(Configuration::get('REDSYS_MANTENER_CARRITO') == 0){
					$rds->validateOrder($params ["idCart"], _PS_OS_CANCELED_, $params ["amount"]/100, "Redsys - Tarjeta", null);
				}
				$rds->addPaymentInfo($params ["idCart"], $params ["merchant_order"], $respuestaSIS[1], $idLog);

				escribirLog("INFO ", $idLog, "El pedido ha finalizado con errores", null, __METHOD__);
				escribirLog("INFO ", $idLog, $respuestaSIS[0]);
			}
?>

<p style="font-family: Arial; font-size: 16; font-weight: bold; color: black; align: center;">
	<?php echo $rds->l('Procesando operación')?>...
</p>
<script>
	window.top.top.location.href="<?php echo $urlDst;?>"
</script>

<?php
			die ();
		}
	}
	private function createParameters($merchant_order, $idCart, $save) {
		$params = array ();
		
		$cart = new Cart ( $idCart );
		$customer = new Customer ( $cart->id_customer );

		$address = new Address((int)$cart->id_address_invoice);

		/** Generamos los contextos necesarios */
		Context::getContext()->customer = $customer;
		Context::getContext()->country = new Country((int)$address->id_country);
		Context::getContext()->language = new Language((int)$cart->id_lang);
		Context::getContext()->currency = new Currency((int)$cart->id_currency);

		// Calculate Amount
		$currency = new Currency ( $cart->id_currency );
		$currency_decimals = is_array ( $currency ) ? ( int ) $currency ['decimals'] : ( int ) $currency->decimals;
		$cart_details = $cart->getSummaryDetails ( null, true );
		$decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;

		$total_price = (int) round(($cart->getOrderTotal(true, Cart::BOTH) * 100), 0);

		if (! empty (Configuration::get( 'REDSYS_CORRECTOR_IMPORTE' )) ) {
			$total_price *= (float)Configuration::get( 'REDSYS_CORRECTOR_IMPORTE' );
			$total_price = (int)$total_price;
		}

		// Product Description
		$products = $cart->getProducts ();
		$productsDesc = '';
		foreach ( $products as $product )
			$productsDesc .= $product ['quantity'] . ' ' . Tools::truncate ( $product ['name'], 50 ) . ' - ';
		$productsDesc = substr ( $productsDesc, 0, strlen ( $productsDesc ) - 3 );
		
		$params ["merchant_order"] = $merchant_order;
		$params ["idCart"] = $idCart;
		$params ["amount"] = $total_price;
		$params ["currency"] = $currency->iso_code_num;
		$params ["idCurrency"] = $currency->id;
		$params ["decimals"] = $decimals;
		
		return $params;
	}
}