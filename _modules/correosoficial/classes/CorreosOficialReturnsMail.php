<?php

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/SendEmail.inc.php';
require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/CorreosOficialUtils.php';

class CorreosOficialReturnsMail
{
    private $customer_email;
    private $sender_email = '';
    private $label = '';
    private $cn23 = '';
    private $company = '';
    private $text_body = '';
    private $subject = "";
    private $shipping_number;
    private $pickup_date;
    private $sender_from_time;
    private $order_id;
    private $shop_name;
    private $return_code_cex;

    public $module;
    protected $context;

    public function __construct($returns_data, $module, $context)
    {
        $this->module = $module;
        $this->context = $context;

        $this->customer_email   = $returns_data['customer_email'];
        $this->sender_email = $returns_data['sender_email'];
        $this->label   = $returns_data['label'];
        $this->cn23    = $returns_data['cn23'];
        $this->company = $returns_data['company'];
        $this->shipping_number = $returns_data['shipping_number'];
        $this->pickup_date = $returns_data['pickup_date'];
        $this->sender_from_time = $returns_data['sender_from_time'];
        $this->order_id = $returns_data['order_id'];
        $this->shop_name = $returns_data['shop_name'];
        $this->return_code_cex = $returns_data['return_code'];

        $this->subject = "[".$this->shop_name."] ".$this->module->l('Return package information', 'correosoficialreturnsmail').
            " - ".$this->module->l('Order: ', 'correosoficialreturnsmail').$this->order_id;

        $this->text_body = $this->prepareEmail();
    }

    public function sendEmail()
    {
        global $co_signup_customers_from;

        $body = $this->getBody($this->text_body, $this->label, $this->cn23, $this->shipping_number);

        $sendMail = new SendMail($this->customer_email, mb_encode_mimeheader($this->subject), $body, $this->sender_email, null, 'multipart');
        // Email al cliente
        $result = $sendMail->SendEmail();

        CorreosOficialUtils::varDump("ENVIO DE CORREO", $result);
        return $result;
    }

