/**
 * FMM AdvSEO
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *  @author    FME Modules
 *  @copyright 2024 fmemodules.com All right reserved
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @category  FMM Modules
 *  @package   Customfields
 */

function selectAll() {
  $('#advseo_pages_excl .gsitemap_metas').prop('checked', true);
}

function UnSelectAll() {
  $('#advseo_pages_excl .gsitemap_metas').prop('checked', false);
}

function appendThisTagGraphs(el) {
		var tag_product = $(el).attr("title");
    
    if ($(el).parent().parent().find("textarea").length > 0) {
      var input_current_value = $(el).parent().parent().find("textarea").val();
      $(el).parent().parent().find("textarea").val(input_current_value+tag_product);
    }
    else {
      var input_current_value = $(el).parent().parent().find("input").val();
      $(el).parent().parent().find("input").val(input_current_value+tag_product);
    }
	}