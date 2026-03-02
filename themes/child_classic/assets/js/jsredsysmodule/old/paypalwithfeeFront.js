/*
 *
 * NOTICE OF LICENSE
 *
 * @category payment_gateways
 * @author www.4webs.es
 * @copyright 4webs 2016
 * @version 5.1.4
 *
 * 
 *  paypalwithfee
 *  Languages: EN
 *  PS version: 1.7
 *
 */

$(document).ready(function(){
	$(document).on('change', 'input[name="payment-option"]', function(e){
		let mod_name = $(this).data('module-name');
		console.log(mod_name);
		if ($(this).attr('data-module-name') == 'paypalwithfee' && $(this).is(':checked')){

			$fee = $('#ppalwf_fee').text();
			$noFee = $('#ppalwf_fee').attr('no_fee');

			if($noFee == 'false'){
				$total = $('#ppalwf_total').text();
				if($('#paypalwithfee-feerow').length == 0){
					$feeRow = $('<div>').addClass('cart-summary-line').attr('id', 'paypalwithfee-feerow');
					$feeRow.append($('<span>').addClass('label').text($('#ppalwf_tfee').text()));
					$feeRow.append($('<span>').addClass('value').text($fee));
					$('#cart-subtotal-shipping').after($feeRow);
				}

				$('.cart-total .value').text($total);

				$feeRow_table = $('<tr>').attr('id', 'paypalwithfee-feerow-table');
				$feeRow_table.append($('<td>').text($('#ppalwf_tfee').text()));
				$feeRow_table.append($('<td>').text($fee));
				$('#order-items > div.order-confirmation-table > table > tbody > tr.total-value.font-weight-bold').before($feeRow_table);
				$('#order-items > div.order-confirmation-table > table > tbody > tr.total-value.font-weight-bold > td:nth-child(2)').text($total);
			}

		}else if (mod_name == 'transbancaria') {
			// other 4webs payment modules compatibility
			$("#paypalwithfee-feerow").remove();
			$("#paypalwithfee-feerow-table").remove();
		} else if (mod_name == 'reembolsocargo') {
			// other 4webs payment modules compatibility
			$("#paypalwithfee-feerow").remove();
			$("#paypalwithfee-feerow-table").remove();
		} else if (mod_name == 'pagoentienda') {
			// other 4webs payment modules compatibility
			$("#paypalwithfee-feerow").remove();
			$("#paypalwithfee-feerow-table").remove();
		} else {
			$("#paypalwithfee-feerow").remove();
			$("#paypalwithfee-feerow-table").remove();

			//rescue original price from data
			$('.cart-total .value').text(prestashop.cart.totals.total.value);
			$('#order-items > div.order-confirmation-table > table > tbody > tr.total-value.font-weight-bold > td:nth-child(2)').text(prestashop.cart.totals.total.value);
			$('#paypalwithfee-feerow').remove();
		}

	});
});