    public function prepareEmail()
    {
        // Variables comunes del mensaje
        $recipient_return_hello    = $this->module->l('Hello', 'correosoficialreturnsmail');
        $recipient_return_thanks   = $this->module->l('Thank you', 'correosoficialreturnsmail');
        $recipient_return_bye      = $this->module->l('Sincerely', 'correosoficialreturnsmail');
        $recipient_return_footer   = $this->shop_name;

        // Recipient Return variables Correos
        $recipient_return_doc_cn23 = $this->module->l('CN23/CP71 Documentation', 'correosoficialreturnsmail');
        $recipient_return_text1    = $this->module->l('Attached is a label that you can print out and attach to the package. If you prefer, you can write down the package code', 'correosoficialreturnsmail');
        $recipient_return_text2    = $this->module->l('and provide it at your nearest post office or contact us to arrange collection. If in this email you receive the', 'correosoficialreturnsmail');
        $recipient_return_text3    = $this->module->l('must accompany the shipment printed and signed by you', 'correosoficialreturnsmail');

        // Recipient Return variables Cex
        $recipient_return_pickup_date = $this->pickup_date;
        $recipient_return_pickup_time = $this->sender_from_time;
        $recipient_return_shop_name  = $this->shop_name;
        $recipient_return_text1_cex  = $this->module->l('We would like to inform you that the', 'correosoficialreturnsmail');
        $recipient_return_text2_cex  = $this->module->l('from the', 'correosoficialreturnsmail');
        $recipient_return_text3_cex  = $this->module->l('Correos Express will proceed to carry out a collection requested by', 'correosoficialreturnsmail');
        $recipient_return_text4_cex  = $this->module->l('we kindly ask you, in order to avoid any unnecessary delays please have the shipment ready before the driver picks it up. Please find enclosed the label to be printed and attached to the package', 'correosoficialreturnsmail');
        $recipient_return_text5_cex  = $this->module->l('Once the shipment is made, you can track it using the following code:', 'correosoficialreturnsmail');
        $recipient_return_text6_cex  = $this->module->l('at', 'correosoficialreturnsmail');
        $recipient_return_text7_cex  = $this->module->l('Track your shipping - correosexpress.com', 'correosoficialreturnsmail');

        $recipient_return_recommendations   = $this->module->l('RECOMMENDATIONS', 'correosoficialreturnsmail');
        $recipient_return_recommendation_info = $this->module->l('In order to ensure that the service is performed correctly and that your shipments are not delayed, we recommend that you', 'correosoficialreturnsmail');
        $recipient_return_recommendation1   = $this->module->l('Have prepared any documentation accompanying the goods', 'correosoficialreturnsmail');
        $recipient_return_recommendation2   = $this->module->l('The goods must be perfectly closed and sealed before the indicated collection time', 'correosoficialreturnsmail');
        $recipient_return_recommendation3   = $this->module->l('On the outside of the box, in a visible place, attach the label included in this mailing', 'correosoficialreturnsmail');

        $this->context->smarty->assign('sender_email', $this->sender_email);
        $this->context->smarty->assign('shop_name', $this->shop_name);
        // Asignación de literales del cuerpo del email de devoluciones
        $this->context->smarty->assign('recipient_return_hello', $recipient_return_hello);
        $this->context->smarty->assign('recipient_return_doc_cn23', $recipient_return_doc_cn23);
        $this->context->smarty->assign('recipient_return_text1', $recipient_return_text1);
        $this->context->smarty->assign('recipient_return_text2', $recipient_return_text2);
        $this->context->smarty->assign('recipient_return_text3', $recipient_return_text3);
        $this->context->smarty->assign('recipient_return_thanks', $recipient_return_thanks);
        $this->context->smarty->assign('recipient_return_bye', $recipient_return_bye);
        $this->context->smarty->assign('recipient_return_footer', $recipient_return_footer);

        $this->context->smarty->assign('recipient_return_pickup_date', $recipient_return_pickup_date);
        $this->context->smarty->assign('recipient_return_pickup_time', $recipient_return_pickup_time);
        $this->context->smarty->assign('recipient_return_shop_name', $recipient_return_shop_name);
        $this->context->smarty->assign('recipient_return_text1_cex', $recipient_return_text1_cex);
        $this->context->smarty->assign('recipient_return_text2_cex', $recipient_return_text2_cex);
        $this->context->smarty->assign('recipient_return_text3_cex', $recipient_return_text3_cex);
        $this->context->smarty->assign('recipient_return_text4_cex', $recipient_return_text4_cex);
        $this->context->smarty->assign('recipient_return_text5_cex', $recipient_return_text5_cex);
        $this->context->smarty->assign('recipient_return_text6_cex', $recipient_return_text6_cex);
        $this->context->smarty->assign('recipient_return_text7_cex', $recipient_return_text7_cex);
        $this->context->smarty->assign('return_code_cex', $this->return_code_cex);
        $this->context->smarty->assign('recipient_return_recommendations', $recipient_return_recommendations);
        $this->context->smarty->assign('recipient_return_recommendation_info', $recipient_return_recommendation_info);
        $this->context->smarty->assign('recipient_return_recommendation1', $recipient_return_recommendation1);
        $this->context->smarty->assign('recipient_return_recommendation2', $recipient_return_recommendation2);
        $this->context->smarty->assign('recipient_return_recommendation3', $recipient_return_recommendation3);

        $this->context->smarty->assign('shipping_number', $this->shipping_number);

        $this->context->smarty->assign('company', $this->company);
        return $this->context->smarty->fetch(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/to_recipient.tpl');
    }

	public function concatenateArrayToString($array) {
		return implode('_', $array);
	}

    public function getBody($text_body, $label, $cn23, $shipping_number)
    {
		$content1 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_label.tpl');
		$content2 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_cn23.tpl');
		$content3 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_last.tpl');

		$label = $this->concatenateArrayToString($label);
        
        if ($cn23 == null) {
            $content = $content1.$content3;
            $body = sprintf($content, $text_body, $shipping_number,  $shipping_number, $label);
        } else {
            $content = $content1.$content2.$content3;
            $body = sprintf($content, $text_body, $shipping_number,  $shipping_number, $label, $shipping_number,  $shipping_number, $cn23);
        }
        
        return CorreosOficialUtils::replaceCharacterWithEntities($body);
    }
}
