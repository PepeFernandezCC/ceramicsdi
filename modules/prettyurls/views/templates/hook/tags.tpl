{*
* seometamanager
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
*  @author    FME Modules
*  @copyright 2024 fmemodules All right reserved
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<p style="clear:both"><a class="fmm_dropme" onclick="appendThisTag(this);" title="productname">Product Name</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productcategory">Product Category</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productdesc">Product Description</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productshortdesc">Product Short Description</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productreference">Product reference</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productmanufacturer">Product Manufacturer</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productfeature">Product Features</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productretailpricewithtax">Product retail price with tax</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productretailpricewithouttax">Product retail price withOut tax</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productspecificpricewithtax">Product specific price with tax</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productspecificpricewithouttax">Product specific price withOut tax</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="productreduction">Product reduction</a><br>
<a class="fmm_dropme" onclick="appendThisTag(this);" title="shoptitle">Shop Title</a></p>

<script type="text/javascript">
var lang_count = "{$lang_count|escape:'htmlall':'UTF-8'}";
lang_count = parseInt(lang_count);
{literal}
  function appendThisTag(el) {
    var tag_product = $(el).attr("title");
    if (lang_count > 1) {
      var input_current_value = $(el).parent().parent().find("div.translatable-field:visible input").val();
      $(el).parent().parent().find("div.translatable-field:visible input").val(input_current_value+tag_product);
    }
    else {
      var input_current_value = $(el).parent().parent().find('input[type="text"]').val();
      $(el).parent().parent().find('input[type="text"]').val(input_current_value+tag_product);
    }
  }
</script>
<style type="text/css">
.fmm_dropme { cursor: pointer; font-size: 11px;}
</style>
{/literal}