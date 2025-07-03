/**
* 2007-2025 PrestaShop
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
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function(){
	
	$(document).on('click', '.module-tab-section-top', function(e){

		if ($(this).hasClass('tab-closed')){
			$(this).removeClass('tab-closed');
		}else{
			$(this).addClass('tab-closed');
		}

		$(this).parent().find('.module-tab-section-content').slideToggle();
	});

	$(document).on('click', '._tab_select', function(e){
		selected_tab = $(this).attr('open-tab');

		$('._tab_select').each(function(key, value){

			if ($(value).attr('open-tab') === selected_tab){
				
				$(value).addClass('module-tab-select-active');

			}else{

				$(value).removeClass('module-tab-select-active');

			}
		});

		$('.module-tab-container').each(function(key, value){
			if ($(value).attr('id') != selected_tab){

				$(value).fadeOut('fast');
				$(value).removeClass('module-tab-active');

			}else{

				$(value).fadeIn('fast');
				$(value).addClass('module-tab-active');

			}
		});
	});

	$(document).on('mouseover mouseout', '.module-input-circle', function(e){

		var hover = $(this).is(':hover');

		if (hover && $('.module-info-box').length == 0) {

			var msg = $(this).attr('data-text');

			var msgBox = $('<div>').addClass('module-info-box').text(msg);
		    msgBox.css( 'position', 'absolute' );
		    msgBox.css( 'top', e.pageY );
		    msgBox.css( 'left', e.pageX + 20 );
			$(this).append(msgBox);

		}else{
			$('.module-info-box').fadeOut('fast', function(){
				$('.module-info-box').remove();
			});
		}
	});

});