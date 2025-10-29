{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
<li class="nav-item active">
  <a class="nav-link active show" id="historyTab" data-toggle="tab" href="#paypalwithfee" role="tab" aria-controls="historyTabContent" aria-expanded="true" aria-selected="false">
    <span class="material-icons">payments</span>
      {l s='Paypal with fee' mod='paypalwithfee'}{*{l s='Paypal with fee' d='Modules.Paypalwithfee.Admin'}*} <span class="badge rounded badge-primary">1</span>
  </a>
</li>
<script type="text/javascript">
$(document).ready(function(){
    $('#myTab li').removeClass('active');
    $('#myTab li').first().addClass('active');
});
</script>
