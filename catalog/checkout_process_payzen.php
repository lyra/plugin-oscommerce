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
 * This file is an access point to the payment gateway plugin to validate an order.
 */
use Lyranetwork\Payzen\Sdk\Form\Response as PayzenResponse;

function _restore_session()
{
    $osCsid = '';
    if (isset($_POST['vads_ext_info_session_id'])) {
        $osCsid = $_POST['vads_ext_info_session_id'];
    } else {
        $answer = json_decode($_POST['kr-answer'], true);
        if (is_array($answer) && isset($answer['transactions']) && ! empty($answer['transactions'])) {
            $transaction = $answer['transactions'][0];

            $osCsid = isset($transaction['metadata']) && isset($transaction['metadata']['session_id']) ? $transaction['metadata']['session_id'] : '';
        }
    }

    $_POST['osCsid'] = $osCsid;
    $_GET['osCsid'] = $osCsid;

    // For cookie based sessions.
    $_COOKIE['osCsid'] = $osCsid;
    $_COOKIE['cookie_test'] = 'please_accept_for_session';
}

function _check_form_response_validity()
{
    return isset($_REQUEST['vads_order_id']) && isset($_REQUEST['vads_ext_info_session_id']);
}

function _check_rest_response_validity()
{
    return isset($_POST['kr-hash']) && isset($_POST['kr-hash-algorithm']) && isset($_POST['kr-answer']);
}

global $messageStack, $logger;
if (! isset($logger)) {
    require_once("./admin/includes/classes/logger.php");
    $logger = new logger();
}

$from_server = isset($_POST['vads_hash']);
$from_rest_server = isset($_POST["kr-hash-key"]) && ($_POST["kr-hash-key"] !== "sha256_hmac");
$header_error_500 = 'HTTP/1.1 500 Internal Server Error';

if (_check_form_response_validity() || _check_rest_response_validity()) {
    if ($from_server || $from_rest_server) { // Restore session if this is an IPN call.
        _restore_session();

        require_once('checkout_process.php');
    } else {
        global $payzen_response, $language;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Restore session if return mode is POST.
            _restore_session();
        }

        require_once('includes/application_top.php');
        require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen.php');
        $payment_object = new payzen();

        $request = $_REQUEST;
        if (isset($request['kr-hash'])) {
            $request = $payment_object->__convert_rest_result(json_decode($request['kr-answer'], true));
        }

        $payment_method = $request['vads_ext_info_payment_method'];
        switch ($payment_method) {
            case 'payzen':
                break;

            case 'payzen_multi':
                require_once(DIR_FS_CATALOG . 'includes/modules/payment/payzen_multi.php');
                $payment_object = new payzen_multi();
                break;

            default:
                require_once(DIR_FS_CATALOG . "includes/languages/$language/modules/payment/payzen.php");
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
                break;
        }

        $payzen_response = new PayzenResponse(
            array_map('stripslashes', $request),
            constant($payment_object->prefix . 'CTX_MODE'),
            constant($payment_object->prefix . 'KEY_TEST'),
            constant($payment_object->prefix . 'KEY_PROD'),
            constant($payment_object->prefix . 'SIGN_ALGO')
        );

        // Check authenticity.
        if (! $payzen_response->isAuthentified() && ! $payment_object->__check_response_hash($_REQUEST, $payment_object->_get_rest_key($_REQUEST))) {
            $logger->write("Tries to access checkout_process_payzen.php page without valid signature with data: " . print_r(request, true), 'ERROR');
            $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
        }

        if ($payment_object->_is_order_paid()) {
            $logger->write("Order #" . $payzen_response->get('order_id') . " is already saved.", 'INFO');

            global $payzen_plugin_features;

            // Messages to display on payment result page.
            if ($payzen_plugin_features['prodfaq'] && (constant($payment_object->prefix . 'CTX_MODE') == 'TEST')) {
                $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO, 'success');
            }

            $logger->write('RETURN URL PROCESS END', 'INFO');

            tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true));
        } else {
            $return_mode = '_' . (isset($_POST['kr-hash']) ? 'POST' : constant($payment_object->prefix . 'RETURN_MODE'));
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
} else {
    $logger->write('Invalid response received. Content: ' . print_r($_REQUEST, true), 'ERROR');

    if ($from_server || $from_rest_server) {
        $logger->write('IPN URL PROCESS END', 'INFO');

        header($header_error_500, true, 500);
        die('<span style="display:none">KO-Invalid IPN request received.' . "\n" . '</span>');
    } else {
        $messageStack->add_session('header', MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR, 'error');

        $logger->write('RETURN URL PROCESS END', 'INFO');

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
    }
}