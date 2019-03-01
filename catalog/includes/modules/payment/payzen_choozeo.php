<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for osCommerce. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra-network.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

/**
 * Main class implementing Choozeo payment module for osCommerce.
 */
require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen.php');

global $payzen_plugin_features;

if ($payzen_plugin_features['choozeo']) {
    global $language;

    /* load module language file */
    require_once(DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen_choozeo.php");

    class payzen_choozeo extends payzen
    {
        var $prefix = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_';

        /**
         * Class constructor.
         */
        function payzen_choozeo()
        {
            parent::payzen();

            // initialize code
            $this->code = 'payzen_choozeo';

            // initialize title
            $this->title = MODULE_PAYMENT_PAYZEN_CHOOZEO_TITLE;

            // check amount restriction
            if ($this->enabled === true) {
                $error = '';

                // field « Minimum amount » cannot be null
                if (! defined('MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT') || ! tep_not_null(MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT)) {
                    $error .= '<div class="secWarning">' . MODULE_PAYMENT_PAYZEN_NULL_MIN_AMOUNT_ERROR . '</div>';
                }

                // field « Minimum amount » cannot be null
                if (! defined('MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT') || ! tep_not_null(MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT)) {
                    $error .= '<div class="secWarning">' . MODULE_PAYMENT_PAYZEN_NULL_MAX_AMOUNT_ERROR . '</div>';
                }

                if (tep_not_null(MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT) && tep_not_null(MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT)
                    && MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT > MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT) {
                    $error .= '<div class="secWarning">' . MODULE_PAYMENT_PAYZEN_INCONSISTENT_AMOUNT_ERROR . '</div>';
                }

                if (! empty($error)) {
                    $this->enabled = false;
                }

                $options = MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS ? json_decode(MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS, true) : array();

                // min and max amounts for « Payment options » must fall within the interval defined by « Minimum amount » and « Maximum amount »
                foreach ($options as $option) {
                    if ((! empty($option['min_amount']) && ($option['min_amount'] < MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT))
                        || (! empty($option['max_amount']) && ($option['max_amount'] > MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT))) {
                        $error .= '<div class="secWarning"><b>' . $option['label'] . '</b> : ' . MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS_ERROR . '</div>';
                    }
                }

                $this->description = $error . $this->description;
            }
        }

        function update_status()
        {
            global $order;

            parent::update_status();

            if (! $this->enabled) {
                return;
            }

            // check billing country
            $country = $order->billing['country']['iso_code_2'];
            if (! in_array($country, array('FR', 'FX', 'GP', 'MQ', 'GF', 'RE', 'YT'))) {
                $this->enabled = false;
                return;
            }

            // check Choozeo payment options
            $options = $this->get_available_options();
            if (empty($options)) {
                $this->enabled = false;
                return;
            }

            if ($order->info['currency'] != 'EUR') {
                // Choozeo supports only EURO, module is not available
                $this->enabled = false;
            }
        }

        function get_available_options()
        {
            global $order;

            $amount = $order->info['total'];

            $options = MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS ?
                json_decode(MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS, true) : array();

            $avail_options = array();
            foreach ($options as $code => $option) {
                if ((! $option['min_amount'] || $amount >= $option['min_amount'])
                    && (! $option['max_amount'] || $amount <= $option['max_amount'])) {
                    // option will be available
                    $avail_options[$code] = $option;
                }
            }

            return $avail_options;
        }

        /**
         * Parameters for what the payment option will look like in the list
         * @return array
         */
        function selection()
        {
            $selection = array(
                'id' => $this->code,
                'module' => $this->title
            );

            $first = true;
            foreach ($this->get_available_options() as $code => $option) {
                $checked = '';
                if ($first) {
                    $checked = ' checked="checked"';
                    $first = false;
                }

                $selection['fields'][] = array(
                    'title' => '',
                    'field' => '<input type="radio"
                                       id="payzen_choozeo_option_' . $code . '"
                                       name="payzen_choozeo_option"
                                       value="' . $code . '"
                                       onclick="$(\'input[name=payment][value=payzen_choozeo]\').click();"
                                       style="vertical-align: middle; margin-top: 0;"' . $checked . '>
                                <label for="payzen_choozeo_option_' . $code . '">' . $option['label'] . '</label>'
                );
            }

            return $selection;
        }

        /**
         * Prepare the form that will be sent to the payment gateway
         * @return string
         */
        function process_button()
        {
            $data = $this->_build_request();

            // override with Choozeo payment card
            $data['payment_cards'] = tep_output_string($_POST['payzen_choozeo_option']);

            // by default osCommerce does not manage customer type
            $data['cust_status'] = 'PRIVATE';

            // send FR even customer is from DOM
            $data['cust_country'] = 'FR';

            // Choozeo supports only automatic validation
            $data['validation_mode'] = '0';

            require_once(DIR_FS_CATALOG . 'includes/classes/payzen_request.php');
            $request = new PayzenRequest();
            $request->setFromArray($data);

            return $request->getRequestHtmlFields();
        }

        /**
         * Module install (register admin-managed parameters in database)
         */
        function install()
        {
            parent::install();

            // choozeo-payment parameters
            $this->_install_query('MIN_AMOUNT', '135', 30);
            $this->_install_query('MAX_AMOUNT', '2000', 31);
            $this->_install_query('OPTIONS', '', 35, 'payzen_cfg_draw_table_choozeo_options(', 'payzen_get_choozeo_options');
        }

        /**
         * Returns the names of module's parameters.
         * @return array[int]string
         */
        function keys()
        {
            global $payzen_plugin_features;

            $keys = array();

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_STATUS';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_SORT_ORDER';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_ZONE';

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_SITE_ID';

            if (! $payzen_plugin_features['qualif']) {
                $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_KEY_TEST';
            }

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_KEY_PROD';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_CTX_MODE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_SIGN_ALGO';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_PLATFORM_URL';

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_LANGUAGE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_AVAILABLE_LANGUAGES';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_CAPTURE_DELAY';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_3DS_MIN_AMOUNT';

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_MIN_AMOUNT';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_MAX_AMOUNT';

            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_REDIRECT_ENABLED';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_REDIRECT_SUCCESS_TIMEOUT';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_REDIRECT_SUCCESS_MESSAGE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_REDIRECT_ERROR_TIMEOUT';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_REDIRECT_ERROR_MESSAGE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_RETURN_MODE';
            $keys[] = 'MODULE_PAYMENT_PAYZEN_CHOOZEO_ORDER_STATUS';

            return $keys;
        }
    }
}
