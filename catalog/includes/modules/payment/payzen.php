<?php
/**
 * PayZen V2-Payment Module version 1.1.3 (revision 66007) for osCommerce 2.3.
 *
 * Copyright (C) 2014-2015 Lyra Network and contributors
 * Support contact : support@payzen.eu
 * Author link : http://www.lyra-network.com/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category  payment
 * @package   payzen
 * @author    Lyra Network <supportvad@lyra-network.com>
 * @copyright 2014-2015 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 * @version   1.1.3 (revision 66007)
*/

/* include BANKNAME API class */
require_once (DIR_FS_CATALOG . 'includes/classes/payzen_api.php');

/* include the admin configuration functions */
include_once (DIR_FS_CATALOG . 'admin/includes/functions/payzen_output.php');

/* load module language file */
include_once (DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen.php");

/**
 * Main class implementing PayZen payment module for OSC.
 */
class payzen {
	/**
	 * @var string
	 */
	var $code;
	/**
	 * @var string
	 */
	var $title;
	/**
	 * @var string
	 */
	var $description;
	/**
	 * @var boolean
	 */
	var $enabled;
	/**
	 * @var int
	 */
	var $sort_order;
	/**
	 * @var string
	 */
	var $form_action_url;
	/**
	 * @var int
	 */
	var $order_status;

	/**
	 * Class constructor
	 */
	function payzen() {
		global $order;

		// initialize code
		$this->code = 'payzen';

		// initialize title
		$this->title = MODULE_PAYMENT_PAYZEN_STD_TITLE;

		// initialize description
		$this->description  = '';
		$this->description .= '<b>' . MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION . '</b>';
		$this->description .= '<br/><br/>';

		$this->description .= '<table class="infoBoxContent">';
		$this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_DEVELOPED_BY . '</td><td><a href="http://www.lyra-network.com/" target="_blank"><b>Lyra network</b></a></td></tr>';
		$this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL . '</td><td><a href="mailto:support@payzen.eu"><b>support@payzen.eu</b></a></td></tr>';
		$this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION . '</td><td><b>1.1.3</b></td></tr>';
		$this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION . '</td><td><b>V2</b></td></tr>';
		$this->description .= '</table>';

		$this->description .= '<br/>';
		$this->description .= MODULE_PAYMENT_PAYZEN_CHECK_URL . '<b>' . HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process_payzen.php</b>';
		$this->description .= '<hr />';

		// initialize enabled
		$this->enabled = (MODULE_PAYMENT_PAYZEN_STATUS == '1');

		// initialize sort_order
		$this->sort_order = MODULE_PAYMENT_PAYZEN_SORT_ORDER;

		$this->form_action_url = MODULE_PAYMENT_PAYZEN_PLATFORM_URL;

		if ((int)MODULE_PAYMENT_PAYZEN_ORDER_STATUS > 0) {
			$this->order_status = MODULE_PAYMENT_PAYZEN_ORDER_STATUS;
		}

		// if there's an order to treat, start preliminary payment zone check
		if (is_object($order)) {
			$this->update_status();
		}
	}

	/**
	 * Payment zone and amount restriction checks
	 */
	function update_status() {
		global $order;

		if(!$this->enabled) {
			return;
		}

		// check customer zone
		if ((int)MODULE_PAYMENT_PAYZEN_ZONE > 0) {
			$flag = false;
			$check_query = tep_db_query('SELECT `zone_id` FROM `' . TABLE_ZONES_TO_GEO_ZONES . '`' .
										" WHERE `geo_zone_id` = '" . MODULE_PAYMENT_PAYZEN_ZONE . "'" .
										" AND `zone_country_id` = '" . $order->billing['country']['id'] . "'" .
										' ORDER BY `zone_id` ASC');
			while ($check = tep_db_fetch_array($check_query)) {
				if (($check['zone_id'] < 1) || ($check['zone_id'] == $order->billing['zone_id'])) {
					$flag = true;
					break;
				}
			}

			if (!$flag) {
				$this->enabled = false;
			}
		}

		// check amount restrictions
		if ((MODULE_PAYMENT_PAYZEN_AMOUNT_MIN != '' && $order->info['total'] < MODULE_PAYMENT_PAYZEN_AMOUNT_MIN)
				|| (MODULE_PAYMENT_PAYZEN_AMOUNT_MAX != '' && $order->info['total'] > MODULE_PAYMENT_PAYZEN_AMOUNT_MAX)) {
			$this->enabled = false;
		}

		// check currency
		$payzenApi = new PayzenApi('UTF-8'); // load PayZen payment API

		$defaultCurrency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
		if(!$payzenApi->findCurrencyByAlphaCode($order->info['currency']) && !$payzenApi->findCurrencyByAlphaCode($defaultCurrency)) {
			// currency is not supported, module is not available
			$this->enabled = false;
		}
	}

	/**
	 * JS checks : we let the platform do all the validation itself
	 * @return false
	 */
	function javascript_validation() {
		return false;
	}

	/**
	 * Parameters for what the payment option will look like in the list
	 * @return array
	 */
	function selection() {
		return array(
				'id' => $this->code,
				'module' => $this->title
		);
	}

	/**
	 * Server-side checks after payment selection : We let the platform do all the validation itself
	 * @return false
	 */
	function pre_confirmation_check() {
		return false;
	}

	/**
	 * Server-size checks before payment confirmation :  We let the platform do all the validation itself
	 * @return false
	 */
	function confirmation() {
		return false;
	}

	/**
	 * Prepare the form that will be sent to the payment gateway
	 * @return string
	 */
	function process_button() {
		global $order, $languages_id, $currencies, $customer_id;

		// load PayZen payment API
		$payzenApi = new PayzenApi('UTF-8');

		// admin configuration parameters
		$configParams = array(
				'site_id', 'key_test', 'key_prod', 'ctx_mode', 'platform_url', 'available_languages',
				'capture_delay', 'validation_mode', 'payment_cards', 'redirect_enabled',
				'redirect_success_timeout', 'redirect_success_message', 'redirect_error_timeout',
				'redirect_error_message', 'return_mode'
		);

		foreach ($configParams as $name) {
			$payzenApi->set($name, constant('MODULE_PAYMENT_PAYZEN_' . strtoupper($name)));
		}

		// get the shop language code
		$query = tep_db_query('SELECT `code` FROM `' . TABLE_LANGUAGES . "` WHERE `languages_id` = '$languages_id'");
		$langData = tep_db_fetch_array($query);
		$payzenLanguage = $payzenApi->isSupportedLanguage($langData['code']) ?
					strtolower($langData['code']) :
					MODULE_PAYMENT_PAYZEN_LANGUAGE;

		// get the currency to use
		$currencyValue = $order->info['currency_value'];
		$payzenCurrency = $payzenApi->findCurrencyByAlphaCode($order->info['currency']);
		if(!$payzenCurrency) {
			// currency is not supported, use the default shop currency
			$defaultCurrency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ?
								LANGUAGE_CURRENCY : DEFAULT_CURRENCY;

			$payzenCurrency = $payzenApi->findCurrencyByAlphaCode($defaultCurrency);
			$currencyValue = 1;
		}

		// calculate amount ...
		$total = tep_round($order->info['total'] * $currencyValue, $currencies->get_decimal_places($payzenCurrency->alpha3));

		// activate 3ds ?
		$threedsMpi = null;
		if(MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT != '' && $order->info['total'] < MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT) {
			$threedsMpi = '2';
		}

		// other parameters
		$data = array(
				// order info
				'amount' => $payzenCurrency->convertAmountToInteger($total),
				'order_id' => $this->_guess_order_id(),
				'contrib' => 'osCommerce2.3_1.1.3/' . tep_get_version() ,
				'order_info' => 'session_id=' . session_id(),

				// misc data
				'currency' => $payzenCurrency->num,
				'language' => $payzenLanguage,
				'threeds_mpi' => $threedsMpi,
				'url_return' => tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),

				// customer info
				'cust_id' => $customer_id,
				'cust_email' => $order->customer['email_address'],
				'cust_phone' => $order->customer['telephone'],
				'cust_first_name' => $order->billing['firstname'],
				'cust_last_name' => $order->billing['lastname'],
				'cust_address' => $order->billing['street_address'] . ' ' . $order->billing['suburb'],
				'cust_city' => $order->billing['city'],
				'cust_state' => $order->billing['state'],
				'cust_zip' => $order->billing['postcode'],
				'cust_country' => $order->billing['country']['iso_code_2']
		);

		// delivery data
		if($order->delivery != false) {
			$data['ship_to_first_name'] = $order->delivery['firstname'];
			$data['ship_to_last_name'] = $order->delivery['lastname'];
			$data['ship_to_street'] = $order->delivery['street_address'];
			$data['ship_to_street2'] = $order->delivery['suburb'];
			$data['ship_to_city'] = $order->delivery['city'];
			$data['ship_to_state'] = $order->delivery['state'];

			$countryCode = $order->delivery['country']['iso_code_2'];
			if($countryCode == 'FX') { // FX not recognized as a country code by PayPal
				$countryCode = 'FR';
			}
			$data['ship_to_country'] = $countryCode;

			//$data['ship_to_country'] = $order->delivery['country']['iso_code_2'];
			$data['ship_to_zip'] = $order->delivery['postcode'];
		}

		$payzenApi->setFromArray($data);

		return $payzenApi->getRequestFieldsHtml();
	}

	/**
	 * Verify client data after he returned from payment gateway
	 */
	function before_process() {
		global $order, $payzenResponse, $messageStack;

		$payzenResponse = new PayzenResponse(
				$_REQUEST,
				MODULE_PAYMENT_PAYZEN_CTX_MODE,
				MODULE_PAYMENT_PAYZEN_KEY_TEST,
				MODULE_PAYMENT_PAYZEN_KEY_PROD
		);
		$fromServer = $payzenResponse->get('hash');

		// Check authenticity
		if(!$payzenResponse->isAuthentified()) {
			if($fromServer) {
				die($payzenResponse->getOutputForGateway('auth_fail'));
			} else {
				$messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

				tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
				die();
			}
		}

		// messages to display on payment result page
		if(MODULE_PAYMENT_PAYZEN_CTX_MODE == 'TEST') {
			$messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO . '<a href="https://secure.payzen.eu/html/faq/prod" target="_blank">https://secure.payzen.eu/html/faq/prod</a>', 'success');
		}

		// act according to case
		if($payzenResponse->isAcceptedPayment()) {
			// successful payment

			if($this->_is_order_paid()) {
				if($fromServer) {
					die ($payzenResponse->getOutputForGateway('payment_ok_already_done'));
				} else {
					$this->_clear_session_vars();
					tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
					die();
				}
			} else {
				// update order payment data
				$order->info['cc_type'] = $payzenResponse->get('card_brand');
				$order->info['cc_number'] = $payzenResponse->get('card_number');
				$order->info['cc_expires'] = str_pad($payzenResponse->get('expiry_month'), 2, '0', STR_PAD_LEFT) . substr($payzenResponse->get('expiry_year'), 2);

				// let's borrow the cc_owner field to store transaction id
				$order->info['cc_owner'] = '-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transaction: ' . $payzenResponse->get('trans_id');

				// Let checkout_process.php finish the job
				return false;
			}

		} else {
			// payment process failed
			if($fromServer) {
				die($payzenResponse->getOutputForGateway('payment_ko'));
			} else {
				$messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR, 'error');
				tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
				die();
			}
		}
	}

	/**
	 * Post-processing after the order has been finalised
	 */
	function after_process() {
		global $cart, $payzenResponse, $messageStack;

		// this function is called only when payment was successful and the order is not registered yet

		$fromServer = $payzenResponse->get('hash');

		// reset cart to allow new checkout process
		$cart->reset(true);

		if($fromServer) {
			die ($payzenResponse->getOutputForGateway('payment_ok'));
		} else {
			$this->_clear_session_vars();

			// payment confirmed by client retun, show a warning if TEST mode
			if(MODULE_PAYMENT_PAYZEN_CTX_MODE == 'TEST') {
				$messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN . '<br />' . MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL, 'warning');
			}

			tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
			require(DIR_WS_INCLUDES . 'application_bottom.php');
			die();
		}
	}

	// unregister session variables used during checkout
	function _clear_session_vars() {
		tep_session_unregister('sendto');
		tep_session_unregister('billto');
		tep_session_unregister('shipping');
		tep_session_unregister('payment');
		tep_session_unregister('comments');
	}

	/**
	 * Return true / 1 if the module is installed
	 * @return unknown_type
	 */
	function check() {
		if (!isset($this->_check)) {
			$check_query = tep_db_query('SELECT `configuration_value` FROM `' . TABLE_CONFIGURATION . '`' .
										" WHERE `configuration_key` = 'MODULE_PAYMENT_PAYZEN_STATUS'");
			$this->_check = tep_db_num_rows($check_query);
		}
		return $this->_check;
	}


	/**
	 * Build and execute a query for the install() function
	 * Parameters have to be escaped before
	 *
	 * @param string $title
	 * @param string $key
	 * @param string $value
	 * @param string $description
	 * @param string $group_id
	 * @param string $sort_order
	 * @param string $date_added
	 * @param string $set_function
	 * @param string $use_function
	 * @return
	 */
	function _install_query($key, $value, $sort_order, $set_function=null, $use_function=null) {
		$prefix = 'MODULE_PAYMENT_PAYZEN_';

		$sql_data = array(
				'configuration_title' => constant($prefix . $key . '_TITLE'),
				'configuration_key' => $prefix . $key,
				'configuration_value' => $value,
				'configuration_description' => constant($prefix . $key . '_DESC'),
				'configuration_group_id' => '6',
				'sort_order' => $sort_order,
				'date_added' => 'now()'
		);

		if ($set_function) {
			$sql_data['set_function'] = $set_function;
		}

		if ($use_function) {
			$sql_data['use_function'] = $use_function;
		}

		tep_db_perform(TABLE_CONFIGURATION, $sql_data);
	}

	/**
	 * Module install (register admin-managed parameters in database)
	 */
	function install() {
		// Ex: _install_query($key, $value, $group_id, $sort_order, $set_function=null, $use_function=null)
		// osCommerce specific parameters
		$this->_install_query('STATUS', '1', 1, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
		$this->_install_query('SORT_ORDER', '0', 2);
		$this->_install_query('ZONE', '0', 3, 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title');

		// gateway access parameters
		$this->_install_query('SITE_ID', '12345678', 10);
		$this->_install_query('KEY_TEST', '1111111111111111', 11);
		$this->_install_query('KEY_PROD', '2222222222222222', 12);
		$this->_install_query('CTX_MODE', 'TEST', 13, "tep_cfg_select_option(array(\'TEST\', \'PRODUCTION\'),");
		$this->_install_query('PLATFORM_URL', 'https://secure.payzen.eu/vads-payment/', 14);

		$this->_install_query('LANGUAGE', 'fr', 21, 'payzen_cfg_draw_pull_down_langs(', 'payzen_get_lang_title');
		$this->_install_query('AVAILABLE_LANGUAGES', '', 22, 'payzen_cfg_draw_pull_down_multi_langs(', 'payzen_get_multi_lang_title');
		$this->_install_query('CAPTURE_DELAY', '', 23);
		$this->_install_query('VALIDATION_MODE', '', 24, 'payzen_cfg_draw_pull_down_validation_modes(', 'payzen_get_validation_mode_title');
		$this->_install_query('PAYMENT_CARDS', '', 25, 'payzen_cfg_draw_pull_down_cards(', 'payzen_get_card_title');
		$this->_install_query('3DS_MIN_AMOUNT', '', 26);

		// amount restriction
		$this->_install_query('AMOUNT_MIN', '', 30);
		$this->_install_query('AMOUNT_MAX', '', 31);

		// gateway return parameters
		$this->_install_query('REDIRECT_ENABLED', '0', 40, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
		$this->_install_query('REDIRECT_SUCCESS_TIMEOUT', '5', 41);
		$this->_install_query('REDIRECT_SUCCESS_MESSAGE', 'Redirection vers la boutique dans quelques instants...', 42);
		$this->_install_query('REDIRECT_ERROR_TIMEOUT', '5', 43);
		$this->_install_query('REDIRECT_ERROR_MESSAGE', 'Redirection vers la boutique dans quelques instants...', 44);
		$this->_install_query('RETURN_MODE', 'GET', 45, "tep_cfg_select_option(array(\'GET\', \'POST\'), ");
		$this->_install_query('ORDER_STATUS', '0', 48, 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name');
	}

	/**
	 * Module deletion
	 */
	function remove() {
		$keys = $this->keys();

		foreach($keys as $key) {
			tep_db_query('DELETE FROM `' . TABLE_CONFIGURATION . "` WHERE `configuration_key` = '$key'");
		}
	}

	/**
	 * Returns the names of module's parameters
	 * @return array[int]string
	 */
	function keys() {
		return array(
				'MODULE_PAYMENT_PAYZEN_STATUS',
				'MODULE_PAYMENT_PAYZEN_SORT_ORDER',
				'MODULE_PAYMENT_PAYZEN_ZONE',

				'MODULE_PAYMENT_PAYZEN_SITE_ID',
				'MODULE_PAYMENT_PAYZEN_KEY_TEST',
				'MODULE_PAYMENT_PAYZEN_KEY_PROD',
				'MODULE_PAYMENT_PAYZEN_CTX_MODE',
				'MODULE_PAYMENT_PAYZEN_PLATFORM_URL',

				'MODULE_PAYMENT_PAYZEN_LANGUAGE',
				'MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES',
				'MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY',
				'MODULE_PAYMENT_PAYZEN_VALIDATION_MODE',
				'MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS',
				'MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT',

				'MODULE_PAYMENT_PAYZEN_AMOUNT_MIN',
				'MODULE_PAYMENT_PAYZEN_AMOUNT_MAX',

				'MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED',
				'MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT',
				'MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE',
				'MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT',
				'MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE',
				'MODULE_PAYMENT_PAYZEN_RETURN_MODE',
				'MODULE_PAYMENT_PAYZEN_ORDER_STATUS'
		);
	}

	/**
	 * Try to guess what will be the order's id when osCommerce will register it at the end of the payment process.
	 * This is only used to set order_id in the request to the payment gateway. It might be inconsistent with the
	 * final osCommerce order id (in cases like two clients going to the payment gateway at the same time...)
	 *
	 * @return int
	 */
	function _guess_order_id() {
		$query = tep_db_query('SELECT MAX(`orders_id`) AS `order_id` FROM `' . TABLE_ORDERS . '`');

		if(tep_db_num_rows($query) == 0) {
			return 0;
		} else {
			$result = tep_db_fetch_array($query);
			return $result['order_id'] + 1;
		}
	}

	/**
	 * Test if order corresponding to entered trans_id is already saved.
	 *
	 * @return boolean true if order already saved
	 */
	function _is_order_paid() {
		global $payzenResponse;

		$orderId = $payzenResponse->get('order_id');
		$customerId = $payzenResponse->get('cust_id');
		$transId = $payzenResponse->get('trans_id');

		$query = tep_db_query('SELECT * FROM `' . TABLE_ORDERS . '`' .
				" WHERE orders_id >= $orderId" .
				" AND customers_id = $customerId" .
				" AND cc_owner LIKE '%Transaction: $transId'");

		return tep_db_num_rows($query) > 0;
	}
}
?>