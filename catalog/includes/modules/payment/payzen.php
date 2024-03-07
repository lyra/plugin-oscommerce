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
require_once(DIR_FS_CATALOG . 'includes/classes/payzen-sdk-autoload.php');

use Lyranetwork\Payzen\Sdk\Form\Api as PayzenApi;
use Lyranetwork\Payzen\Sdk\Rest\Api as PayzenRest;
use Lyranetwork\Payzen\Sdk\Form\Request as PayzenRequest;
use Lyranetwork\Payzen\Sdk\Form\Response as PayzenResponse;

if (defined('DIR_FS_ADMIN')) {
    // Include the admin configuration functions.
    require_once(DIR_FS_ADMIN . 'includes/functions/payzen_output.php');
}

global $language, $payzen_plugin_features, $logger, $payzen_statuses;

if (! isset($logger)) {
    require_once(DIR_FS_CATALOG . "admin/includes/classes/logger.php");
    $logger = new logger();
}

$payzen_plugin_features = array(
    'qualif' => false,
    'prodfaq' => true,
    'restrictmulti' => false,
    'shatwo' => true,
    'smartform' => true,

    'multi' => true
);

$payzen_statuses = array(
    'PAYZEN_CANCELLED' => 'Cancelled [PAYZEN]',
    'PAYZEN_FAILED' => 'Failed [PAYZEN]'
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
    private static $PLUGIN_VERSION = '1.4.0';
    private static $GATEWAY_VERSION = 'V2';
    private static $REST_URL = 'https://api.payzen.eu/api-payment/';
    private static $STATIC_URL = 'https://static.payzen.eu/static/';

    private static $HEADER_ERROR_500 = 'HTTP/1.1 500 Internal Server Error';

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

        // Initialize module code.
        $this->code = 'payzen';

        // Initialize module title.
        $this->title = MODULE_PAYMENT_PAYZEN_STD_TITLE;

        // Initialize module description.
        $this->description = '';
        $this->description .= '<b>' . MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION . '</b>';
        $this->description .= '<br/><br/>';

        $this->description .= '<table class="infoBoxContent">';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_DEVELOPED_BY . '</td><td><a href="http://www.lyra.com/" target="_blank"><b>Lyra network</b></a></td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL . '</td><td>' . PayzenApi::formatSupportEmails(self::$SUPPORT_EMAIL) . '</td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION . '</td><td><b>' . self::$PLUGIN_VERSION . '</b></td></tr>';
        $this->description .= '<tr><td style="text-align: right;">' . MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION . '</td><td><b>' . self::$GATEWAY_VERSION . '</b></td></tr>';

        $this->description .= '<tr style="height: 20px;" colspan="2"><td></td></tr>'; // Separator.
        $this->description .= '<tr>
                                   <td style="text-align: right; vertical-align: top;">' . MODULE_PAYMENT_PAYZEN_DOCUMENTATION_URL . '</td>';
        $this->description .= '<td>';

        // Get documentation links.
        $doc_languages = array(
            'fr' => 'Français',
            'en' => 'English',
            'es' => 'Español',
            'pt' => 'Português',
            'br' => 'Português',
            'de' => 'Deutsch'
            // Complete when other languages are managed.
        );

        foreach (PayzenApi::getOnlineDocUri() as $lang => $docUri) {
            $this->description .= '<a style="margin-left: 10px; text-decoration: none; text-transform: uppercase;" href="' . $docUri . 'oscommerce/sitemap.html" target="_blank"><b>' . $doc_languages[$lang] . '</<b></a>';
        }

        $this->description .= '</td>';
        $this->description .= '</tr>';
        $this->description .= '</table>';
        $this->description .= '<hr />';

        // Initialize enabled.
        $this->enabled = defined($this->prefix . 'STATUS') && (constant($this->prefix . 'STATUS') == '1');

        // Initialize module sort_order.
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
        if ((int)constant($this->prefix . 'ZONE') > 0) {
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
        $default_currency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
        if (! PayzenApi::findCurrencyByAlphaCode($order->info['currency']) && !PayzenApi::findCurrencyByAlphaCode($default_currency)) {
            // Currency is not supported, module is not available.
            $this->enabled = false;
        }
    }

    /**
     * JS checks: we let the gateway do all the validation itself.
     *
     * @return false
     */
    function javascript_validation()
    {
        return false;
    }

    /**
     * Parameters for what the payment option will look like in the list.
     *
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
     *
     * @return false
     */
    function pre_confirmation_check()
    {
        return false;
    }

    /**
     * Server-size checks before payment confirmation:  We let the gateway do all the validation itself.
     */
    function confirmation()
    {
        global $payzen_order_id, $customer_id, $languages_id, $order, $order_total_modules, $token, $order_info_saved;

        if ($this->_is_smartform()) {
            $insert_order = false;

            // Check if we need to insert the order in the database to retrieve its id.
            if (tep_session_is_registered('payzen_order_id') && tep_session_is_registered('order_info_saved')) {
                $order_id = is_numeric($payzen_order_id) ? (int) $payzen_order_id : $payzen_order_id;

                $currency_check = tep_db_query("select currency from " . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
                $currency = tep_db_fetch_array($currency_check);

                // Check if the cart or the currency of the order changed. If so, remove the order from the database to reinsert it later.
                if (($currency['currency'] != $order->info['currency']) || ($order_info_saved != json_encode($order))) {
                    $this->_delete_order($order_id);
                    $insert_order = true;
                }
            } else {
                $insert_order = true;
            }

            if ($insert_order) {
                // Insert the order in the database to retrieve its id.
                $sql_data_array = array(
                    'customers_id' => $customer_id,
                    'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
                    'customers_company' => $order->customer['company'],
                    'customers_street_address' => $order->customer['street_address'],
                    'customers_suburb' => $order->customer['suburb'],
                    'customers_city' => $order->customer['city'],
                    'customers_postcode' => $order->customer['postcode'],
                    'customers_state' => $order->customer['state'],
                    'customers_country' => $order->customer['country']['title'],
                    'customers_telephone' => $order->customer['telephone'],
                    'customers_email_address' => $order->customer['email_address'],
                    'customers_address_format_id' => $order->customer['format_id'],
                    'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
                    'delivery_company' => $order->delivery['company'],
                    'delivery_street_address' => $order->delivery['street_address'],
                    'delivery_suburb' => $order->delivery['suburb'],
                    'delivery_city' => $order->delivery['city'],
                    'delivery_postcode' => $order->delivery['postcode'],
                    'delivery_state' => $order->delivery['state'],
                    'delivery_country' => $order->delivery['country']['title'],
                    'delivery_address_format_id' => $order->delivery['format_id'],
                    'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
                    'billing_company' => $order->billing['company'],
                    'billing_street_address' => $order->billing['street_address'],
                    'billing_suburb' => $order->billing['suburb'],
                    'billing_city' => $order->billing['city'],
                    'billing_postcode' => $order->billing['postcode'],
                    'billing_state' => $order->billing['state'],
                    'billing_country' => $order->billing['country']['title'],
                    'billing_address_format_id' => $order->billing['format_id'],
                    'payment_method' => $order->info['payment_method'],
                    'cc_type' => $order->info['cc_type'],
                    'cc_owner' => $order->info['cc_owner'],
                    'cc_number' => $order->info['cc_number'],
                    'cc_expires' => $order->info['cc_expires'],
                    'date_purchased' => 'now()',
                    'orders_status' => 1,
                    'currency' => $order->info['currency'],
                    'currency_value' => $order->info['currency_value']
                );

                tep_db_perform(TABLE_ORDERS, $sql_data_array);

                $insert_id = tep_db_insert_id();

                // Process and insert all the order totals in the database.
                $order_totals = $order_total_modules->process();
                foreach ($order_totals as $order_total) {
                    $sql_data_array = array(
                        'orders_id' => $insert_id,
                        'title' => $order_total['title'],
                        'text' => $order_total['text'],
                        'value' => $order_total['value'],
                        'class' => $order_total['code'],
                        'sort_order' => $order_total['sort_order']
                    );

                    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
                }

                // For each product in the order, insert it in the database linked to the order that we just created.
                foreach ($order->products as $product) {
                    $sql_data_array = array(
                        'orders_id' => $insert_id,
                        'products_id' => tep_get_prid($product['id']),
                        'products_model' => $product['model'],
                        'products_name' => $product['name'],
                        'products_price' => $product['price'],
                        'final_price' => $product['final_price'],
                        'products_tax' => $product['tax'],
                        'products_quantity' => $product['qty']
                    );

                    tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

                    $order_products_id = tep_db_insert_id();

                    // If the product has attributes, insert them in the product_attributes table and in the product_download if they have the option.
                    if (isset($product['attributes'])) {
                        foreach ($product['attributes'] as $attribute) {
                            if (DOWNLOAD_ENABLED == 'true') {
                                $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                    from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                    left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                    on pa.products_attributes_id=pad.products_attributes_id
                                    where pa.products_id = '" . $product['id'] . "'
                                    and pa.options_id = '" . $attribute['option_id'] . "'
                                    and pa.options_id = popt.products_options_id
                                    and pa.options_values_id = '" . $attribute['value_id'] . "'
                                    and pa.options_values_id = poval.products_options_values_id
                                    and popt.language_id = '" . $languages_id . "'
                                    and poval.language_id = '" . $languages_id . "'";
                            } else {
                                $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                                    from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                    where pa.products_id = '" . $product['id'] . "'
                                    and pa.options_id = '" . $attribute['option_id'] . "'
                                    and pa.options_id = popt.products_options_id
                                    and pa.options_values_id = '" . $attribute['value_id'] . "'
                                    and pa.options_values_id = poval.products_options_values_id
                                    and popt.language_id = '" . $languages_id . "'
                                    and poval.language_id = '" . $languages_id . "'";
                            }

                            $attributes = tep_db_query($attributes_query);
                            $attributes_values = tep_db_fetch_array($attributes);

                            $sql_data_array = array(
                                'orders_id' => $insert_id,
                                'orders_products_id' => $order_products_id,
                                'products_options' => $attributes_values['products_options_name'],
                                'products_options_values' => $attributes_values['products_options_values_name'],
                                'options_values_price' => $attributes_values['options_values_price'],
                                'price_prefix' => $attributes_values['price_prefix']
                            );

                            tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                            if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                                $sql_data_array = array(
                                    'orders_id' => $insert_id,
                                    'orders_products_id' => $order_products_id,
                                    'orders_products_filename' => $attributes_values['products_attributes_filename'],
                                    'download_maxdays' => $attributes_values['products_attributes_maxdays'],
                                    'download_count' => $attributes_values['products_attributes_maxcount']
                                );

                                tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                            }
                        }
                    }
                }

                // Store in session the order id, the order just created to know if we already processed it.
                $order_info_saved = json_encode($order);
                $payzen_order_id = $insert_id;
                tep_session_register('payzen_order_id');
                tep_session_register('order_info_saved');
            }

            $token = $this->_get_form_token();
            if ($token) {
                $content = $this->_load_static_files();
                $content .= $this->_generate_smartform_html($token);
                $content .= $this->_smartform_javascript();

                return array('title' => $content);
            }

        }

        return false;
    }

    private function _get_form_token()
    {
        global $payzen_order_id, $logger;

        $order_id = is_numeric($payzen_order_id) ? (int) $payzen_order_id : $payzen_order_id;

        $params = $this->_get_rest_api_form_token_data($order_id);

        $logger->write("Creating form token for order #$order_id with parameters: $params", 'INFO');

        try {
            // Perform our request.
            $client = new PayzenRest(
                constant($this->prefix . 'REST_URL'),
                constant($this->prefix . 'SITE_ID'),
                $this->_get_private_key()
            );

            $response = $client->post('V4/Charge/CreatePayment', $params);

            if ($response['status'] !== 'SUCCESS') {
                $msg = "Error while creating payment form token for order #$order_id: " . $response['answer']['errorMessage'] . ' (' . $response['answer']['errorCode'] . ').';

                if (! empty($response['answer']['detailedErrorMessage'])) {
                    $msg .= ' Detailed message: ' . $response['answer']['detailedErrorMessage'] . ' (' . $response['answer']['detailedErrorCode'] . ').';
                }

                $logger->write($msg, 'ERROR');

                return false;
            } else {
                $logger->write("Form token created successfully for order #$order_id.", 'INFO');

                return $response['answer']['formToken'];
            }
        } catch (Exception $e) {
            $logger->write($e->getMessage(), 'ERROR');

            return false;
        }
    }

    private function _get_private_key()
    {
        $ctx_mode = constant($this->prefix . 'CTX_MODE');
        $key = ($ctx_mode == 'TEST') ? 'PRIV_TEST_KEY' : 'PRIV_PROD_KEY';

        return constant($this->prefix . $key);
    }

    private function _get_rest_api_form_token_data($order_id)
    {
        global $order, $logger;

        if (empty($order)) {
            $logger->write("Cannot create a form token. Empty cart passed.", 'ERROR');

            return false;
        }

        $amount = $order->info['total'];
        if ($amount <= 0) {
            $logger->write("Cannot create a form token. Invalid amount passed.", 'ERROR');

            return false;
        }

        // Check currency.
        $currency = PayzenApi::findCurrencyByAlphaCode($order->info['currency']);
        if (! $currency) {
            $logger->write("Cannot create a form token. Unsupported currency passed [" . $currency->getAlpha3() . "].", 'ERROR');

            return false;
        }

        $request = $this->_prepare_request();

        // Activate 3DS?
        $strong_auth = $request->get("threeds_mpi") === "2" ? 'DISABLED' : 'AUTO';

        $data = [
            'orderId' => $order_id,
            'customer' => [
                'email' => $request->get('cust_email'),
                'reference' => $request->get('cust_id'),
                'billingDetails' => [
                    'language' => $request->get('language'),
                    'title' => $request->get('cust_title'),
                    'firstName' => $request->get('cust_first_name'),
                    'lastName' => $request->get('cust_last_name'),
                    'address' => $request->get('cust_address'),
                    'zipCode' => $request->get('cust_zipcode'),
                    'city' => $request->get('cust_city'),
                    'phoneNumber' => $request->get('cust_phone'),
                    'cellPhoneNumber' => $request->get('cust_cell_phone'),
                    'country' => $request->get('cust_country')
                ]
            ],
            'transactionOptions' => [
                'cardOptions' => [
                    'paymentSource' => 'EC'
                ]
            ],
            'contrib' => $request->get('contrib'),
            'strongAuthentication' => $strong_auth,
            'currency' => $currency->getAlpha3(),
            'amount' => $currency->convertAmountToInteger($amount),
            'metadata' => array(
                'session_id' => $request->get('ext_info_session_id'),
                'payment_method' => $request->get('ext_info_payment_method')
            )
        ];

        // In case of Smartform, only payment means supporting capture delay will be shown.
        $capture_delay = constant($this->prefix . 'CAPTURE_DELAY');
        if (is_numeric($capture_delay)) {
            $data['transactionOptions']['cardOptions']['capture_delay'] = $capture_delay;
        }

        $validation_mode = constant($this->prefix . 'VALIDATION_MODE');
        if (! is_null($validation_mode)) {
            $data['transactionOptions']['cardOptions']['manualValidation'] = ($validation_mode === '1') ? 'YES' : 'NO';
        }

        // Set shipping info.
        if ($order->delivery && is_array($order->delivery)) {
            $data['customer']['shippingDetails'] = array(
                'firstName' => $request->get('ship_to_first_name'),
                'lastName' => $request->get('ship_to_last_name'),
                'address' => $request->get('ship_to_street'),
                'address2' => $request->get('ship_to_street2'),
                'zipCode' => $request->get('ship_to_zip'),
                'city' => $request->get('ship_to_city'),
                'state' => $request->get('ship_to_state'),
                'country' => $request->get('ship_to_country'),
                'deliveryCompanyName' => $request->get('ship_to_delivery_company_name')
            );
        }

        // Set the maximum attempts number in case of failed payment.
        $rest_attempts = constant($this->prefix . 'REST_ATTEMPTS');
        if ($rest_attempts !== '') {
            $data['transactionOptions']['cardOptions']['retry'] = $rest_attempts;
        }

        // Filter payment means when creating payment token.
        $data['paymentMethods'] = $this->_get_payment_means_for_smartform();

        $data['formAction'] = "PAYMENT";

        return json_encode($data);
    }

    private function _get_payment_means_for_smartform()
    {
        $payment_cards = constant($this->prefix . 'PAYMENT_CARDS');
        if ($payment_cards == "") {
            return array();
        }

        return explode(';', $payment_cards);
    }

    /**
     * Prepare the form that will be sent to the payment gateway.
     *
     * @return string
     */
    function process_button()
    {
        $request = $this->_prepare_request();

        return $request->getRequestHtmlFields();
    }

    function _prepare_request()
    {
        global $order, $languages_id, $currencies, $customer_id;

        $request = new PayzenRequest(CHARSET);

        $data = array();

        // Admin configuration parameters.
        $config_params = array(
            'site_id', 'key_test', 'key_prod', 'ctx_mode', 'sign_algo', 'platform_url', 'available_languages',
            'capture_delay', 'redirect_enabled', 'redirect_success_timeout', 'redirect_success_message',
            'redirect_error_timeout', 'redirect_error_message', 'return_mode', 'validation_mode', 'payment_cards'
        );

        foreach ($config_params as $name) {
            $data[$name] = constant($this->prefix . strtoupper($name));
        }

        // Get the shop language code.
        $lang = $this->_get_shop_language_code($languages_id);

        // Get the currency to use.
        $currency_value = $order->info['currency_value'];
        $payzen_currency = PayzenApi::findCurrencyByAlphaCode($order->info['currency']);
        if (! $payzen_currency) {
            // Currency is not supported, use the default shop currency.
            $default_currency = (defined('USE_DEFAULT_LANGUAGE_CURRENCY') && USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ?
                LANGUAGE_CURRENCY : DEFAULT_CURRENCY;

            $payzen_currency = PayzenApi::findCurrencyByAlphaCode($default_currency);
            $currency_value = 1;
        }

        // Calculate amount.
        $total = tep_round($order->info['total'] * $currency_value, $currencies->get_decimal_places($payzen_currency->getAlpha3()));

        // Activate 3DS?
        $threeds_mpi = null;
        if (constant($this->prefix . '3DS_MIN_AMOUNT') && ($order->info['total'] < constant($this->prefix . '3DS_MIN_AMOUNT'))) {
            $threeds_mpi = '2';
        }

        // Other parameters.
        $data += array(
            // Order info.
            'amount' => $payzen_currency->convertAmountToInteger($total),
            'order_id' => $this->_guess_order_id(),
            'contrib' => self::$CMS_IDENTIFIER . '_' . self::$PLUGIN_VERSION . '/' . tep_get_version() . '/' . PayzenApi::shortPhpVersion(),

            // Misc data.
            'currency' => $payzen_currency->getNum(),
            'language' => $lang,
            'threeds_mpi' => $threeds_mpi,
            'url_return' => HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process_payzen.php',

            // Customer info.
            'cust_id' => $customer_id,
            'cust_email' => $order->customer['email_address'],
            'cust_phone' => $order->customer['telephone'],
            'cust_cell_phone' => $order->customer['telephone'], // No cell phone defined, just use customer phone.
            'cust_title' => $order->billing['billing_name'],
            'cust_first_name' => $order->billing['firstname'],
            'cust_last_name' => $order->billing['lastname'],
            'cust_address' => $order->billing['street_address'] . ' ' . $order->billing['suburb'],
            'cust_city' => $order->billing['city'],
            'cust_state' => $order->billing['state'],
            'cust_zip' => $order->billing['postcode'],
            'cust_country' => $order->billing['country']['iso_code_2']
        );

        // Delivery data.
        if ($order->delivery) {
            $data['ship_to_first_name'] = $order->delivery['firstname'];
            $data['ship_to_last_name'] = $order->delivery['lastname'];
            $data['ship_to_street'] = $order->delivery['street_address'];
            $data['ship_to_street2'] = $order->delivery['suburb'];
            $data['ship_to_city'] = $order->delivery['city'];
            $data['ship_to_state'] = $order->delivery['state'];
            $data['ship_to_delivery_company_name'] = $order->delivery['company'];

            $country_code = $order->delivery['country']['iso_code_2'];
            if ($country_code == 'FX') { // FX not recognized as a country code by PayPal.
                $country_code = 'FR';
            }

            $data['ship_to_country'] = $country_code;

            $data['ship_to_zip'] = $order->delivery['postcode'];
        }

        $request->setFromArray($data);

        // To recover order session.
        $request->addExtInfo('session_id', session_id());

        // To recover order payment method.
        $request->addExtInfo('payment_method', $this->code);

        return $request;
    }

    /**
     * Verify client data after he returned from payment gateway.
     */
    function before_process()
    {
        global $order, $payzen_response, $messageStack, $payzen_plugin_features, $logger;

        $smartform = false;
        $request = $_REQUEST;
        if (isset($_REQUEST['kr-answer'])) {
            $_REQUEST['kr-answer'] = stripslashes($_REQUEST['kr-answer']);
            $request = $this->__convert_rest_result(json_decode(stripslashes($_REQUEST['kr-answer']), true));
            $smartform = true;
        }

        $payzen_response = new PayzenResponse(
            array_map('stripslashes', $request),
            constant($this->prefix . 'CTX_MODE'),
            constant($this->prefix . 'KEY_TEST'),
            constant($this->prefix . 'KEY_PROD'),
            constant($this->prefix . 'SIGN_ALGO')
        );

        $from_server = ($payzen_response->get('hash') != null || (isset($_REQUEST["kr-hash-key"]) && $_REQUEST["kr-hash-key"] !== "sha256_hmac"));

        // Check authenticity.
        if (! $payzen_response->isAuthentified() && ! $this->__check_response_hash($_REQUEST, $this->_get_rest_key($_REQUEST))) {
            $logger->write("Tries to access validation page without valid signature with data: " . print_r($_POST, true), 'ERROR');
            if ($from_server) {
                $logger->write('IPN URL PROCESS END', 'INFO');

                header(self::$HEADER_ERROR_500, true, 500);
                die($payzen_response->getOutputForGateway('auth_fail'));
            } else {
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

                $logger->write('RETURN URL PROCESS END', 'INFO');

                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
                die();
            }
        }

        // Messages to display on payment result page.
        if (! $from_server && $payzen_plugin_features['prodfaq'] && (constant($this->prefix . 'CTX_MODE') == 'TEST')) {
            $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO, 'success');
        }

        $order_id = $payzen_response->get('order_id');

        // Act according to case.
        if ($payzen_response->isAcceptedPayment()) { // Successful payment.
            if ($this->_is_order_paid()) {
                $logger->write("Order #" . $payzen_response->get('order_id') . " is already saved.", 'INFO');
                if ($from_server) {
                    $logger->write('IPN URL PROCESS END', 'INFO');

                    die ($payzen_response->getOutputForGateway('payment_ok_already_done'));
                } else {
                    $logger->write('RETURN URL PROCESS END', 'INFO');

                    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
                    die();
                }
            } else {
                $logger->write("Payment successfull, let's complete order #" . $payzen_response->get('order_id') . ".", 'INFO');

                $card_brand = $payzen_response->get('card_brand');
                $card_number = $payzen_response->get('card_number');
                $trans_id = '-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transaction: ' . $payzen_response->get('trans_id');
                $expires = $order->info['cc_expires'];

                if ($payzen_response->get('expiry_month') && $payzen_response->get('expiry_year')) {
                    $expires = str_pad($payzen_response->get('expiry_month'), 2, '0', STR_PAD_LEFT) . substr($payzen_response->get('expiry_year'), 2);
                }

                // When smartform is used, order is already created, and we update payment data directly.
                if ($smartform && $order_id) {
                    $this->_update_order_data($card_brand, $card_number, $expires, $trans_id);

                    $this->_clear_session_vars();

                    if ($from_server) {
                        $logger->write('IPN URL PROCESS END', 'INFO');

                        die ($payzen_response->getOutputForGateway('payment_ok'));
                    } else {
                        $logger->write('RETURN URL PROCESS END', 'INFO');

                        tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
                        die();
                    }
                }

                // Update order payment data.
                $order->info['cc_type'] = $card_brand;
                $order->info['cc_number'] = $card_number;

                $order->info['cc_expires'] = $expires;

                // Let's borrow the cc_owner field to store transaction id.
                $order->info['cc_owner'] = $trans_id;
                // Let checkout_process.php finish the job.
                return false;
            }
        } else {
            // Payment process failed.
            $logger->write("Payment failed or cancelled for order #" . $payzen_response->get('order_id') . ".", 'INFO');

            $this->_update_order_status();
            if ($from_server) {
                $logger->write('IPN URL PROCESS END', 'INFO');

                die($payzen_response->getOutputForGateway('payment_ko'));
            } else {
                if (! $payzen_response->isCancelledPayment()) {
                    $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR, 'error');
                }

                $logger->write('RETURN URL PROCESS END', 'INFO');

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
        global $payzen_response, $messageStack, $logger;

        // This function is called only when payment was successful and the order is not registered yet.
        $from_server = ($payzen_response->get('hash') != null);

        if(! $payzen_response->isAcceptedPayment() || $payzen_response->isPendingPayment()) {
            $this->_update_order_status();
        }

        if ($from_server) {
            $this->_clear_session_vars();

            $logger->write('IPN URL PROCESS END', 'INFO');

            die ($payzen_response->getOutputForGateway('payment_ok'));
        } else {
            // Payment confirmed by client retun, show a warning if TEST mode.
            if (constant($this->prefix . 'CTX_MODE') == 'TEST') {
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN . '<br />' . MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL, 'warning');
            }

            $logger->write('RETURN URL PROCESS END', 'INFO');

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

        tep_session_unregister('payzen_order_id');
        tep_session_unregister('order_info_saved');

        // Reset cart to allow new checkout process.
        $cart->reset(true);
    }

    /**
     * Return true if the module is installed.
     *
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
     */
    function _install_query($key, $value, $sort_order, $set_function = null, $use_function = null)
    {
        $title = defined('MODULE_PAYMENT_PAYZEN_' . $key . '_TITLE') ? constant('MODULE_PAYMENT_PAYZEN_' . $key . '_TITLE') : '';
        $description = defined('MODULE_PAYMENT_PAYZEN_' . $key . '_DESC') ? constant('MODULE_PAYMENT_PAYZEN_' . $key . '_DESC') : '';

        $sql_data = array(
            'configuration_title' => $title,
            'configuration_key' => $this->prefix . $key,
            'configuration_value' => $value,
            'configuration_description' => $description,
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

        $ipn_url = HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process_payzen.php';

        $this->_install_payzen_status();

        // _install_query($key, $value, $group_id, $sort_order, $set_function=null, $use_function=null)
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
        $this->_install_query('IPN_URL', $ipn_url, 16, "payzen_tep_cfg_disabled_input(");

        if ($payzen_plugin_features['smartform']) {
            $this->_install_query('REST_API_KEYS', '', 17, "payzen_tep_cfg_title_fields(");
            $this->_install_query('PRIV_TEST_KEY', '', 18);
            $this->_install_query('PRIV_PROD_KEY', '', 19);
            $this->_install_query('REST_URL', self::$REST_URL, 20);
            $this->_install_query('PUB_TEST_KEY', '', 21);
            $this->_install_query('PUB_PROD_KEY', '', 22);
            $this->_install_query('HMAC_TEST_KEY', '', 23);
            $this->_install_query('HMAC_PROD_KEY', '', 24);
            $this->_install_query('REST_IPN_URL', $ipn_url, 25, "payzen_tep_cfg_disabled_input(");
            $this->_install_query('STATIC_URL', self::$STATIC_URL, 26);

            $this->_install_query('ADVANCED_OPTIONS', '', 27, "payzen_tep_cfg_title_fields(");
            $this->_install_query('CARD_DATA_ENTRY_MODE', 'MODE_FORM', 28, 'payzen_cfg_draw_pull_down_card_data_entry_mode(', 'payzen_get_card_data_entry_mode_title');
            $this->_install_query('REST_POPIN_MODE', '0', 29, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
            $this->_install_query('REST_THEME', 'neon', 30, 'payzen_cfg_draw_pull_down_theme(');
            $this->_install_query('REST_COMPACT_MODE', '0', 31, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
            $this->_install_query('REST_THRESHOLD', '', 32);
            $this->_install_query('REST_ATTEMPTS', '', 33);
        }

        $this->_install_query('LANGUAGE', self::$LANGUAGE, 34, 'payzen_cfg_draw_pull_down_langs(', 'payzen_get_lang_title');
        $this->_install_query('AVAILABLE_LANGUAGES', '', 35, 'payzen_cfg_draw_pull_down_multi_langs(', 'payzen_get_multi_lang_title');
        $this->_install_query('CAPTURE_DELAY', '', 36);
        $this->_install_query('3DS_MIN_AMOUNT', '', 37);

        $this->_install_query('VALIDATION_MODE', '', 38, 'payzen_cfg_draw_pull_down_validation_modes(', 'payzen_get_validation_mode_title');
        $this->_install_query('PAYMENT_CARDS', '', 39, 'payzen_cfg_draw_pull_down_cards(', 'payzen_get_card_title');

        // Amount restriction.
        $this->_install_query('MIN_AMOUNT', '', 40);
        $this->_install_query('MAX_AMOUNT', '', 41);

        // Gateway return parameters.
        $this->_install_query('REDIRECT_ENABLED', '0', 42, 'payzen_cfg_draw_pull_down_bools(', 'payzen_get_bool_title');
        $this->_install_query('REDIRECT_SUCCESS_TIMEOUT', '5', 43);
        $this->_install_query('REDIRECT_SUCCESS_MESSAGE', MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE, 44);
        $this->_install_query('REDIRECT_ERROR_TIMEOUT', '5', 45);
        $this->_install_query('REDIRECT_ERROR_MESSAGE', MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE, 46);
        $this->_install_query('RETURN_MODE', 'POST', 47, "tep_cfg_select_option(array(\'GET\', \'POST\'), ");
        $this->_install_query('ORDER_STATUS', '2', 48, 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name');
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
        $keys[] = 'MODULE_PAYMENT_PAYZEN_IPN_URL';

        if ($payzen_plugin_features['smartform']) {
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_API_KEYS';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_PRIV_TEST_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_PRIV_PROD_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_URL';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_PUB_TEST_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_PUB_PROD_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_HMAC_TEST_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_HMAC_PROD_KEY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_IPN_URL';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_STATIC_URL';

            $keys[] = 'MODULE_PAYMENT_PAYZEN_ADVANCED_OPTIONS';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_POPIN_MODE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_THEME';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_COMPACT_MODE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_THRESHOLD';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_REST_ATTEMPTS';
        }

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

        $order_id = $payzen_response->get('order_id');
        $customer_id = $payzen_response->get('cust_id');
        $trans_id = $payzen_response->get('trans_id');

        $query = tep_db_query('SELECT * FROM `' . TABLE_ORDERS . '`' .
            " WHERE orders_id >= $order_id" .
            " AND customers_id = $customer_id" .
            " AND cc_owner LIKE '%Transaction: $trans_id'");

        return tep_db_num_rows($query) > 0;
    }

    private function _is_smartform()
    {
        $smartform_modes = [
            "MODE_SMARTFORM",
            "MODE_SMARTFORM_EXT_WITH_LOGOS",
            "MODE_SMARTFORM_EXT_WITHOUT_LOGOS"
        ];

        return in_array(constant($this->prefix . "CARD_DATA_ENTRY_MODE"), $smartform_modes);
    }

    private function _delete_order($order_id)
    {
        $check_query = tep_db_query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . $order_id . '" limit 1');

        if (tep_db_num_rows($check_query) < 1) {
            tep_db_query('delete from ' . TABLE_ORDERS . ' where orders_id = "' . $order_id . '"');
            tep_db_query('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . $order_id . '"');
            tep_db_query('delete from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . $order_id . '"');
            tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . $order_id . '"');
            tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . $order_id . '"');
            tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . $order_id . '"');

            return true;
        }

        return false;
    }

    private function _get_explode_element($separator, $string, $element)
    {
        if ($string === null) {
            return '';
        }

        $parts = explode($separator, $string);

        if (isset($parts[$element])) {
            return $parts[$element];
        }

        return '';
    }

    public function _get_shop_language_code($languages_id)
    {
        $query = tep_db_query('SELECT `code` FROM `' . TABLE_LANGUAGES . "` WHERE `languages_id` = '$languages_id'");
        $lang_data = tep_db_fetch_array($query);
        return PayzenApi::isSupportedLanguage($lang_data['code']) ? strtolower($lang_data['code']) : constant($this->prefix . 'LANGUAGE');
    }

    /** Return all static files needed to display the Smartform.
     *
     * @return string
     */
    private function _load_static_files()
    {
        global $languages_id;

        $js_static_url = constant($this->prefix . "STATIC_URL");
        $theme = constant($this->prefix . "REST_THEME");
        $pub_key = constant($this->prefix . "CTX_MODE") === 'TEST' ? constant($this->prefix . "PUB_TEST_KEY") : constant($this->prefix . "PUB_PROD_KEY");

        // Get the shop language code.
        $language = $this->_get_shop_language_code($languages_id);

        $content = "<meta name='viewport' content='width=device-width, initial-scale=1' />";
        $content .= "<script src='" . $js_static_url . "js/krypton-client/V4.0/stable/kr-payment-form.min.js'
                        kr-public-key='" . $pub_key . "'
                        kr-post-url-success='" . HTTP_SERVER . DIR_WS_CATALOG . "checkout_process_payzen.php'
                        kr-post-url-refused='" . HTTP_SERVER . DIR_WS_CATALOG . "checkout_process_payzen.php'
                        kr-language='" . $language . "'>
                    </script>\n";
        $content .= "<link rel='stylesheet' href='" . $js_static_url . "js/krypton-client/V4.0/ext/" . $theme . "-reset.css'>\n";
        $content .= "<script src='" . $js_static_url . "js/krypton-client/V4.0/ext/" . $theme . ".js'></script>\n";

        return $content;
    }

    /**
     * Return the HTML tag of the Smartform with every parameter needed to display it.
     *
     * @param $token
     * @return string
     */
    private function _generate_smartform_html($token)
    {
        $card_data_entry_mode = constant($this->prefix . "CARD_DATA_ENTRY_MODE");
        $popin = constant($this->prefix . "REST_POPIN_MODE");
        $smartform = "<div class='kr-smart-form'";

        if ($popin) {
            $smartform .= " kr-popin";
        }

        if ($card_data_entry_mode === 'MODE_SMARTFORM_EXT_WITH_LOGOS' || $card_data_entry_mode === 'MODE_SMARTFORM_EXT_WITHOUT_LOGOS') {
            $smartform .= " kr-card-form-expanded";
        }

        if ($card_data_entry_mode === 'MODE_SMARTFORM_EXT_WITHOUT_LOGOS') {
            $smartform .= " kr-no-card-logo-header";
        }

        $smartform .= " kr-form-token='" . $token . "'></div>\n";

        return $smartform;
    }

    /**
     * Return the JS code needed to configure and run Smartform.
     *
     * @return string
     */
    private function _smartform_javascript()
    {
        global $languages_id;

        // Get the shop language code.
        $language = $this->_get_shop_language_code($languages_id);

        $compact = constant($this->prefix . "REST_COMPACT_MODE");
        $threshold = constant($this->prefix . "REST_THRESHOLD");

        return <<<JSCODE
        <script>
            function _configureSmartform() {
                if ($compact) {
                    KR.setFormConfig({
                        cardForm: {layout: "compact"},
                        smartForm: {layout: "compact"}
                    });
                }

                if ('$threshold' && ! isNaN('$threshold')) {
                    KR.setFormConfig({smartForm: {groupingThreshold: '$threshold'}});
                }

                KR.setFormConfig({
                    language: '$language'
                });
            }

            _configureSmartform();
            window.addEventListener('load', function () {
                const submit_button = $("form[name='checkout_confirmation'] :submit");
                submit_button.hide();
            })
        </script>
JSCODE;
    }

    function __check_response_hash($data, $key)
    {
        global $logger;

        $supported_sign_algos = array('sha256_hmac');

        // Check if the hash algorithm is supported.
        if (! isset($data['kr-hash-algorithm']) && ! in_array($data['kr-hash-algorithm'], $supported_sign_algos)) {
            $logger->write('Hash algorithm is not supported: ' . $data['kr-hash-algorithm'], 'ERROR');

            return false;
        }

        // On some servers, / can be escaped.
        $kr_answer = str_replace('\/', '/', $data['kr-answer'] ?: '');

        $hash = hash_hmac('sha256', $kr_answer, $key);

        // Return true if calculated hash and sent hash are the same.
        return ($hash === $data['kr-hash']);
    }

    function __get_property($array, $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return null;
    }

    function __convert_rest_result($answer)
    {
        if (! is_array($answer) || empty($answer)) {
            return array();
        }

        $transactions = $this->__get_property($answer, 'transactions');
        if (! is_array($transactions) || empty($transactions)) {
            return array();
        }

        $transaction = $transactions[0];

        $response = array();

        $response['vads_result'] = $this->__get_property($transaction, 'errorCode') ?: '00';
        $response['vads_extra_result'] = $this->__get_property($transaction, 'detailedErrorCode');

        $response['vads_trans_status'] = $this->__get_property($transaction, 'detailedStatus');
        $response['vads_trans_uuid'] = $this->__get_property($transaction, 'uuid');
        $response['vads_operation_type'] = $this->__get_property($transaction, 'operationType');
        $response['vads_effective_creation_date'] = $this->__get_property($transaction, 'creationDate');
        $response['vads_payment_config'] = 'SINGLE'; // Only single payments are possible via REST API at this time.

        if ($customer = $this->__get_property($answer, 'customer')) {
            if ($billingDetails = $this->__get_property($customer, 'billingDetails')) {
                $response['vads_language'] = $this->__get_property($billingDetails, 'language');
            }
            $response['vads_cust_id'] = $this->__get_property($customer, 'reference');
        }

        $response['vads_amount'] = $this->__get_property($transaction, 'amount');
        $response['vads_currency'] = PayzenApi::getCurrencyNumCode($this->__get_property($transaction, 'currency'));

        if ($this->__get_property($transaction, 'paymentMethodToken')) {
            $response['vads_identifier'] = $this->__get_property($transaction, 'paymentMethodToken');
            $response['vads_identifier_status'] = 'CREATED';
        }

        if ($orderDetails = $this->__get_property($answer, 'orderDetails')) {
            $response['vads_order_id'] = $this->__get_property($orderDetails, 'orderId');
        }

        if ($metadata = $this->__get_property($transaction, 'metadata')) {
            foreach ($metadata as $key => $value) {
                $response['vads_ext_info_' . $key] = $value;
            }
        }

        if ($transactionDetails = $this->__get_property($transaction, 'transactionDetails')) {
            $response['vads_sequence_number'] = $this->__get_property($transactionDetails, 'sequenceNumber');

            // Workarround to adapt to REST API behavior.
            $effectiveAmount = $this->__get_property($transactionDetails, 'effectiveAmount');
            $effectiveCurrency = PayzenApi::getCurrencyNumCode($this->__get_property($transactionDetails, 'effectiveCurrency'));

            if ($effectiveAmount && $effectiveCurrency) {
                // Invert only if there is currency conversion.
                if ($effectiveCurrency !== $response['vads_currency']) {
                    $response['vads_effective_amount'] = $response['vads_amount'];
                    $response['vads_effective_currency'] = $response['vads_currency'];
                    $response['vads_amount'] = $effectiveAmount;
                    $response['vads_currency'] = $effectiveCurrency;
                } else {
                    $response['vads_effective_amount'] = $effectiveAmount;
                    $response['vads_effective_currency'] = $effectiveCurrency;
                }
            }

            $response['vads_warranty_result'] = $this->__get_property($transactionDetails, 'liabilityShift');

            if ($cardDetails = $this->__get_property($transactionDetails, 'cardDetails')) {
                $response['vads_trans_id'] = $this->__get_property($cardDetails, 'legacyTransId'); // Deprecated.
                $response['vads_presentation_date'] = $this->__get_property($cardDetails, 'expectedCaptureDate');

                $response['vads_card_brand'] = $this->__get_property($cardDetails, 'effectiveBrand');
                $response['vads_card_number'] = $this->__get_property($cardDetails, 'pan');
                $response['vads_expiry_month'] = $this->__get_property($cardDetails, 'expiryMonth');
                $response['vads_expiry_year'] = $this->__get_property($cardDetails, 'expiryYear');

                $response['vads_payment_option_code'] = $this->__get_property($cardDetails, 'installmentNumber');

                if ($authorizationResponse = $this->__get_property($cardDetails, 'authorizationResponse')) {
                    $response['vads_auth_result'] = $this->__get_property($authorizationResponse, 'authorizationResult');
                    $response['vads_authorized_amount'] = $this->__get_property($authorizationResponse, 'amount');
                }

                if (($authenticationResponse = $this->__get_property($cardDetails, 'authenticationResponse'))
                    && ($value = $this->__get_property($authenticationResponse, 'value'))) {
                    $response['vads_threeds_status'] = $this->__get_property($value, 'status');
                    $response['vads_threeds_auth_type'] = $this->__get_property($value, 'authenticationType');
                    if ($authenticationValue = $this->__get_property($value, 'authenticationValue')) {
                        $response['vads_threeds_cavv'] = $this->__get_property($authenticationValue, 'value');
                    }
                } elseif (($threeDSResponse = $this->__get_property($cardDetails, 'threeDSResponse'))
                    && ($authenticationResultData = $this->__get_property($threeDSResponse, 'authenticationResultData'))) {
                    $response['vads_threeds_cavv'] = $this->__get_property($authenticationResultData, 'cavv');
                    $response['vads_threeds_status'] = $this->__get_property($authenticationResultData, 'status');
                    $response['vads_threeds_auth_type'] = $this->__get_property($authenticationResultData, 'threeds_auth_type');
                }
            }

            if ($fraudManagement = $this->__get_property($transactionDetails, 'fraudManagement')) {
                if ($riskControl = $this->__get_property($fraudManagement, 'riskControl')) {
                    $response['vads_risk_control'] = '';

                    foreach ($riskControl as $value) {
                        $response['vads_risk_control'] .= "{$value['name']}={$value['result']};";
                    }
                }

                if ($riskAssessments = $this->__get_property($fraudManagement, 'riskAssessments')) {
                    $response['vads_risk_assessment_result'] = $this->__get_property($riskAssessments, 'results');
                }
            }
        }

        return $response;
    }

    public function _update_order_data($card_brand, $card_number, $expires, $trans_id)
    {
        global $order, $payzen_response, $payzen_order_id, $languages_id, $currencies, $order_totals, $customer_id, $sendto, $billto, $$payment;

        $order_id = is_numeric($payzen_order_id) ? (int) $payzen_order_id : $payzen_order_id;

        $new_order_status = $this->_get_new_order_status();

        tep_db_query('UPDATE `' . TABLE_ORDERS . '`' .
            " SET cc_type = '$card_brand'" .
            ", cc_number = '$card_number'" .
            ", cc_expires = '$expires'" .
            ", cc_owner = '$trans_id'" .
            ", orders_status = '$new_order_status'" .
            " WHERE orders_id = '" . $payzen_response->get('order_id') . "'");

        $sql_data_array = array(
            'orders_id' => $order_id,
            'orders_status_id' => (int) $new_order_status,
            'date_added' => 'now()',
            'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
            'comments' => $order->info['comments']
        );

        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        // Initialized for the email confirmation.
        $products_ordered = '';

        foreach ($order->products as $product) { // Stock Update.
            if (STOCK_LIMITED == 'true') {
                if (DOWNLOAD_ENABLED == 'true') {
                    $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename
                                FROM " . TABLE_PRODUCTS . " p
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                ON p.products_id=pa.products_id
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                ON pa.products_attributes_id=pad.products_attributes_id
                                WHERE p.products_id = '" . tep_get_prid($product['id']) . "'";

                    $products_attributes = isset($product['attributes']) ? $product['attributes'] : '';
                    if (is_array($products_attributes)) {
                        $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] .
                                            "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
                    }
                } else {
                    $stock_query_raw = "select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($product['id']) . "'";
                }

                $stock_query = tep_db_query($stock_query_raw);
                if (tep_db_num_rows($stock_query) > 0) {
                    $stock_values = tep_db_fetch_array($stock_query);
                    $stock_left = $stock_values['products_quantity'];
                    if ((DOWNLOAD_ENABLED != 'true') || (! $stock_values['products_attributes_filename'])) {
                        $stock_left = $stock_left - $product['qty'];
                    }

                    tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . tep_get_prid($product['id']) . "'");
                    if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
                        tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($product['id']) . "'");
                    }
                }
            }

            // Update products_ordered.
            tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $product['qty']) .
                        " where products_id = '" . tep_get_prid($product['id']) . "'");

            // Insert customer choosen option to order.
            $products_ordered_attributes = '';
            if (isset($product['attributes'])) {
                foreach ($product['attributes'] as $attribute) {
                    if (DOWNLOAD_ENABLED == 'true') {
                        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                           from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                           left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                           on pa.products_attributes_id = pad.products_attributes_id
                                           where pa.products_id = '" . $product['id'] . "'
                                           and pa.options_id = '" . $attribute['option_id'] . "'
                                           and pa.options_id = popt.products_options_id
                                           and pa.options_values_id = '" . $attribute['value_id'] . "'
                                           and pa.options_values_id = poval.products_options_values_id
                                           and popt.language_id = '" . $languages_id . "'
                                           and poval.language_id = '" . $languages_id . "'";
                    } else {
                        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix 
                                            from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                                            where pa.products_id = '" . $product['id'] . "' 
                                            and pa.options_id = '" . $attribute['option_id'] . "' 
                                            and pa.options_id = popt.products_options_id 
                                            and pa.options_values_id = '" . $attribute['value_id'] . "' 
                                            and pa.options_values_id = poval.products_options_values_id 
                                            and popt.language_id = '" . $languages_id . "' 
                                            and poval.language_id = '" . $languages_id . "'";
                    }

                    $attributes = tep_db_query($attributes_query);
                    $attributes_values = tep_db_fetch_array($attributes);

                    $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
                }
            }

            $products_ordered .= $product['qty'] . ' x ' . $product['name'] . ' (' . $product['model'] . ') = ' . $currencies->display_price($product['final_price'], $product['tax'], $product['qty']) . $products_ordered_attributes . "\n";
        }

        // Email confirmation.
        $email_order = STORE_NAME . "\n" .
            EMAIL_SEPARATOR . "\n" .
            EMAIL_TEXT_ORDER_NUMBER . ' ' . $order_id . "\n" .
            EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $order_id, 'SSL', false) . "\n" .
            EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

        if ($order->info['comments']) {
            $email_order .= tep_db_output($order->info['comments']) . "\n\n";
        }

        $email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
            EMAIL_SEPARATOR . "\n" .
            $products_ordered .
            EMAIL_SEPARATOR . "\n";

        foreach ($order_totals as $total) {
            $email_order .= strip_tags($total['title']) . ' ' . strip_tags($total['text']) . "\n";
        }

        if ($order->content_type != 'virtual') {
            $email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                EMAIL_SEPARATOR . "\n" .
                tep_address_label($customer_id, $sendto, 0, '', "\n") . "\n";
        }

        $email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
            EMAIL_SEPARATOR . "\n" .
            tep_address_label($customer_id, $billto, 0, '', "\n") . "\n\n";

        if (is_object($$payment)) {
            $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
                EMAIL_SEPARATOR . "\n";
            $payment_class = $$payment;
            $email_order .= $payment_class->title . "\n\n";
            if ($payment_class->email_footer) {
                $email_order .= $payment_class->email_footer . "\n\n";
            }
        }

        tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        // Send emails to other people.
        if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
            tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
    }

    public function _get_rest_key($request)
    {
        if (constant($this->prefix . 'CTX_MODE') === 'TEST') {
            $rest_key = $request["kr-hash-key"] === "sha256_hmac" ? constant($this->prefix . 'HMAC_TEST_KEY') : constant($this->prefix . 'PRIV_TEST_KEY');
        } else {
            $rest_key = $request["kr-hash-key"] === "sha256_hmac" ? constant($this->prefix . 'HMAC_PROD_KEY') : constant($this->prefix . 'PRIV_PROD_KEY');
        }

        return $rest_key;
    }

    private function _install_payzen_status()
    {
        global $payzen_statuses;

        foreach ($payzen_statuses as $status_name) {
            $check_query = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = '" . $status_name . "' limit 1");

            if (tep_db_num_rows($check_query) < 1) {
                $status_query = tep_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
                $status = tep_db_fetch_array($status_query);

                $status_id = $status['status_id'] + 1;

                $languages = tep_get_languages();

                foreach ($languages as $lang) {
                    tep_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', '" . $status_name . "')");
                }

                $flags_query = tep_db_query("describe " . TABLE_ORDERS_STATUS . " public_flag");
                if (tep_db_num_rows($flags_query) == 1) {
                    tep_db_query("update " . TABLE_ORDERS_STATUS . " set public_flag = 0 and downloads_flag = 0 where orders_status_id = '" . $status_id . "'");
                }
            }
        }
    }

    private function _get_new_order_status()
    {
        global $payzen_response, $payzen_statuses;

        if($payzen_response->isAcceptedPayment()) {
            $new_status = "Pending";
            if (! $payzen_response->isPendingPayment()) {
                return constant($this->prefix . 'ORDER_STATUS');
            }
        } else {
            $new_status = $payzen_statuses['PAYZEN_FAILED'];
            if ($payzen_response->isCancelledPayment()) {
                $new_status = $payzen_statuses['PAYZEN_CANCELLED'];
            }
        }

        $status_query = tep_db_query("select orders_status_id as status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = '" . $new_status . "' limit 1");
        $status = tep_db_fetch_array($status_query);

        return $status['status_id'];
    }

    private function _update_order_status()
    {
        global $payzen_response;

        $new_order_status = (int) $this->_get_new_order_status();
        $order_id = (int) $payzen_response->get('order_id');

        tep_db_query("UPDATE " . TABLE_ORDERS .
            " SET orders_status = " . $new_order_status .
            " WHERE orders_id = " . $order_id);

        $sql_data_array = array(
            'orders_id' => $order_id,
            'orders_status_id' => $new_order_status,
            'date_added' => 'now()',
            'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0'
        );

        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    }
}