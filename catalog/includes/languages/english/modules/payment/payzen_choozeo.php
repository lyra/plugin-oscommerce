<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for osCommerce. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra-network.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

// administration interface - amount restriction error message
define('MODULE_PAYMENT_PAYZEN_NULL_MIN_AMOUNT_ERROR', "The field « Minimum amount » is mandatory.");
define('MODULE_PAYMENT_PAYZEN_NULL_MAX_AMOUNT_ERROR', "The field « Maximum amount » is mandatory.");
define('MODULE_PAYMENT_PAYZEN_INCONSISTENT_AMOUNT_ERROR', "« Minimum amount » must be inferior to « Maximum amount ».");
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS_ERROR', "Min and max amounts of payment options must fall within the interval defined by fields « Minimum amount » and « Maximum amount ».");

// administration interface - choozeo payment options
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Payment options");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Définir la restriction sur le montant pour chaque carte.");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Label");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Min amount");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Max amount");

// choozeo payment catalog messages
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_TITLE', "PayZen - Payment with Choozeo");
