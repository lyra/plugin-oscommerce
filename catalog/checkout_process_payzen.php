<?php
/**
 * PayZen V2-Payment Module version 1.3.0 for osCommerce 2.3.x. Support contact : support@payzen.eu.
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
 * @category  Payment
 * @package   Payzen
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2018 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 */

/**
 * This file is an access point for the PayZen payment gateway to validate an order.
 */

if (key_exists('vads_hash', $_POST) && isset($_POST['vads_hash']) && key_exists('vads_result', $_POST) && isset($_POST['vads_result'])) {
    // restore session if this is an IPN call.

    $osCsid = substr($_POST['vads_order_info'], strlen('session_id='));
    $_POST['osCsid'] = $osCsid;
    $_GET['osCsid'] = $osCsid;

    // for cookie based sessions ...
    $_COOKIE['osCsid'] = $osCsid;
    $_COOKIE['cookie_test'] = 'please_accept_for_session';

    require_once('checkout_process.php');
} else {
    require_once('includes/application_top.php');

    global $payzen_response, $language, $messageStack;

    $paymentMethod = str_replace('payment_method=', '', $_REQUEST['vads_order_info2']);

    switch ($paymentMethod) {
        case 'payzen':
            require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen.php');
            $paymentObject = new payzen();
            break;

        case 'payzen_multi':
            require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen_multi.php');
            $paymentObject = new payzen_multi();
            break;

        case 'payzen_choozeo':
            require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen_choozeo.php');
            $paymentObject = new payzen_choozeo();
            break;

        default:
            require_once(DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen.php");
            $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
            break;
    }

    require_once(DIR_FS_CATALOG . 'includes/classes/payzen_response.php');
    $payzen_response = new PayzenResponse(
        array_map('stripslashes', $_REQUEST),
        constant($paymentObject->prefix . 'CTX_MODE'),
        @constant($paymentObject->prefix . 'KEY_TEST'),
        constant($paymentObject->prefix . 'KEY_PROD'),
        constant($paymentObject->prefix . 'SIGN_ALGO')
    );

    $from_server = $payzen_response->get('hash') != null;

    // check authenticity
    if (! $payzen_response->isAuthentified()) {
        $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
    }

    if ($paymentObject->_is_order_paid()) {
        global $payzen_plugin_features;

        // messages to display on payment result page
        if ($payzen_plugin_features['prodfaq'] && (constant($paymentObject->prefix . 'CTX_MODE') == 'TEST')) {
            $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
    } else {
        $return_mode = '_' . ($from_server ? 'POST' : constant($paymentObject->prefix . 'RETURN_MODE'));

        if ($return_mode == '_POST') {
            $action = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true);
            $fields = '';

            foreach ($$return_mode as $key => $value) {
                $fields .= '<input type="hidden" name="' . $key . '" value="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '" />' . "\n";
            }

            echo <<<EOT
                <html>
                    <body>
                        <form action="$action" method="POST" name="checkout_process_payzen_form">
                            $fields
                        </form>

                        <script type="text/javascript">
                            window.onload = function() {
                                document.checkout_process_payzen_form.submit();
                            };
                        </script>
                    </body>
                </html>
EOT;
        } else {
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PROCESS, http_build_query($$return_mode), 'SSL', true));
        }
    }
}
