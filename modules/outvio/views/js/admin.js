/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Outvio OU
 *  @copyright 2017-2018 Outvio OU
 *  @license   Outvio OU
 */
 
$(function(){
	$('#configuration_form').on('submit', function(){
		$(".selected_api_options option").prop('selected', true);
	});

	$(".selected_api_options option").each(function(){
		var value = $(this).attr('value');
		$('.ps_api_options option[value="'+value+'"]').prop('selected', false).hide();
	});
});

function selectApiOptions(key, section) {
	var option = $('#'+key+'_left option:selected');
	var value = option.attr('value');
	option.hide().appendTo('#'+key+'_right').show();
	$("."+section+' option[value="'+value+'"]').prop('selected', false).hide();
}

function removeApiOptions(key, section) {
	var option = $('#'+key+'_right option:selected');
	var value = option.attr('value');
	option.remove();//.appendTo('#'+key+'_left');
	$("."+section+' option[value="'+value+'"]').prop('selected', false).show();
}
