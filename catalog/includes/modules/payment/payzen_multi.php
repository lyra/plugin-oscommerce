<?php
/**
 * PayZen V2-Payment Module version 1.2.0 for osCommerce 2.3.x. Support contact : support@payzen.eu.
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
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2017 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 * @category  payment
 * @package   payzen
 */

/**
 * Main class implementing PayZen multiple payment module for osCommerce.
 */
require_once (DIR_FS_CATALOG . 'includes/modules/payment/payzen.php');

/* load module language file */
require_once (DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen_multi.php");

class payzen_multi extends payzen
{
    var $prefix = 'MODULE_PAYMENT_PAYZEN_MULTI_';

    /**
     * Class constructor.
     */
    function payzen_multi()
    {
        parent::payzen();

        // initialize code
        $this->code = 'payzen_multi';

        // initialize title
        $this->title = MODULE_PAYMENT_PAYZEN_MULTI_TITLE;
    }

    /**
     * Payment zone and amount restriction checks.
     */
    function update_status()
    {
        parent::update_status();

        if (! $this->enabled) {
            return;
        }

        // check multi payment options
        $options = $this->get_available_options();
        if (empty($options)) {
            $this->enabled = false;
        }
    }

    function get_available_options()
    {
        global $order;

        $amount = $order->info['total'];

        $options = MODULE_PAYMENT_PAYZEN_MULTI_OPTIONS ?
                    json_decode(MODULE_PAYMENT_PAYZEN_MULTI_OPTIONS, true) :
                    array();

        $availOptions = array();
        if (is_array($options) && ! empty($options)) {
            foreach ($options as $code => $option) {
                if (empty($option)) {
                    continue;
                }

                if ((! $option['min_amount'] || $amount >= $option['min_amount'])
                && (! $option['max_amount'] || $amount <= $option['max_amount'])) {
                    // option will be available
                    $availOptions[$code] = $option;
                }
            }
        }

        return $availOptions;
    }

    /**
     * Parameters for what the payment option will look like in the list
     * @return array
     */
    function selection()
    {
        global $order;

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
                'field' => '<input type="radio" id="payzen_option_' . $code . '" name="payzen_option" value="'.$code.'" onclick="$(\'input[name=payment][value=payzen_multi]\').click();" style="vertical-align: middle; margin-top: 0;"' . $checked . '>' .
                           '<label for="payzen_option_' . $code . '">' . $option['label'] . '</label>'
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
        global $order, $languages_id, $currencies, $customer_id;

        $data = $this->_build_request();

        // set multi payment options
        $options = $this->get_available_options();
        $option = $options[$_POST['payzen_option']];

        $first = (key_exists('first', $option) && $option['first'] != '') ?
                (int) (string) (($option['first'] / 100) * $data['amount']) : // amount is in cents
                null;

        // override cb contract
        $data['contracts'] = $option['contract'] ? 'CB=' . $option['contract'] : null;

        require_once (DIR_FS_CATALOG . 'includes/classes/payzen_request.php');
        $request = new PayzenRequest(CHARSET);

        $request->setFromArray($data);
        $request->setMultiPayment(null /* use already set amount */, $first, $option['count'], $option['period']);

        return $request->getRequestHtmlFields();
    }

    /**
     * Module install (register admin-managed parameters in database)
     */
    function install()
    {
        parent::install();

        // multi-payment parameters
        $this->_install_query('OPTIONS', '', 35, 'payzen_cfg_draw_table_multi_options(', 'payzen_get_multi_options');
    }

    /**
     * Returns the names of module's parameters.
     * @return array[int]string
     */
    function keys()
    {
        return array(
            'MODULE_PAYMENT_PAYZEN_MULTI_STATUS',
            'MODULE_PAYMENT_PAYZEN_MULTI_SORT_ORDER',
            'MODULE_PAYMENT_PAYZEN_MULTI_ZONE',

            'MODULE_PAYMENT_PAYZEN_MULTI_SITE_ID',
            'MODULE_PAYMENT_PAYZEN_MULTI_KEY_TEST',
            'MODULE_PAYMENT_PAYZEN_MULTI_KEY_PROD',
            'MODULE_PAYMENT_PAYZEN_MULTI_CTX_MODE',
            'MODULE_PAYMENT_PAYZEN_MULTI_PLATFORM_URL',

            'MODULE_PAYMENT_PAYZEN_MULTI_LANGUAGE',
            'MODULE_PAYMENT_PAYZEN_MULTI_AVAILABLE_LANGUAGES',
            'MODULE_PAYMENT_PAYZEN_MULTI_CAPTURE_DELAY',
            'MODULE_PAYMENT_PAYZEN_MULTI_VALIDATION_MODE',
            'MODULE_PAYMENT_PAYZEN_MULTI_PAYMENT_CARDS',
            'MODULE_PAYMENT_PAYZEN_MULTI_3DS_MIN_AMOUNT',

            'MODULE_PAYMENT_PAYZEN_MULTI_AMOUNT_MIN',
            'MODULE_PAYMENT_PAYZEN_MULTI_AMOUNT_MAX',

            'MODULE_PAYMENT_PAYZEN_MULTI_OPTIONS',

            'MODULE_PAYMENT_PAYZEN_MULTI_REDIRECT_ENABLED',
            'MODULE_PAYMENT_PAYZEN_MULTI_REDIRECT_SUCCESS_TIMEOUT',
            'MODULE_PAYMENT_PAYZEN_MULTI_REDIRECT_SUCCESS_MESSAGE',
            'MODULE_PAYMENT_PAYZEN_MULTI_REDIRECT_ERROR_TIMEOUT',
            'MODULE_PAYMENT_PAYZEN_MULTI_REDIRECT_ERROR_MESSAGE',
            'MODULE_PAYMENT_PAYZEN_MULTI_RETURN_MODE',
            'MODULE_PAYMENT_PAYZEN_MULTI_ORDER_STATUS'
        );
    }
}
