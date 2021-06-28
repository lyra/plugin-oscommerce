<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for osCommerce. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra-network.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

// Include gateway API class.
require_once(DIR_FS_CATALOG . 'includes/classes/payzen_api.php');

if (defined('DIR_FS_ADMIN')) {
    // Include the admin configuration functions.
    require_once(DIR_FS_ADMIN . 'includes/functions/payzen_output.php');
}

global $language, $payzen_plugin_features;

$payzen_plugin_features = array(
    'qualif' => false,
    'prodfaq' => true,
    'restrictmulti' => false,
    'shatwo' => true,

    'multi' => true
);

// Load module language file.
require_once(DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen.php");

/**
 * Main class implementing payment module for osCommerce.
 */
class payzen
{
    private static $GATEWAY_CODE = 'PayZen';
    private static $GATEWAY_NAME = 'PayZen';
    private static $BACKOFFICE_NAME = 'PayZen';
    private static $GATEWAY_URL = 'https://secure.payzen.eu/vads-payment/';
    private static $SITE_ID = '12345678';
    private static $KEY_TEST = '1111111111111111';
    private static $KEY_PROD = '2222222222222222';
    private static $CTX_MODE = 'TEST';
    private static $SIGN_ALGO = 'SHA-256';
    private static $LANGUAGE = 'fr';

    private static $CMS_IDENTIFIER = 'osCommerce_2.3.x';
    private static $SUPPORT_EMAIL = 'support@payzen.eu';
    private static $PLUGIN_VERSION = '1.3.3';
    private static $GATEWAY_VERSION = 'V2';

    var $prefix = 'MODULE_PAYMENT_PAYZEN_';

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
     * Class constructor.
     */
    function payzen()
    {
        global $order;

        // Initialize code.
        $this->code = 'payzen';

        // Initialize title.
        $this->title = MODULE_PAYMENT_PAYZEN_STD_TITLE;

        // Initialize description.
        $this->description  = '';
        $this->description .= '<b>' . MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION . '</b>';
        $this->description .= '<br/><br/>';

        $this->description .= '<table class="infoBoxContent">';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_DEVELOPED_BY . '</td><td><a href="http://www.lyra-network.com/" target="_blank"><b>Lyra network</b></a></td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL . '</td><td>' . PayzenApi::formatSupportEmails(self::$SUPPORT_EMAIL) . '</td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION . '</td><td><b>' . self::$PLUGIN_VERSION . '</b></td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION . '</td><td><b>' . self::$GATEWAY_VERSION . '</b></td></tr>';

        $this->description .= '<tr style="height: 20px;" colspan="2"><td></td></tr>'; // Separator.
        $this->description .= '<tr>
                                   <td style="text-align: right; vertical-align: top;">' . MODULE_PAYMENT_PAYZEN_IPN_URL_TITLE . '</td>
                                   <td>
                                       <b style="word-break: break-word;">' . HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process_payzen.php</b><br />' .
                                       MODULE_PAYMENT_PAYZEN_IPN_URL_DESC . '
                                   </td>
                               </tr>';

        $this->description .= '</table>';

        $this->description .= '<hr />';

        // Initialize enabled.
        $this->enabled = defined($this->prefix . 'STATUS') && (constant($this->prefix . 'STATUS') == '1');

        // Initialize sort_order.
        $this->sort_order = defined($this->prefix . 'SORT_ORDER') ? constant($this->prefix . 'SORT_ORDER') : 0;

        $this->form_action_url = defined($this->prefix . 'PLATFORM_URL') ? constant($this->prefix . 'PLATFORM_URL') : '';

        if (defined($this->prefix . 'ORDER_STATUS') && (constant($this->prefix . 'ORDER_STATUS') > 0)) {
            $this->order_status = constant($this->prefix . 'ORDER_STATUS');
        }

        // If there's an order to treat, start preliminary payment zone check.
        if (is_object($order)) {
            $this->update_status();
        }
    }

    /**
     * Payment zone and amount restriction checks.
     */
    function update_status()
    {
        global $order;

        if (! $this->enabled) {
            return;
        }

        // Check customer zone.
        if ((int) constant($this->prefix . 'ZONE') > 0) {
            $flag = false;
            $check_query = tep_db_query('SELECT `zone_id` FROM `' . TABLE_ZONES_TO_GEO_ZONES . '`' .
                                        " WHERE `geo_zone_id` = '" . constant($this->prefix . 'ZONE') . "'" .
                                        " AND `zone_country_id` = '" . $order->billing['country']['id'] . "'" .
                                        ' ORDER BY `zone_id` ASC');
            while ($check = tep_db_fetch_array($check_query)) {
                if (($check['zone_id'] < 1) || ($check['zone_id'] == $order->billing['zone_id'])) {
                    $flag = true;
                    break;
                }
            }

            if (! $flag) {
                $this->enabled = false;
                return;
            }
        }

        // Check amount restrictions.
        if ((constant($this->prefix . 'MIN_AMOUNT') && $order->info['total'] < constant($this->prefix . 'MIN_AMOUNT'))
                || (constant($this->prefix . 'MAX_AMOUNT') && $order->info['total'] > constant($this->prefix . 'MAX_AMOUNT'))) {
            $this->enabled = false;
            return;
        }

        // Check currency.
        $defaultCurrency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
        if (! PayzenApi::findCurrencyByAlphaCode($order->info['currency']) && ! PayzenApi::findCurrencyByAlphaCode($defaultCurrency)) {
            // Currency is not supported, module is not available.
            $this->enabled = false;
        }
    }

    /**
     * JS checks: we let the gateway do all the validation itself.
     * @return false
     */
    function javascript_validation()
    {
        return false;
    }

    /**
     * Parameters for what the payment option will look like in the list.
     * @return array
     */
    function selection()
    {
        return array(
            'id' => $this->code,
            'module' => $this->title
        );
    }

    /**
     * Server-side checks after payment selection: We let the gateway do all the validation itself.
     * @return false
     */
    function pre_confirmation_check()
    {
        return false;
    }

    /**
     * Server-size checks before payment confirmation:  We let the gateway do all the validation itself.
     * @return false
     */
    function confirmation()
    {
        return false;
    }

    /**
     * Prepare the form that will be sent to the payment gateway.
     * @return string
     */
    function process_button()
    {
        require_once(DIR_FS_CATALOG . 'includes/classes/payzen_request.php');
        $request = new PayzenRequest(CHARSET);

        $request->setFromArray($this->_build_request());

        // To recover order session.
        $request->addExtInfo('session_id', session_id());

        // To recover order payment method.
        $request->addExtInfo('payment_method', $this->code);

        return $request->getRequestHtmlFields();
    }

    function _build_request()
    {
        global $order, $languages_id, $currencies, $customer_id;

        $data = array();

        // Admin configuration parameters.
        $config_params = array(
            'site_id', 'key_test', 'key_prod', 'ctx_mode', 'sign_algo', 'platform_url', 'available_languages',
            'capture_delay', 'redirect_enabled','redirect_success_timeout', 'redirect_success_message',
            'redirect_error_timeout', 'redirect_error_message', 'return_mode', 'validation_mode', 'payment_cards'
        );

        foreach ($config_params as $name) {
            $data[$name] = constant($this->prefix . strtoupper($name));
        }

        // Get the shop language code.
        $query = tep_db_query('SELECT `code` FROM `' . TABLE_LANGUAGES . "` WHERE `languages_id` = '$languages_id'");
        $langData = tep_db_fetch_array($query);
        $payzenLanguage = PayzenApi::isSupportedLanguage($langData['code']) ?
            strtolower($langData['code']) : constant($this->prefix . 'LANGUAGE');

        // Get the currency to use.
        $currencyValue = $order->info['currency_value'];
        $payzenCurrency = PayzenApi::findCurrencyByAlphaCode($order->info['currency']);
        if (! $payzenCurrency) {
            // Currency is not supported, use the default shop currency.
            $defaultCurrency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ?
                LANGUAGE_CURRENCY : DEFAULT_CURRENCY;

            $payzenCurrency = PayzenApi::findCurrencyByAlphaCode($defaultCurrency);
            $currencyValue = 1;
        }

        // Calculate amount.
        $total = tep_round($order->info['total'] * $currencyValue, $currencies->get_decimal_places($payzenCurrency->getAlpha3()));

        // Activate 3DS?
        $threedsMpi = null;
        if (constant($this->prefix . '3DS_MIN_AMOUNT') && ($order->info['total'] < constant($this->prefix . '3DS_MIN_AMOUNT'))) {
            $threedsMpi = '2';
        }

        // Other parameters.
        $data += array(
            // Order info.
            'amount' => $payzenCurrency->convertAmountToInteger($total),
            'order_id' => $this->_guess_order_id(),
            'contrib' => self::$CMS_IDENTIFIER . '_' . self::$PLUGIN_VERSION . '/' . tep_get_version() . '/' . PayzenApi::shortPhpVersion(),

            // Misc data.
            'currency' => $payzenCurrency->getNum(),
            'language' => $payzenLanguage,
            'threeds_mpi' => $threedsMpi,
            'url_return' => HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process_payzen.php',

            // Customer info.
            'cust_id' => $customer_id,
            'cust_email' => $order->customer['email_address'],
            'cust_phone' => $order->customer['telephone'],
            'cust_cell_phone' => $order->customer['telephone'], // No cell phone defined, just use customer phone.
            'cust_first_name' => $order->billing['firstname'],
            'cust_last_name' => $order->billing['lastname'],
            'cust_address' => $order->billing['street_address'] . ' ' . $order->billing['suburb'],
            'cust_city' => $order->billing['city'],
            'cust_state' => $order->billing['state'],
            'cust_zip' => $order->billing['postcode'],
            'cust_country' => $order->billing['country']['iso_code_2']
        );

        // Delivery data.
        if ($order->delivery != false) {
            $data['ship_to_first_name'] = $order->delivery['firstname'];
            $data['ship_to_last_name'] = $order->delivery['lastname'];
            $data['ship_to_street'] = $order->delivery['street_address'];
            $data['ship_to_street2'] = $order->delivery['suburb'];
            $data['ship_to_city'] = $order->delivery['city'];
            $data['ship_to_state'] = $order->delivery['state'];

            $countryCode = $order->delivery['country']['iso_code_2'];
            if ($countryCode == 'FX') { // FX not recognized as a country code by PayPal.
                $countryCode = 'FR';
            }

            $data['ship_to_country'] = $countryCode;

            $data['ship_to_zip'] = $order->delivery['postcode'];
        }

        return $data;
    }

    /**
     * Verify client data after he returned from payment gateway.
     */
    function before_process()
    {
        global $order, $payzen_response, $messageStack, $payzen_plugin_features;

        require_once(DIR_FS_CATALOG . 'includes/classes/payzen_response.php');
        $payzen_response = new PayzenResponse(
            array_map('stripslashes', $_REQUEST),
            constant($this->prefix . 'CTX_MODE'),
            @constant($this->prefix . 'KEY_TEST'),
            constant($this->prefix . 'KEY_PROD'),
            constant($this->prefix . 'SIGN_ALGO')
        );
        $fromServer = ($payzen_response->get('hash') != null);

        // Check authenticity.
        if (! $payzen_response->isAuthentified()) {
            if ($fromServer) {
                die($payzen_response->getOutputForGateway('auth_fail'));
            } else {
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
                die();
            }
        }

        // Messages to display on payment result page.
        if (! $fromServer && $payzen_plugin_features['prodfaq'] && (constant($this->prefix . 'CTX_MODE') == 'TEST')) {
            $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO, 'success');
        }

        // Act according to case.
        if ($payzen_response->isAcceptedPayment()) {
            // Successful payment.

            if ($this->_is_order_paid()) {
                if ($fromServer) {
                    die ($payzen_response->getOutputForGateway('payment_ok_already_done'));
                } else {
                    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
                    die();
                }
            } else {
                // Update order payment data.
                $order->info['cc_type'] = $payzen_response->get('card_brand');
                $order->info['cc_number'] = $payzen_response->get('card_number');

                if ($payzen_response->get('expiry_month') && $payzen_response->get('expiry_year')) {
                    $order->info['cc_expires'] = str_pad($payzen_response->get('expiry_month'), 2, '0', STR_PAD_LEFT) . substr($payzen_response->get('expiry_year'), 2);
                }

                // Let's borrow the cc_owner field to store transaction id.
                $order->info['cc_owner'] = '-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transaction: ' . $payzen_response->get('trans_id');

                // Let checkout_process.php finish the job.
                return false;
            }
        } else {
            // Payment process failed.
            if ($fromServer) {
                die($payzen_response->getOutputForGateway('payment_ko'));
            } else {
                if (! $payzen_response->isCancelledPayment()) {
                    $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR, 'error');
                }

                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
                die();
            }
        }
    }

    /**
     * Post-processing after the order has been finalised.
     */
    function after_process()
    {
        global $payzen_response, $messageStack;

        // This function is called only when payment was successful and the order is not registered yet.

        $fromServer = ($payzen_response->get('hash') != null);

        if ($fromServer) {
            $this->_clear_session_vars();

            die ($payzen_response->getOutputForGateway('payment_ok'));
        } else {
            // Payment confirmed by client retun, show a warning if TEST mode.
            if (constant($this->prefix . 'CTX_MODE') == 'TEST') {
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN . '<br />' . MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL, 'warning');
            }

            return false;
        }
    }

    /**
     * Unregister session variables used during checkout and clear cart.
     */
    function _clear_session_vars()
    {
        global $cart;
        tep_session_unregister('sendto');
        tep_session_unregister('billto');
        tep_session_unregister('shipping');
        tep_session_unregister('payment');
        tep_session_unregister('comments');

        // Reset cart to allow new checkout process.
        $cart->reset(true);
    }

    /**
     * Return true if the module is installed.
     * @return bool
     */
    function check()
    {
        if (! isset($this->_check)) {
            $check_query = tep_db_query('SELECT `configuration_value` FROM `' . TABLE_CONFIGURATION . '`' .
                                        " WHERE `configuration_key` = '" . $this->prefix . "STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }

        return $this->_check;
    }


    /**
     * Build and execute a query for the install() function.
     * Parameters have to be escaped before.
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
    function _install_query($key, $value, $sort_order, $set_function=null, $use_function=null)
    {
        $sql_data = array(
            'configuration_title' => constant('MODULE_PAYMENT_PAYZEN_' . $key . '_TITLE'),
            'configuration_key' => $this->prefix . $key,
            'configuration_value' => $value,
            'configuration_description' => constant('MODULE_PAYMENT_PAYZEN_' . $key . '_DESC'),
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
     * Module install (register admin-managed parameters in database).
     */
    function install()
    {
        global $payzen_plugin_features;

        // Ex: _install_query($key, $value, $group_id, $sort_order, $set_function=null, $use_function=null)
        // osCommerce specific parameters.
        $this->_install_query('STATUS', '1', 1, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
        $this->_install_query('SORT_ORDER', '0', 2);
        $this->_install_query('ZONE', '0', 3, 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title');

        // Gateway access parameters.
        $this->_install_query('SITE_ID', self::$SITE_ID, 10);

        $params = 'array(\'PRODUCTION\')';
        if (! $payzen_plugin_features['qualif']) {
            $params = 'array(\'TEST\', \'PRODUCTION\')';
            $this->_install_query('KEY_TEST', self::$KEY_TEST, 11);
        }

        $this->_install_query('KEY_PROD', self::$KEY_PROD, 12);
        $this->_install_query('CTX_MODE', self::$CTX_MODE, 13, "tep_cfg_select_option($params,");
        $this->_install_query('SIGN_ALGO', self::$SIGN_ALGO, 14, 'payzen_cfg_draw_pull_down_sign_algos(', 'payzen_get_sign_algo_title');
        $this->_install_query('PLATFORM_URL', self::$GATEWAY_URL, 15);

        $this->_install_query('LANGUAGE', self::$LANGUAGE, 21, 'payzen_cfg_draw_pull_down_langs(', 'payzen_get_lang_title');
        $this->_install_query('AVAILABLE_LANGUAGES', '', 22, 'payzen_cfg_draw_pull_down_multi_langs(', 'payzen_get_multi_lang_title');
        $this->_install_query('CAPTURE_DELAY', '', 23);
        $this->_install_query('3DS_MIN_AMOUNT', '', 26);

        $this->_install_query('VALIDATION_MODE', '', 24, 'payzen_cfg_draw_pull_down_validation_modes(', 'payzen_get_validation_mode_title');
        $this->_install_query('PAYMENT_CARDS', '', 25, 'payzen_cfg_draw_pull_down_cards(', 'payzen_get_card_title');

        // Amount restriction.
        $this->_install_query('MIN_AMOUNT', '', 30);
        $this->_install_query('MAX_AMOUNT', '', 31);

        // Gateway return parameters.
        $this->_install_query('REDIRECT_ENABLED', '0', 40, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
        $this->_install_query('REDIRECT_SUCCESS_TIMEOUT', '5', 41);
        $this->_install_query('REDIRECT_SUCCESS_MESSAGE', MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE, 42);
        $this->_install_query('REDIRECT_ERROR_TIMEOUT', '5', 43);
        $this->_install_query('REDIRECT_ERROR_MESSAGE', MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE, 44);
        $this->_install_query('RETURN_MODE', 'GET', 45, "tep_cfg_select_option(array(\'GET\', \'POST\'), ");
        $this->_install_query('ORDER_STATUS', '0', 48, 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name');
    }

    /**
     * Module deletion.
     */
    function remove()
    {
        $keys = $this->keys();

        foreach ($keys as $key) {
            tep_db_query('DELETE FROM `' . TABLE_CONFIGURATION . "` WHERE `configuration_key` = '$key'");
        }
    }

    /**
     * Returns the names of module's parameters.
     * @return array[int]string
     */
    function keys()
    {
        global $payzen_plugin_features;

        $keys = array();

        $keys[] = 'MODULE_PAYMENT_PAYZEN_STATUS';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_SORT_ORDER';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_ZONE';

        $keys[] = 'MODULE_PAYMENT_PAYZEN_SITE_ID';

        if (! $payzen_plugin_features['qualif']) {
            $keys[] = 'MODULE_PAYMENT_PAYZEN_KEY_TEST';
        }

        $keys[] = 'MODULE_PAYMENT_PAYZEN_KEY_PROD';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_CTX_MODE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_SIGN_ALGO';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_PLATFORM_URL';

        $keys[] = 'MODULE_PAYMENT_PAYZEN_LANGUAGE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_VALIDATION_MODE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT';

        $keys[] = 'MODULE_PAYMENT_PAYZEN_MIN_AMOUNT';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_MAX_AMOUNT';

        $keys[] = 'MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_RETURN_MODE';
        $keys[] = 'MODULE_PAYMENT_PAYZEN_ORDER_STATUS';

        return $keys;
    }

    /**
     * Try to guess what will be the order's id when osCommerce will register it at the end of the payment process.
     * This is only used to set order_id in the request to the payment gateway. It might be inconsistent with the
     * final osCommerce order ID (in cases like two clients going to the payment gateway at the same time...).
     *
     * @return int
     */
    function _guess_order_id()
    {
        $query = tep_db_query('SELECT MAX(`orders_id`) AS `order_id` FROM `' . TABLE_ORDERS . '`');

        if (tep_db_num_rows($query) == 0) {
            return 0;
        } else {
            $result = tep_db_fetch_array($query);
            return $result['order_id'] + 1;
        }
    }

    /**
     * Check if order corresponding to entered trans_id is already saved.
     *
     * @return boolean true if order already saved
     */
    function _is_order_paid()
    {
        global $payzen_response;

        $orderId = $payzen_response->get('order_id');
        $customerId = $payzen_response->get('cust_id');
        $transId = $payzen_response->get('trans_id');

        $query = tep_db_query('SELECT * FROM `' . TABLE_ORDERS . '`' .
                " WHERE orders_id >= $orderId" .
                " AND customers_id = $customerId" .
                " AND cc_owner LIKE '%Transaction: $transId'");

        return tep_db_num_rows($query) > 0;
    }
}
