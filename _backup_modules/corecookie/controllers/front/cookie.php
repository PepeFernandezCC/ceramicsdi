<?php
	/**
	 * NOTICE OF LICENSE
	 *
	 * This source file is subject to the Commercial License and is not open source.
	 * Each license that you purchased is only available for 1 website only.
	 * You can't distribute, modify or sell this code.
	 * If you want to use this file on more websites, you need to purchase additional licenses.
	 *
	 * DISCLAIMER
	 *
	 * Do not edit or add to this file.
	 * If you need help please contact <attechteams@gmail.com>
	 *
	 * @author    Alpha Tech <attechteams@gmail.com>
	 * @copyright 2022 Alpha Tech
	 * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
	 */
	
	class CorecookieCookieModuleFrontController extends ModuleFrontController {
		public $auth = false;
		
		public function __construct() {
			$this->context = Context::getContext();
			$this->display_column_left = false;
			$this->display_column_right = false;
			parent::__construct();
		}
		
		public function initContent() {
			parent::initContent();
			if (Tools::isSubmit('_a') && Tools::isSubmit('_r')) {
				$action = Tools::getValue('_a');
				if ($action == 'save_policy_accepted') {
					die($this->savePolicyAccepted());
				}
				die(Tools::jsonEncode([
					'success' => false,
					'msg' => $this->module->l('An error occurred, please visit again later!', 'personalData')
				]));
			}
		}
		
		public function savePolicyAccepted() {
			$policy_acceptance = new CoreCookiePolicyAcceptance();
			$customer = new Customer(Tools::getValue('id_customer'));
			if (!$customer->id) {
				return json_encode([
					'success' => false,
					'msg' => $this->module->l('Customer is invalid', 'personalData')
				]);
			}
			
			$policy_acceptance->email = $customer->email;
			$policy_acceptance->customer_id = $customer->id;
			$policy_acceptance->accepted_page = Tools::getValue('accepted_page');
			$policy_acceptance->given_consent = json_encode([
				'analytics' => Tools::getValue('given_consent_analytics'),
				'marketing' => Tools::getValue('given_consent_marketing'),
				'functionality' => Tools::getValue('given_consent_functionality'),
			]);
			$policy_acceptance->ip_address = Tools::getRemoteAddr();
			$policy_acceptance->interaction = Tools::getValue('interaction');
			$policy_acceptance->since = time();
			if (!$policy_acceptance->save()) {
				return json_encode([
					'success' => false,
					'msg' => $this->module->l('Cannot save policy acceptance', 'personalData')
				]);
			}
			return json_encode([
				'success' => true,
				'msg' => $this->module->l('Save policy acceptance successfully', 'personalData')
			]);
		}
	}
?>