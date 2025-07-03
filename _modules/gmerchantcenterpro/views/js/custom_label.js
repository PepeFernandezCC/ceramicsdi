/*
*
* Google merchant center Pro
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*/
// declare the custom label js object
var GmcProCustomLabel = function (sName) {

	// set name
	this.name = sName;

	// set name
	this.oldVersion = false;

	// set translated js msgs
	this.msgs = {};

	// stock error array
	this.aError = [];
	// set this in obj context
	var oThis = this;

	/***
	 * initForm() method manage the init form with the good values
	 *
	 * @param string sSelectElem
	 * @param string sLabel
	 */
	this.initForm = function (sSelectElem, sLabel) {
		// Check if sLabel is defined, if not, use a default value
		sLabel = (typeof sLabel !== 'undefined') ? sLabel : 'Filter:';

		// initialize the list of elt to show and hide
		var aShow = [];
		var aHide = [];
		var sGmcpCustomLabelType = ''; // Define this variable to avoid reference errors

		// manage each case from configuration to prepare the form
		if ($("#" + sSelectElem).val() == "custom_label") {
			aShow.push('#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_manual_info');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_last_order,#bt_cl_configure_attribute,#bt_cl_configure_new_products,#bt_cl_configure_best_sales,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'Manual custom label';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_categorie") {
			$('#bt_cat_tree').addClass('col-xs-3');
			$('#bt_cat_tree').removeClass('col-xs-12');

			aShow.push('#bt_cl_configure_cat,#bt_cl_configure_cat_header,#gmcp_cat_behavior')
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_last_order,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_new_products,#bt_cl_configure_best_sales,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'categories (dynamic mode) ';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_features_list") {
			aShow.push('#bt_cl_configure_attribute');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_product,#bt_cl_configure_product_header,#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_best_sales,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_new_products,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'features (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_new_product") {
			aShow.push('#bt_cl_configure_new_products,#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_best_sales,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'new products (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_best_sale") {
			aShow.push('#bt_cl_configure_best_sales');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_new_products,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'best sales (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_price_range") {
			aShow.push('#bt_cl_configure_price_range');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_best_sales,#bt_cl_configure_new_products,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'price range sales (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_last_order") {
			aShow.push('#bt_cl_configure_last_order');
			aHide.push('#bt_cl_configure_not_sell,#bt_cl_configure_cat,#bt_cl_configure_price_range,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_best_sales,#bt_cl_configure_new_products,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'Last order (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_not_sell") {
			aShow.push('#bt_cl_configure_not_sell');
			aHide.push('#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_price_range,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_best_sales,#bt_cl_configure_new_products,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'Not sell (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if ($("#" + sSelectElem).val() == "dynamic_promotion") {
			aShow.push('');
			aHide.push('#bt_cl_configure_last_order,#bt_cl_configure_cat,#bt_cl_configure_price_range,#bt_cl_configure_cat_header,#bt_cl_configure_brand,#bt_cl_configure_brand_header,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#bt_cl_configure_attribute,#bt_cl_configure_best_sales,#bt_cl_configure_new_products,#bt_cl_configure_supplier,#bt_cl_configure_supplier_header,#gmcp_infobox_dynamique_cat,#gmcp_cat_behavior,#gmcp_manual_info,#bt_cl_configure_product_header,#bt_cl_configure_product,#bt_cl_configure_price_range');

			// generate the h3 title above the filter
			sGmcpCustomLabelType = 'Last order (dynamic mode)';
			if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
				oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
			}
		}

		if (typeof oGmcPro !== 'undefined') {
			if (typeof oGmcPro.initHide === 'function') {
				oGmcPro.initHide(aHide);
			}
			if (typeof oGmcPro.initShow === 'function') {
				oGmcPro.initShow(aShow);
			}
		}
	};

	/***
	 * displayClElement() method manage the add rules button disable
	 *
	 * @param string sSelectElem
	 * @param string sLabel
	 */
	this.displayClElement = function (sSelectElem, sLabel) {
		$("#" + sSelectElem).change(function () {
			// Define variables that were previously undefined
			var sGmcpCustomLabelType = '';
			var sCustomLabelType = '';

			if ($("#" + sSelectElem).val() == "custom_label") {
				$('#bt_cl_configure_cat').slideDown();
				$('#bt_cl_configure_cat_header').slideDown();
				$('#bt_cl_configure_brand').slideDown();
				$('#bt_cl_configure_brand_header').slideDown();
				$('#bt_cl_configure_supplier').slideDown();
				$('#bt_cl_configure_supplier_header').slideDown();
				$('#bt_cl_configure_price_range').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideDown();
				$('#bt_cl_configure_product_header').slideDown();
				$('#gmcp_manual_info').slideDown();
				$('#bt_label-name').val('');
				$('#bt_label-name').removeAttr('readonly');
				$('#bt_label-name').val('');
				$('#bt_cat_tree').addClass('col-xs-12');
				$('#bt_cat_tree').removeClass('col-xs-3');
				$('#bt_cat_header_tree').removeClass('col-xs-6');
				$('#bt_cat_header_title').removeClass('col-xs-2');
				$('#bt_cat_header_title').addClass('col-xs-6');
				$('#bt_cat_header_button').removeClass('col-xs-2');
				$('#bt_cat_header_button').addClass('col-xs-6');
				$('#bt_cat_header_button').removeClass('pull-left');
				$('#bt_cat_header_button').addClass('pull-right');
				$('#bt_cat_row').css('display', 'block');
				$('#bt_cl_configure_last_order').slideUp();

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'manual configuration';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}
			//dynamic categories
			if ($("#" + sSelectElem).val() == "dynamic_categorie") {
				$('#bt_cl_configure_cat_header').slideDown();
				$('#bt_cl_configure_cat').slideDown();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideDown();
				$('#gmcp_cat_behavior').slideDown();
				$('#gmcp_manual_info').slideUp();
				$('#bt_label-name').val('');
				$('#bt_label-name').removeAttr('readonly');
				$('#bt_cat_tree').removeClass('col-xs-12');
				$('#bt_cat_tree').addClass('col-xs-3');
				$('#bt_cat_header_title').addClass('col-xs-2');
				$('#bt_cat_header_title').removeClass('col-xs-6');
				$('#bt_cat_header_button').addClass('col-xs-2');
				$('#bt_cat_header_button').removeClass('col-xs-6');
				$('#bt_cat_header_button').addClass('pull-left');
				$('#bt_cat_header_button').removeClass('pull-right');
				$('#bt_cat_row').css('display', 'block');
				$('#bt_cl_configure_last_order').slideUp();
				$('#bt_cl_configure_not_sell').slideUp();

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'categories (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}
			//dynamic attributes
			if ($("#" + sSelectElem).val() == "dynamic_features_list") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideDown();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');
				$('#bt_cl_configure_last_order').slideUp();
				$('#bt_cl_configure_not_sell').slideUp();

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'features (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}
			//dynamic new products
			if ($("#" + sSelectElem).val() == "dynamic_new_product") {
				$('#bt_cl_configure_cat').slideDown();
				$('#bt_cl_configure_cat_header').slideDown();
				$('#bt_cl_configure_brand').slideDown();
				$('#bt_cl_configure_brand_header').slideDown();
				$('#bt_cl_configure_supplier').slideDown();
				$('#bt_cl_configure_supplier_header').slideDown();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideDown();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_tree').addClass('col-xs-12');
				$('#bt_cat_tree').removeClass('col-xs-3');
				$('#bt_cat_header_title').removeClass('col-xs-2');
				$('#bt_cat_header_title').addClass('col-xs-6');
				$('#bt_cat_header_button').removeClass('col-xs-2');
				$('#bt_cat_header_button').addClass('col-xs-6');
				$('#bt_cat_header_button').removeClass('pull-left');
				$('#bt_cat_header_button').addClass('pull-right');
				$('#bt_cat_row').css('display', 'block');
				$('#bt_cl_configure_last_order').slideUp();
				$('#bt_cl_configure_not_sell').slideUp();

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'new products (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}
			//dynamic best sales
			if ($("#" + sSelectElem).val() == "dynamic_best_sale") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideDown();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');
				$('#bt_cl_configure_last_order').slideUp();
				$('#bt_cl_configure_not_sell').slideUp();

				// Define sCustomLabelType with the same value as sGmcpCustomLabelType for consistency
				sCustomLabelType = 'Best sales';

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'best sales (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}

			//dynamic price range
			if ($("#" + sSelectElem).val() == "dynamic_price_range") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideDown();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#bt_cl_configure_last_order').slideUp();
				$('#gmcp_manual_info').slideUp();
				$('#bt_cl_configure_not_sell').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');

				// Define sCustomLabelType with the same value as sGmcpCustomLabelType for consistency
				sCustomLabelType = 'Price Range';

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'price range (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}

			//dynamic last order
			if ($("#" + sSelectElem).val() == "dynamic_last_order") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				$('#bt_cl_configure_last_order').slideDown();
				$('#bt_cl_configure_not_sell').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');

				// Define sCustomLabelType with the same value as sGmcpCustomLabelType for consistency
				sCustomLabelType = 'Last order';

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'last order (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}

			//dynamic promotion
			if ($("#" + sSelectElem).val() == "dynamic_promotion") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				$('#bt_cl_configure_last_order').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');

				// Define sCustomLabelType with the same value as sGmcpCustomLabelType for consistency
				sCustomLabelType = 'Promotion';

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'promotion (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}

			//dynamic not sell
			if ($("#" + sSelectElem).val() == "dynamic_not_sell") {
				$('#bt_cl_configure_cat').slideUp();
				$('#bt_cl_configure_cat_header').slideUp();
				$('#bt_cl_configure_brand').slideUp();
				$('#bt_cl_configure_brand_header').slideUp();
				$('#bt_cl_configure_supplier').slideUp();
				$('#bt_cl_configure_supplier_header').slideUp();
				$('#bt_cl_configure_attribute').slideUp();
				$('#bt_cl_configure_new_products').slideUp();
				$('#bt_cl_configure_best_sales').slideUp();
				$('#bt_cl_configure_product').slideUp();
				$('#bt_cl_configure_product_header').slideUp();
				$('#bt_cl_configure_price_range').slideUp();
				$('#gmcp_infobox_dynamique_cat').slideUp();
				$('#gmcp_manual_info').slideUp();
				$('#bt_cl_configure_not_sell').slideDown();
				$('#bt_cl_configure_last_order').slideUp();
				// $('#bt_label-name').attr('readonly', 'true');
				$('#bt_label-name').val('');
				$('#bt_cat_row').css('display', 'none');

				// Define sCustomLabelType with the same value as sGmcpCustomLabelType for consistency
				sCustomLabelType = 'Not sell';

				// generate the h3 title above the filter
				sGmcpCustomLabelType = 'not sell (dynamic mode)';
				if (typeof oGmcPro !== 'undefined' && typeof oGmcPro.generateFilterTitle === 'function') {
					oGmcPro.generateFilterTitle('#gcmp_filter_name', sLabel, sGmcpCustomLabelType);
				}
			}
		});
	};

	/***
	 * generateClValueFeature() method manage the title display for the feature CL
	 *
	 * @param string sSelectElem
	 */
	this.generateClValueFeature = function (sSelectElem) {
		// for new product dynamic text
		$('#' + sSelectElem).change(function () {
			sFeature = $('#dynamic_features_list option:selected').text();
			$('#bt_label-name').val(sFeature);
		});
	};

	/***
	 * generateClValueNewProduct() method manage the title display for the new product CL
	 *
	 * @param string sSelectElem
	 */
	this.generateClValueNewProduct = function (sSelectElem) {
		// for new product dynamic text
		$("#" + sSelectElem).change(function () {
			if ($("#bt_cl-type").val() == "dynamic_new_product") {
				var sDate = $("#bt_cl_dyn_date_start").val();
				$('#bt_label-name').val('New product from date : ' + ' ' + sDate);
			}
		});
	};

	/***
	 * generateClValueBestSaleUnit() method manage the title display when an option for best sale is selected
	 *
	 * @param string sSelectElem
	 * @param string sCurrency
	 */
	this.generateClValueBestSaleUnit = function (sSelectElem, sCurrency) {
		// for new product dynamic text
		$("#" + sSelectElem).change(function () {
			var sType = $("#dynamic_best_sales_unit").val();
			var sTypeText = $("#dynamic_best_sales_unit option:selected").text();
			var sCurrencySign = sCurrency;
			var sCustomLabelType = 'Best sales'; // Define this variable to avoid reference errors

			if (sType == 'unit') {
				$('#bt_label-name').val(sCustomLabelType + ' > ');
			}
			else {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + ' ' + sCurrencySign);
			}
		});
	};

	/***
	 * generateClValueBestSaleAmount()  method manage the title display when an option for best sale is selected
	 *
	 * @param string sSelectElem
	 * @param string sCurrency
	 */
	this.generateClValueBestSaleAmount = function (sSelectElem, sCurrency) {
		// for new product dynamic text
		$("#" + sSelectElem).focusout(function () {
			var sAmountUnit = $("#bt_cl_dyn_amount").val();
			var sType = $("#dynamic_best_sales_unit").val();
			var sTypeText = $("#dynamic_best_sales_unit option:selected").text();
			var sCurrencySign = sCurrency;
			var sCustomLabelType = 'Best sales'; // Define this variable to avoid reference errors

			if (sType == 'unit') {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sTypeText);
			}
			else {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sCurrencySign);
			}
		});
	};

	/***
	 * generateClValueBestSaleDate()  method manage the title display when an option for best sale is selected
	 *
	 * @param string sSelectElemDateStart
	 * @param string sSelectElemDateEnd
	 * @param string sCurrency
	 */
	this.generateClValueBestSaleDate = function (sSelectElemDateStart, sSelectElemDateEnd, sCurrency) {
		var sFromText = 'From';
		var sToText = 'To';
		var sCurrencySign = sCurrency;
		var sCustomLabelType = 'Best sales'; // Define this variable to avoid reference errors
		var sAmountUnit = ''; // Define this variable to avoid reference errors
		var sDateStart = ''; // Define this variable to avoid reference errors

		$("#" + sSelectElemDateStart).change(function () {
			sDateStart = $("#bt_dyn_best_sale_start").val();
			var sType = $("#dynamic_best_sales_unit").val();
			var sTypeText = $("#dynamic_best_sales_unit option:selected").text();
			sAmountUnit = $("#bt_cl_dyn_amount").val(); // Get the amount value

			if (sType == 'unit') {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sTypeText + ' ' + sFromText + ' ' + sDateStart);
			}
			else {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sCurrencySign + ' ' + sFromText + ' ' + sDateStart);
			}
		});

		$("#" + sSelectElemDateEnd).change(function () {
			var sDateEnd = $("#bt_dyn_best_sale_end").val();
			var sType = $("#dynamic_best_sales_unit").val();
			var sTypeText = $("#dynamic_best_sales_unit option:selected").text();
			sAmountUnit = $("#bt_cl_dyn_amount").val(); // Get the amount value
			sDateStart = $("#bt_dyn_best_sale_start").val(); // Get the start date value

			if (sType == 'unit') {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sTypeText + ' ' + sFromText + ' ' + sDateStart + ' ' + sToText + ' ' + sDateEnd);
			}
			else {
				$('#bt_label-name').val(sCustomLabelType + ' > ' + sAmountUnit + ' ' + sCurrencySign + ' ' + sFromText + ' ' + sDateStart + ' ' + sToText + ' ' + sDateEnd);
			}
		});
	};

	/***
	 * generateClValueBestSaleDate()  method manage the title display when an option for best sale is selected
	 *
	 * @param string sElement
	 */
	this.generateClValueBestSaleIcon = function (sElement) {

		//manage the help text a the right of the field unit
		$("#" + sElement).change(function () {
			if ($("#dynamic_best_sales_unit").val() == 'unit') {
				$("#cl_dyn_unit_help").html("<i class='icon icon-shopping-cart'/>");
			}
			else {
				$("#cl_dyn_unit_help").html("<i class='icon icon-euro'/>");
			}
		});
	};


	/***
	 * generateClValueBestPriceRange()  method manage the title display when an option for the price range is added
	 *
	 * @param string sPriceFrom
	 * @param string sPriceTo
	 * @param string sCurrency
	 */
	this.generateClValueBestPriceRange = function (sPriceFrom, sPriceTo, sCurrency) {
		var sFromText = 'Min price  ';
		var sToText = 'Max Price';
		var sCurrencySign = sCurrency;
		var sCustomLabelType = 'Price Range'; // Define this variable to avoid reference errors

		$("#" + sPriceFrom).focusout(function () {
			var sPriceFromVal = $('#' + sPriceFrom).val();
			$('#bt_label-name').val(sCustomLabelType + ' ' + sPriceFromVal + ' ' + sCurrency);
		});

		$("#" + sPriceTo).focusout(function () {
			var sPriceFromVal = $('#' + sPriceFrom).val();
			var sPriceToVal = $('#' + sPriceTo).val();
			$('#bt_label-name').val(sCustomLabelType + ' ' + sPriceFromVal + ' ' + sCurrency + ' to ' + sPriceToVal + ' ' + sCurrency);
		});
	};
};